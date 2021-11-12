<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserLoggedIn;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Notification as NotificationResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Session;
use \Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except(['login', 'refresh', 'authors']);
    }

    /**
     * Logs a user in
     *
     * @return json
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $http = new \GuzzleHttp\Client;

        try {
            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $validatedData['username'],
                    'password' => $validatedData['password']
                ]
            ]);

            $user = User::where('uid', $validatedData['username'])->first();
            Auth::login($user);
            Notification::send($user, new UserLoggedIn());
            $user->update([
                'last_login_at' => Carbon::now()->toDateTimeString(),
            ]);

            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            Auth('web')->logout();
            Session::flush();

            if ($e->getCode() === 400) {
                return response()->json('Invalid request', $e->getCode());
            } else if ($e->getCode() === 401) {
                return response()->json('Invalid credentials', 401);
            }

            return response()->json('Something went wrong on the server.', $e->getCode());
        }
    }

    /**
     * Logs a user in
     *
     * @return json
     */
    public function refresh(Request $request)
    {
        $validatedData = $request->validate([
            'refresh_token' => 'required',
        ]);

        $http = new \GuzzleHttp\Client;

        try {
            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $validatedData['refresh_token'],
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                ]
            ]);

            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            Auth('web')->logout();
            Session::flush();

            if ($e->getCode() === 400) {
                return response()->json('Invalid request', $e->getCode());
            } else if ($e->getCode() === 401) {
                return response()->json('Invalid credentials', 401);
            }

            return response()->json('Something went wrong on the server.', $e->getCode());
        }
    }

    /**
     * Logs a user out
     *
     * @return void
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->revoke();
        });

        Auth('web')->logout();
        Session::flush();

        return response()->json(['message' => 'Logout success'], 200);
    }

    /**
     * Returns user info
     *
     * @return void
     */
    public function user(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Subscribes user to tags
     *
     * @return void
     */
    public function subscribe(Request $request)
    {
        $user = auth('api')->user();
        $tags = json_decode($request->input('tags'));
        $user->subscriptions()->sync($tags);
        return $request->user()->only('id', 'subscriptions');
    }

    /**
     * Get notifications
     *
     * @return void
     */
    public function notifications(Request $request)
    {
        $activities = auth('api')->user()->activities()->orderBy('created_at', 'desc')->paginate(10);
        return NotificationResource::collection($activities);
    }

    /**
     * Read notifications
     *
     * @return void
     */
    public function readNotifications(Request $request)
    {
        auth('api')->user()->unreadNotifications()->update(['read_at' => now()]);
        $activities = auth('api')->user()->activities()->orderBy('created_at', 'desc')->paginate(10);
        return NotificationResource::collection($activities);
    }

    /**
     * Returns all authors
     *
     * @return void
     */
    public function authors(Request $request)
    {
        return User::select('id', 'name')->where('is_author', 1)->orderBy('name', 'asc')->get();
    }
}
