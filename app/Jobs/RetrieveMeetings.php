<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Models\Setting;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RetrieveMeetings implements ShouldQueue, ShouldBeUniqueUntilProcessing
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

        $from = $this->timestamp->toIso8601ZuluString();
        $to = $this->timestamp->addSeconds(86400)->toIso8601ZuluString();
        $azure_oauth = Setting::where('key', '=', 'azure_oauth_identity')
            ->latest()
            ->first();
        $azure_access_token = decrypt($azure_oauth->value['access_token']);
        $azure_api_base_url = env('GRAPH_API_URL');
        $azure_api_resource = '/users/' . $this->calendarAccount . '/calendarview?' .
            '$select=id,subject,start,end,location,attendees,organizer' .
            '&startdatetime=' . $from . '&enddatetime=' . $to;
        $client = Http::withToken($azure_access_token)->baseUrl($azure_api_base_url);
        $local_ids = Meeting::select('id')->pluck('id')->toArray();
        $link = null;

        do {
            $response = $client->get($azure_api_resource);
            if ($response->successful()) {
                $date = $response->header('date');
                $link = $response['@odata.nextLink'] ?? null;
                $timestamp = Carbon::createFromFormat(DateTimeInterface::RFC7231, $date);
                $upsert_meetings = array_filter($response['value'], function ($item) {
                    return array_key_exists('uniqueId', $item['location']);
                });
                $delete_meetings = array_filter($response['value'], function ($item) {
                    return false; //TODO
                });
                $delete_meeting_ids = array_map(function ($item) {
                    return $item['id'];
                }, $delete_meetings);

                Meeting::destroy(array_intersect($delete_meeting_ids, $local_ids));

                $meetings = array_map(function ($meeting) use ($timestamp) {
                    return [
                        'calender_id' => $meeting['id'],
                        'user_email' => $this->calendarAccount,
                        'subject' => array_key_exists('subject', $meeting) ?
                            Str::limit($meeting['subject'], 1024) : '',
                        'start' => Carbon::createFromFormat(DateTimeInterface::ISO8601,
                            Str::replace('.0000000', '', $meeting['start']['dateTime']) . 'Z'),
                        'end' => Carbon::createFromFormat(DateTimeInterface::ISO8601,
                            Str::replace('.0000000', '', $meeting['end']['dateTime']) . 'Z'),
                        'link' => $meeting['location']['uniqueId'],
                        'attendees' => json_encode($meeting['attendees']),
                        'organizer' => json_encode($meeting['organizer']),
                        'synced_at' => $timestamp
                    ];
                }, $upsert_meetings);

                Meeting::upsert($meetings, ['calender_id', 'user_email'], [
                    'subject',
                    'start',
                    'end',
                    'link',
                    'attendees',
                    'organizer'
                ]);

                if ($link) {
                    $azure_api_resource = Str::after($link, $azure_api_base_url);
                }
            }
        } while ($response->successful() && $link);
    }
}
