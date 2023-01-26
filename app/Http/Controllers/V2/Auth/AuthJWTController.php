<?php

namespace App\Http\Controllers\V2\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use App\ApiUser;
use Laravel\Socialite\Facades\Socialite;
use \Carbon\Carbon;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AuthJWTController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Try to get user from OAuth2.0 and the call callback method below
     *
     * @return json
     */
    public function signIn()
    {
	    return Socialite::driver('iee_api')
            ->redirect();
    }

    /**
     * Try to get user from OAuth2.0 and the call callback method below
     *
     * @return json
     */
    public function signInWeb(Request $request)
    {
        if (!isset($request->redirect) || empty($request->redirect)) {
            return $this->signIn();
        }
	    return Socialite::driver('iee_api')
            ->with(['state' => Crypt::encryptString($request->redirect)])
            ->redirect();
    }

    /**
     * Redirect user after successful login
     *
     * @return route
     */
    public function redirect(Request $request)
    {
        $web_redirect = null;
        if (isset($request->state) && !empty($request->state)) {
            try{
                $web_redirect = Crypt::decryptString(($request->state));
                if (filter_var($web_redirect, FILTER_VALIDATE_URL) === FALSE) {
                    $web_redirect = null;
                }
            }
            catch(DecryptException $e) {

            }
            
        }
        // Get user from Login Iee Ihu with OAuth2.0 
        $user = Socialite::driver('iee_api')->stateless()->user();
    	// Try to log user in calling our login method below
	    return $this->login($user, $web_redirect);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login($socialiteUser, $web_redirect = null)
    {
        // Create or update user based on our results
        $user = ApiUser::where('uid', $socialiteUser->uid)->first();
        if ($user === null) {
            $user = ApiUser::create(
                [
                    'name' => $socialiteUser->name,
                    'name_eng' => $socialiteUser->name_eng,
                    'email' => $socialiteUser->email,
                    'uid' => $socialiteUser->uid,
                    'is_author' => $socialiteUser->uid === 'it134062' ? 1 : $socialiteUser->is_author
                ]
            );
        } else {
            $user = ApiUser::where('uid', $socialiteUser->uid)->update(
                [
                    'name' => $socialiteUser->name,
                    'name_eng' => $socialiteUser->name_eng,
                    'email' => $socialiteUser->email,
                    'uid' => $socialiteUser->uid,
                    'is_author' => $socialiteUser->uid === 'it134062' ? 1 : $socialiteUser->is_author
                ]
            );
	    }
	
        try {
            // Get user and then try to log in and sent notification
            $user = ApiUser::where('uid', $socialiteUser->uid)->first();	    
            $attributes = ['id' => $user->id];

            if (auth('api_v2')->check()) {
                auth('api_v2')->logout();
            }
            
	  
	        // return new static($attributes); 
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            // If an error occurs log user out
            if (auth('api_v2')->check()) {
                auth('api_v2')->logout();
            }
            
            // Depending on the error code sent the appropriate message
            if ($e->getCode() === 400) {
                return response()->json('Invalid request', $e->getCode());
            } else if ($e->getCode() === 401) {
                return response()->json('Invalid credentials', 401);
            }

            return response()->json('Something went wrong on the server.', $e->getCode());
        }

        if (!is_null($web_redirect)) {
            if (! $token = auth('generate_token')->setTTL(120)->login($user)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->away($web_redirect . '/login_success?token=' . $token);
        } else {
            if (! $token = auth('api_v2')->login($user)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }   
        auth('api_v2')->login($user);
        //Notification::send($user, new UserLoggedIn());
        $user->update([
            'last_login_at' => Carbon::now()->toDateTimeString(),
        ]);
        return $this->respondWithToken($token, $attributes);
    }

    /**
     * generateToken generates a JWT by using a one time access token generated 
     * after a user logs in via iee SSO
     * @param (post) token required
     */
    public function generateToken(Request $request) {
        if (empty($request->token)) {
            return response()->json(['error' => 'Empty token'], 401);
        }

        if (!auth('generate_token')->check()) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $token_user = auth('generate_token')->user(); 
        $main_user = ApiUser::where('uid', $token_user->uid)->first();	 

        auth('generate_token')->logout();
        auth('generate_token')->invalidate(true);
        
        $attributes = ['id' => $main_user->id];

        if (! $token = auth('api_v2')->login($main_user)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        

        return $this->respondWithToken($token, $attributes);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        try {
            $user = auth('api_v2')->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['message' => 'You are not logged in'], 401);
        }

        return response()->json($user);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api_v2')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api_v2')->refresh());
    }

    /**
     * Subscribes user to tags
     *
     * @return void
     */
    public function subscribe(Request $request)
    {
        // Get logged in user and tags from the request and updated user's subscriptions table
        $user = auth('api_v2')->user();
        $tags = ($request->input('tags'));
        $user->subscriptions()->sync($tags);
        // Return user's id and subscriptions
        return $user->only('id', 'subscriptions');
    }

    /**
     * Returns users subscriptions
     *
     * @return void
     */
    public function getSubscriptions(Request $request)
    {

        try {
            $user = auth('api_v2')->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['message' => 'You are not logged in'], 401);
        }

        return $user->subscriptions()->get();
        // Check if user is logged in and return subscription otherwise return message
        // $user = auth('api_v2')->userOrFail();
        // if($user === null){
        //     return response()->json(['message' => 'Unauthenticated'], 401);
        // }else{
        //     return $user->subscriptions()->get();
        // }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $attributes = [])
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user_data' => $attributes,
            'expires_in' => auth('api_v2')->factory()->getTTL() * 60
        ]);
    }
}