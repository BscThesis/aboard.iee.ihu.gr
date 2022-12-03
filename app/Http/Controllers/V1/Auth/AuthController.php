<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\V1\UserLoggedIn;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Notification as NotificationResource;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Tag;
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
        $this->middleware('auth:web')->except(['login', 'authors', 'signIn', 'redirect']);
    }

    /**
     * Try to get user from OAuth2.0 and the call callback method below
     *
     * @return json
     */
    public function signIn()
    {
	    return Socialite::driver('iee')->redirect();
    }

    /**
     * Redirect user after successful login
     *
     * @return route
     */
    public function redirect()
    {
        // Get user from Login Iee Ihu with OAuth2.0 
        $user = Socialite::driver('iee')->user();
    	// Try to log user in calling our login method below
        $this->login($user);
        // Then redirect user to announcements page
	    return redirect(route('announcements'));
    }

    /**
     * Logs a user in
     *
     * @return json
     */
    public function login($socialiteUser)
    {
        // Create or update user based on our results
        $user = User::where('uid', $socialiteUser->uid)->first();
        if ($user === null) {
            $user = User::create(
                [
                    'name' => $socialiteUser->name,
                    'name_eng' => $socialiteUser->name_eng,
                    'email' => $socialiteUser->email,
                    'uid' => $socialiteUser->uid,
                    'is_author' => $socialiteUser->is_author
                ]
            );
        } else {
            $user = User::where('uid', $socialiteUser->uid)->update(
                [
                    'name' => $socialiteUser->name,
                    'name_eng' => $socialiteUser->name_eng,
                    'email' => $socialiteUser->email,
                    'uid' => $socialiteUser->uid,
                    'is_author' => $socialiteUser->is_author
                ]
            );
	    }
	
        try {
            // Get user and then try to log in and sent notification
            $user = User::where('uid', $socialiteUser->uid)->first();	    
            $attributes = ['id' => $user->id];
            Auth::login($user);
            //Notification::send($user, new UserLoggedIn());
            $user->update([
                'last_login_at' => Carbon::now()->toDateTimeString(),
	        ]);
	  
	        return new static($attributes); 
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            // If an error occurs log user out and clear the session
            Auth('web')->logout();
            Session::flush();
            
            // Depending on the error code sent the appropriate message
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
        // Get logged in user and tags from the request and updated user's subscriptions table
        $user = auth()->user();
        $tags = json_decode($request->input('tags'));
        $user->subscriptions()->sync($tags);
        // Return user's id and subscriptions
        return $request->user()->only('id', 'subscriptions');
    }
    
    /**
     * Returns users subscriptions
     *
     * @return void
     */
    public function getSubscriptions(Request $request)
    {
        // Check if user is logged in and return subscription otherwise return message
        $user = auth()->user();
        if($user === null){
            return response()->json(['message' => 'Unauthenticated'], 401);
        }else{
            return $user->subscriptions()->get();
        }
    }

    /**
     * Get notifications
     *
     * @return void
     */
    public function notifications(Request $request)
    {
        // Return user's notifications paginated by 10 and order it by newest first
        $activities = auth()->user()->activities()->orderBy('created_at', 'desc')->paginate(10);
        return NotificationResource::collection($activities);
    }

    /**
     * Read notifications
     *
     * @return void
     */
    public function readNotifications(Request $request)
    {
        // Update read_at column in user's table
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        // Show user's notifications paginated by 10 and order it by newest first
        $activities = auth()->user()->activities()->orderBy('created_at', 'desc')->paginate(10);
        return NotificationResource::collection($activities);
    }

    /**
     * Returns all authors
     *
     * @return void
     */
    public function authors(Request $request)
    {
        // Check if header has authorization and if user is not logged in and then try to log in from the token
	    if ($request->headers->has('authorization') && !Auth::guard('web')->check()) {
            try{
                $socialiteUser = Socialite::driver('iee')->userFromToken($request->bearerToken());
                $user = User::where('uid', $socialiteUser['uid'])->first();
                Auth('web')->login($user);
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                Auth('web')->logout();
                Session::flush();
                return response()->json(['message' => 'Unauthenticated'], 401);
            } catch (\Throwable $e) {
                Auth('web')->logout();
                Session::flush();
                if ($user == null)
                    return response()->json(['message' => 'User not found! Login to our website first in order to activate your account.'], 404);
                return response()->json(['message' => $e->getMessage()], 401);
            }         
        }
        // If user is logged in or inside university's wifi return authors, filtering and then counting every announcement each one has
        $local_ip = $request->session()->get('local_ip', 0);                
        if ($local_ip == 1 or Auth::guard('web')->check()) {
            return User::select('id', 'name')->where('is_author', 1)
            ->withCount(['announcements'=>function ($query) use ($request){
                $query->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    json_decode(request()->input('title', '')),
                    json_decode(request()->input('body', '')),
                    json_decode(request()->input('updatedAfter', '')),
                    json_decode(request()->input('updatedBefore', '')),
                );
            }])->having('announcements_count','>',0)->orderBy('name', 'asc')->get();
	    } 
        // Else return authors filtering and then counting every public announcement each one has
        else {
            return User::select('id', 'name')->where('is_author', 1)
            ->withCount(['announcements'=>function ($query) use ($request){
                $query->whereHas('tags', function ($query) {
                    $query->where('is_public', 1);
                })
                ->withFilters(
                    request()->input('users', []),
                    request()->input('tags', []),
                    json_decode(request()->input('title', '')),
                    json_decode(request()->input('body', '')),
                    json_decode(request()->input('updatedAfter', '')),
                    json_decode(request()->input('updatedBefore', '')),
                );
            }])->having('announcements_count','>',0)->orderBy('name', 'asc')->get();            
        }           
    }
}
