<?php

namespace App\Http\Controllers;

use App\Jobs\PerformBookingsDelete;
use App\Models\Device;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('dashboard');
    }

    public function getDevices()
    {
        return Device::orderBy('serial')
            ->get()
            ->toArray();
    }

    public function getUnmappedDevices()
    {
        return Device::whereNull('user_email')
            ->orderBy('serial')
            ->get()
            ->toArray();
    }

    public function postAddMapping(Request $request)
    {
        $request->validate([
            'deviceId' => ['required', 'string', 'exists:devices,id'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        function upsertDevice(string $id, string $email)
        {
            return Device::updateOrCreate(['id' => $id], [
                'user_email' => $email
            ]);
        }

        function upsertUser(string $email)
        {
            return User::firstOrCreate(['email' => $email]);
        }

        return DB::transaction(function () use ($request) {
            upsertUser($request->get('email'));

            return upsertDevice(
                $request->get('deviceId'),
                $request->get('email')
            )
                ->toArray();
        });
    }

    public function postRemoveMapping(Request $request)
    {
        try {
            $request->validate([
                'deviceId' => ['required', 'string', 'exists:devices,id'],
                'email' => ['required', 'string', 'email', 'max:255', 'exists:users'],
            ]);

            function upsertDevice(string $id)
            {
                return Device::where('id', $id)->update(['user_email' => null]);
            }

            function deleteUser(string $email)
            {
                return User::where('email', $email)->delete();
            }

            DB::transaction(function () use ($request) {
                $device_count = Device::where('user_email', $request->get('email'))->count();

                if ($device_count <= 1 && Auth::user()['email'] !== $request->get('email')) {
                    deleteUser($request->get('email'));
                }

                upsertDevice(
                    $request->get('deviceId'),
                    $request->get('email')
                );
            });

            PerformBookingsDelete::dispatchSync($request->get('email'), $request->get('deviceId'));
        } catch (Exception $e) {
            return response()->json(['status' => 'error'], 400);
        }

        return response()->json(['status' => 'success']);
    }
}
