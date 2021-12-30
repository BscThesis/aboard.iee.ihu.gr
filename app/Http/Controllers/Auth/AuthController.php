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
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Session;
use \Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Str;
use Hash;

class AuthController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except(['login', 'refresh', 'authors', 'signIn', 'redirect']);
    }


    public function signIn()
    {
	//dd(Socialite::driver('iee'));
	return Socialite::driver('iee')->redirect();
    }

    public function redirect()
    {
        $user = Socialite::driver('iee')->user();
        
        $this->login($user);


        // $user1 = User::query()->whereEmail($user->email)->first();
        // Auth::guard('web')->login($user);
        // return redirect('/announcements');
    }

    /**
     * Logs a user in
     *
     * @return json
     */
    public function login($socialiteUser)
    {
        // Create or update user based on our results
        $user = User::where('uid', $socialiteUser['uid'])->first();
        if ($user === null) {
            $user = User::create(
                [
                    'name' => $socialiteUser['name'],
                    'name_eng' => $socialiteUser['name_eng'],
                    'email' => $socialiteUser['email'],
                    'uid' => $socialiteUser['uid'],
                    'is_author' => $socialiteUser['is_author']
                ]
            );
        } else {
            $user = User::where('uid', $socialiteUser['uid'])->update(
                [
                    'name' => $socialiteUser['name'],
                    'name_eng' => $socialiteUser['name_eng'],
                    'email' => $socialiteUser['email'],
                    'uid' => $socialiteUser['uid'],
                    'is_author' => $socialiteUser['is_author']
                ]
            );
        }
        try {
            $user = User::where('uid', $socialiteUser['uid'])->first();
            Auth::login($user);
            Notification::send($user, new UserLoggedIn());
            $user->update([
                'last_login_at' => Carbon::now()->toDateTimeString(),
            ]);
            return redirect('/announcements');
            
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
        $local_ip = $request->session()->get('local_ip', 0);                
        if ($local_ip == 1 or Auth::guard('api')->check()) {
            return User::select('id', 'name')->where('is_author', 1)
            ->withCount(['announcements'=>function ($query) use ($request){
                $query->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    json_decode(request()->input('q', '')));
            }])->orderBy('name', 'asc')->get();
        } elseif  ($request->headers->has('authorization') && !Auth::guard('api')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
         else {
            return User::select('id', 'name')->where('is_author', 1)
            ->withCount(['announcements'=>function ($query) use ($request){
                $query->whereHas('tags', function ($query) {
                    $query->where('is_public', 1);
                })
                ->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    json_decode(request()->input('q', ''))  
                );
            }])->orderBy('name', 'asc')->get();
            
        }           
    }
}
