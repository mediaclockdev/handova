<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Laravel\Sanctum\PersonalAccessToken;

class AwsTokenAuth
{
    public function handle($request, Closure $next)
    {
        $token =
            $request->bearerToken()
            ?? $request->header('X-Authorization')
            ?? null;

        if (!$token) {
            return response()->json(['message' => 'Token required'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        auth()->login($accessToken->tokenable);

        return $next($request);
    }
}