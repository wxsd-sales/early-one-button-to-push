<?php

namespace App\Http\Controllers;

use App\Jobs\PerformBookingsPut;
use App\Jobs\RefreshAzureToken;
use App\Jobs\RetrieveDevices;
use App\Jobs\RetrieveMeetings;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function refreshAzureToken()
    {
        Log::info('[JobsController] refreshAzureToken');
        try {
            RefreshAzureToken::dispatchSync();
        } catch (Exception $e) {
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }

    public function retrieveDevices()
    {
        Log::info('[JobsController] retrieveDevices');
        try {
            RetrieveDevices::dispatchSync();
        } catch (Exception $e) {
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }

    public function retrieveMeetings(Request $request)
    {
        Log::info('[JobsController] retrieveMeetings');
        try {
            $email = $request->get('email');

            if ($email) {
                RetrieveMeetings::dispatchSync($email);
            } else {
                $emails = User::orderBy('email')
                    ->pluck('email')
                    ->toArray();

                foreach ($emails as $email) {
                    RetrieveMeetings::dispatchSync($email);
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }

    public function performBookingsPut(Request $request)
    {
        Log::info('[JobsController] performBookingsPut');
        try {
            $email = $request->get('email');

            if ($email) {
                PerformBookingsPut::dispatchSync($email);
            } else {
                $emails = User::orderBy('email')
                    ->pluck('email')
                    ->toArray();

                foreach ($emails as $email) {
                    PerformBookingsPut::dispatchSync($email);
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }

}
