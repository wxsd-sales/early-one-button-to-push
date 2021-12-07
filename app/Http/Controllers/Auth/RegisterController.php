<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * @var string[]
     */
    public static $azureScopes = ['.default', 'offline_access'];
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @return Application|Factory|View
     */
    public function showRegistrationForm()
    {
        return view('auth.setup', ['url' => [
            'setup' => route('setup', [], false),
            'login' => route('login', [], false),
            'reset' => route('reset', [], false),
            'azure' => route('auth.azure', [], false),
        ]]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function azureOauthRedirect(Request $request)
    {
        return Socialite::driver('azure')
            ->setScopes(self::$azureScopes)
            ->redirect();
    }

    /**
     * @param Request $request
     * @return Application|JsonResponse|RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function azureOauthCallback(Request $request)
    {
        $timestamp = now()->timestamp;
        $azure_provider = Socialite::driver('azure');

        $azure_callback_validation = $this->oauthCallbackValidator($azure_provider);
        if ($azure_callback_validation->fails()) {
            $message = "Could not retrieve Azure OAuth access code.";
            Log::info($message);

            return redirect()
                ->route('setup')
                ->withErrors(['azure' => $message]);
        }

        $validated_azure_callback = $azure_callback_validation->validated();
        $azure_oauth_identity = [
            'access_token' => encrypt($validated_azure_callback['token']),
            'email' => $validated_azure_callback['email'],
            'expires_at' => $validated_azure_callback['expiresIn'] + $timestamp,
            'id' => $validated_azure_callback['id'],
            'refresh_token' => encrypt($validated_azure_callback['refreshToken']),
            'timestamp' => now()->timestamp
        ];

        event(new Registered($user = $this->upsert($azure_oauth_identity)));
        $this->guard()->login($user);
        $this->registered($request, $user);

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    /**
     * @param Provider $provider
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function oauthCallbackValidator(Provider $provider)
    {
        $identity = [];

        try {
            $identity = (array)$provider->user();
        } catch (Exception $e) {
            Log::error($e);
        }

        return Validator::make($identity, [
            'email' => ['required', 'string', 'email', 'max:255'],
            'expiresIn' => ['required', 'integer'],
            'id' => ['required'],
            'refreshToken' => ['required'],
            'token' => ['required']
        ])
            ->stopOnFirstFailure();
    }

    /**
     * @param array $azure_oauth_identity
     * @return mixed
     */
    protected function upsert(array $azure_oauth_identity)
    {
        function upsertAzureOauthIdentitySetting(array $azure_oauth_identity)
        {
            return Setting::updateOrCreate(['key' => 'azure_oauth_identity'], [
                'value' => $azure_oauth_identity
            ]);
        }

        function upsertUser(string $email)
        {
            return User::updateOrCreate(['email' => $email], []);
        }

        return DB::transaction(function () use ($azure_oauth_identity) {
            upsertAzureOauthIdentitySetting($azure_oauth_identity);

            return upsertUser($azure_oauth_identity['email']);
        });
    }
}
