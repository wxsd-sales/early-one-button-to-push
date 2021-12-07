<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class PerformBookingsDelete implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Carbon
     */
    private $timestamp;

    /**
     * @var string
     */
    private $calendarAccount;

    /**
     * @var string
     */
    private $deviceId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $calendarAccount, string $deviceId)
    {
        $this->timestamp = now();
        $this->calendarAccount = $calendarAccount;
        $this->deviceId = $deviceId;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->deviceId;
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
        $webex_api_resource = '/xapi/command/Bookings.Put';
        $client = Http::withToken($webex_access_token)->baseUrl($webex_api_base_url);

        $client->post($webex_api_resource, [
            'deviceId' => $this->deviceId,
            'body' => [
                'Bookings' => [[
                    'Id' => '0',
                    'MeetingId' => '0',
                    'Number' => '',
                    'Organizer' => [
                        'Name' => ''
                    ],
                    'Protocol' => 'Spark',
                    'Time' => [
                        'Duration' => 0,
                        'EndTimeBuffer' => 0,
                        'StartTime' => '1970-01-01T00:00:00Z',
                        'StartTimeBuffer' => 0
                    ],
                    'Title' => ''
                ]]
            ]
        ]);
    }
}

// TODO: Optimize number of deletes.
