<?php

namespace App\Jobs;

use App\Models\Device;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RetrieveDevices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $webex_access_token = env('WEBEX_BOT_TOKEN');
        $webex_api_base_url = env('WEBEX_API_URL');
        $webex_api_resource = '/devices';
        $client = Http::withToken($webex_access_token)->baseUrl($webex_api_base_url);
        $local_webex_device_ids = Device::select('id')->pluck('id')->toArray();
        $remote_ids = [];
        $link = '';

        do {
            $response = $client->get($webex_api_resource);
            if ($response->successful()) {
                $date = $response->header('date');
                $link = $response->header('link');
                $timestamp = Carbon::createFromFormat(DateTimeInterface::RFC7231, $date);
                $device_items = $response['items'] ?? [];

                $remote_ids = array_merge($remote_ids, array_column($device_items, 'id'));
                $devices = array_map(function ($device_item) use ($timestamp) {
                    return [
                        'id' => $device_item['id'],
                        'place_id' => $device_item['placeId'],
                        'product' => $device_item['product'],
                        'mac' => $device_item['mac'],
                        'serial' => $device_item['serial'],
                        'primary_sip_url' => $device_item['primarySipUrl'],
                        'synced_at' => $timestamp
                    ];
                }, $device_items);

                Device::upsert($devices, ['id'], [
                    'place_id', 'product', 'mac', 'serial', 'primary_sip_url'
                ]);

                if ($link) {
                    $webex_api_resource = Str::between($link, "<$webex_api_base_url", '>;');
                }
            }
        } while ($response->successful() && $link !== '');

        Device::destroy(array_diff($local_webex_device_ids, $remote_ids));
    }
}
