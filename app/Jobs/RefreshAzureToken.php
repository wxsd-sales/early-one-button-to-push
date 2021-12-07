<?php

namespace App\Jobs;

use App\Http\Controllers\Auth\RegisterController;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RefreshAzureToken implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $auth_url;

    /**
     * @var float|int|string
     */
    private $timestamp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->auth_url = 'https://login.microsoftonline.com/' .
            config('services.azure.tenant') .
            '/oauth2/v2.0/token';
        $this->timestamp = now()->timestamp;
    }

    public function uniqueId()
    {
        return $this->timestamp;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function handle()
    {
        $azure_oauth = Setting::where('key', '=', 'azure_oauth_identity')
            ->latest()
            ->first();

        if ($azure_oauth->exists()) {
            $auth_response = Http::asForm()
                ->retry(5, 1000)
                ->post($this->auth_url, [
                    'client_id' => config('services.azure.client_id'),
                    'scope' => implode(" ", RegisterController::$azureScopes),
                    'refresh_token' => decrypt($azure_oauth->value['refresh_token']),
                    'redirect_uri' => url(config('services.azure.redirect')),
                    'grant_type' => 'refresh_token',
                    'client_secret' => config('services.azure.client_secret')
                ]);

            Log::error($auth_response->body());
            $auth_response->throw();

            $azure_oauth['value->refresh_token'] = encrypt($auth_response['refresh_token']);
            $azure_oauth['value->expires_at'] = $this->timestamp + $auth_response['expires_in'];
            $azure_oauth['value->access_token'] = encrypt($auth_response['access_token']);

            $azure_oauth->save();
        }
    }
}
