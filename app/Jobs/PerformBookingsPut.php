<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PerformBookingsPut implements ShouldQueue, ShouldBeUniqueUntilProcessing
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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $calendarAccount)
    {
        $this->timestamp = now();
        $this->calendarAccount = $calendarAccount;
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->calendarAccount;
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
        $device_ids = Device::where('user_email', '=', $this->calendarAccount)->pluck('id')->toArray();
        $meetings = Meeting::where('user_email', '=', $this->calendarAccount)->get();
        $bookings = $meetings->map(function ($meeting) {
            $duration = $meeting['end']->timestamp - $meeting['start']->timestamp;

            return [
                'Id' => Str::of($meeting['id']),
                'MeetingId' => Str::of($meeting['id']),
                'Number' => Str::of($meeting['link'])->remove('@webex',)->trim(),
                'Organizer' => [
                    'Name' => Arr::get($meeting, 'organizer.emailAddress.name', '')
                ],
                'Protocol' => 'Spark',
                'Time' => [
                    'Duration' => ceil($duration / 60),
                    'StartTime' => $meeting['start'],
                    'StartTimeBuffer' => 1800
                ],
                'Title' => $meeting['subject']
            ];
        }, $meetings);

        foreach ($device_ids as $device_id) {
            $client->post($webex_api_resource, [
                'deviceId' => $device_id,
                'body' => [
                    'Bookings' => $bookings
                ]
            ]);
        }
    }
}

// TODO: Optimize number of puts.
