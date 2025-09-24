<?php

namespace App\Http\Controllers\V3\Auth;

use App\Http\Controllers\Controller;
use App\ApiUser;
use Laravel\Socialite\Facades\Socialite;
use \Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AuthJWTController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {}

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
            try {
                $web_redirect = Crypt::decryptString(($request->state));
                if (filter_var($web_redirect, FILTER_VALIDATE_URL) === FALSE) {
                    $web_redirect = null;
                }
            } catch (DecryptException $e) {
            }
        }
        try {
            // Get user from Login Iee Ihu with OAuth2.0 
            $user = Socialite::driver('iee_api')->stateless()->user();
            // dd($user->user); //TODO: remove
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
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
    $user = \App\ApiUser::where('uid', $socialiteUser->uid)->first();

    $payload = [
        'name'     => $socialiteUser->name,
        'name_eng' => $socialiteUser->name_eng,
        'email'    => $socialiteUser->email,
        'uid'      => $socialiteUser->uid,
    ];

    if (!$user) {
        $user = \App\ApiUser::create($payload);
    } else {
        $user->update($payload);
        // refresh the instance so relationships work right away
        $user->refresh();
    }

    /**
     * --- Group resolve & attach as STUDENT ---
     * Source: eduPersonPrimaryAffiliation
     * 1) groups.internal_identifier = code (lowercased)
     * 2) groups.affiliation_code   = code
     * 3) config('group_map')[code] -> internal_identifier, then lookup
     * If none match, return 422 (don’t auto-create).
     */
    $primaryAff = $socialiteUser->user['eduPersonPrimaryAffiliation'] ?? null;

    if (!$primaryAff || !is_string($primaryAff)) {
        \Log::warning('Missing eduPersonPrimaryAffiliation on login', ['uid' => $socialiteUser->uid]);
        return response()->json([
            'message' => 'Cannot determine your department (eduPersonPrimaryAffiliation missing). Please contact support.'
        ], 422);
    }

    $code  = mb_strtolower(trim($primaryAff));
    $group = \App\Models\V3\Group::where('internal_identifier', $code)->first();

    if (!$group) {
        $group = \App\Models\V3\Group::where('affiliation_code', $code)->first();
    }

    if (!$group) {
        $map = config('group_map', []);
        if (is_array($map) && isset($map[$code])) {
            $mapped = $map[$code];
            // allow either a simple string or ['internal_identifier' => '...']
            if (is_string($mapped)) {
                $group = \App\Models\V3\Group::where('internal_identifier', $mapped)->first();
            } elseif (is_array($mapped) && !empty($mapped['internal_identifier'])) {
                $group = \App\Models\V3\Group::where('internal_identifier', $mapped['internal_identifier'])->first();
            }
        }
    }

    if (!$group) {
        \Log::warning('No group matched for affiliation code', ['uid' => $socialiteUser->uid, 'code' => $code]);
        return response()->json([
            'message' => "Your affiliation code '{$code}' is not configured to a group. Please contact support."
        ], 422);
    }

    // Attach/update pivot as 'student' without removing other memberships
    $user->groups()->syncWithoutDetaching([$group->id => ['role' => 'student']]);
    $user->groups()->updateExistingPivot($group->id, ['role' => 'student']);
    // --- end group-attach ---

    try {
        // Get user and then try to log in and sent notification
        $user = ApiUser::where('uid', $socialiteUser->uid)->first();
        $attributes = ['id' => $user->id];

        if (auth('api_v3')->check()) {
            auth('api_v3')->logout();
        }
        // return new static($attributes); 
    } catch (\GuzzleHttp\Exception\BadResponseException $e) {
        // If an error occurs log user out
        if (auth('api_v3')->check()) {
            auth('api_v3')->logout();
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
        if (!$token = auth('generate_token')->setTTL(120)->login($user)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return redirect()->away($web_redirect . '/login_success?token=' . $token);
    } else {
        if (!$token = auth('api_v3')->login($user)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
    auth('api_v3')->login($user);

    // Keeping this since you had it (even though you said it might not be needed)
    $user->update([
        'last_login_at' => \Carbon\Carbon::now()->toDateTimeString(),
    ]);

    return $this->respondWithToken($token, $attributes);
}

    /**
     * generateToken generates a JWT by using a one time access token generated 
     * after a user logs in via iee SSO
     * @param (post) token required
     */
    public function generateToken(Request $request)
    {
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

        if (! $token = auth('api_v3')->login($main_user)) {
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
            $user = auth('api_v3')->userOrFail();
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
        auth('api_v3')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api_v3')->refresh());
    }

    /**
     * Subscribes user to tags
     *
     * @return void
     */
    public function subscribe(Request $request)
    {
        // Get logged in user and tags from the request and updated user's subscriptions table
        $user = auth('api_v3')->user();
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
            $user = auth('api_v3')->userOrFail();
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['message' => 'You are not logged in'], 401);
        }

        return $user->subscriptions()->get();
        // Check if user is logged in and return subscription otherwise return message
        // $user = auth('api_v3')->userOrFail();
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
            'expires_in' => auth('api_v3')->factory()->getTTL() * 60
        ]);
    }
}
