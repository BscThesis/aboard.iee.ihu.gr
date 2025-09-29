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
            dd($user->user); //TODO: remove
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
            $user->refresh();
        }

        $identity = $this->buildSsoUserObject($socialiteUser);
        $groups = \App\Models\V3\Group::query()->get(['id', 'name', 'is_user', 'is_author']);

        $matchedAnyGroup = false;

        foreach ($groups as $group) {
            if ($this->evaluateGroupRule($group->is_user, $identity)) {
                $this->assignGroupRole($user, $group, 'student');
                $matchedAnyGroup = true;
                continue;
            }

            if ($this->evaluateGroupRule($group->is_author, $identity)) {
                $this->assignGroupRole($user, $group, 'staff');

                foreach ($this->collectDescendantGroups($group) as $descendantGroup) {
                    $this->assignGroupRole($user, $descendantGroup, 'staff');
                }

                $matchedAnyGroup = true;
            }
        }

        if (!$matchedAnyGroup) {
            \Log::info('Login denied: no group matched by expressions', [
                'uid' => $socialiteUser->uid,
                'affiliation' => $identity->eduPersonAffiliation ?? null,
                'primary_affiliation' => $identity->eduPersonPrimaryAffiliation ?? null,
            ]);

            return response()->json([
                'message' => 'Your account does not have access to this application (no matching group rules).',
            ], 403);
        }

        // ----- token flow unchanged -----
        try {
            $user = \App\ApiUser::where('uid', $socialiteUser->uid)->first();
            $attributes = ['id' => $user->id];

            if (auth('api_v3')->check()) {
                auth('api_v3')->logout();
            }
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if (auth('api_v3')->check()) {
                auth('api_v3')->logout();
            }
            if ($e->getCode() === 400) {
                return response()->json('Invalid request', 400);
            } elseif ($e->getCode() === 401) {
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

        // keep as-is if you still want it
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

    private function buildSsoUserObject($socialiteUser): object
    {
        $raw = $socialiteUser->user ?? [];
        if (!is_array($raw)) {
            $raw = [];
        }

        $normalised = $raw;

        foreach (['eduPersonAffiliation', 'eduPersonPrimaryAffiliation'] as $key) {
            if (isset($normalised[$key])) {
                $value = $normalised[$key];
                if (is_array($value)) {
                    $normalised[$key] = $value[0] ?? null;
                } elseif ($value === '') {
                    $normalised[$key] = null;
                }
            } else {
                $normalised[$key] = null;
            }
        }

        $normalised['uid'] = $socialiteUser->uid ?? ($normalised['uid'] ?? null);
        $normalised['email'] = $socialiteUser->email ?? ($normalised['email'] ?? null);

        return json_decode(json_encode($normalised));
    }

    private function evaluateGroupRule(?string $expression, object $user): bool
    {
        if ($expression === null) {
            return false;
        }

        $code = trim($expression);

        if ($code === '') {
            return false;
        }

        $code = rtrim($code, ';');
        if (stripos($code, 'return') === false) {
            $code = 'return (' . $code . ');';
        } else {
            $code .= ';';
        }

        try {
            return (bool) eval($code);
        } catch (\Throwable $e) {
            \Log::warning('Group expression eval failed', [
                'expression' => $expression,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function collectDescendantGroups(\App\Models\V3\Group $group): array
    {
        $descendants = [];

        foreach ($group->subgroups as $child) {
            $descendants[] = $child;
            $descendants = array_merge($descendants, $this->collectDescendantGroups($child));
        }

        return $descendants;
    }

    private function assignGroupRole(ApiUser $user, \App\Models\V3\Group $group, string $role): void
    {
        $existing = $user->groups()->where('groups.id', $group->id)->first();

        if (!$existing) {
            $user->groups()->attach($group->id, ['role' => $role]);
            return;
        }

        $currentRole = $existing->pivot->role ?? null;

        if ($currentRole === $role) {
            return;
        }

        $priority = ['student' => 0, 'staff' => 1, 'admin' => 2];
        $currentScore = $priority[$currentRole] ?? -1;
        $newScore = $priority[$role] ?? -1;

        if ($newScore > $currentScore) {
            $user->groups()->updateExistingPivot($group->id, ['role' => $role]);
        }
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
