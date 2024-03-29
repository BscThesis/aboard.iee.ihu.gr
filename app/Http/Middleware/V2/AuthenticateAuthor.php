<?php

namespace App\Http\Middleware\V2;

use Illuminate\Support\Facades\Auth;

use Closure;

class AuthenticateAuthor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // echo json_encode($request); exit;
        try {
            $user = auth('api_v2')->userOrFail();
            if (!$user->is_admin && !$user->is_author) {
                return response()->json(['message' => 'You must have admin and/or author rights in order to continue'], 401);
            }
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json(['message' => 'You must sign in in order to continue'], 401);
        }

        return $next($request);
    }
}
