<?php

namespace App\Http\Middleware;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $guards = empty($guards) ? [null] : $guards;
            foreach ($guards as $guard) {
                if (!Auth::guard($guard)->check())
                    return ApiResponse::error([], ApiMessage::AUTH_UNAUTHORIZED, 401);
            }
        } catch (TokenExpiredException $e) {
            return  ApiResponse::error(['Token Expired'], ApiMessage::AUTH_UNAUTHORIZED, 401);
        } catch (TokenInvalidException $e) {
            return  ApiResponse::error(['Token Invalid'], ApiMessage::AUTH_UNAUTHORIZED, 401);
        }

        return $next($request);
    }
}
