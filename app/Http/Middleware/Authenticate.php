<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        Log::debug($request->hasSession());
        if ($request->hasSession()) {
            Log::info("Found a session");
        }
        if ($request->user()) {
            Log::info("Found a user");
        }
        Log::info("HELLO");
        Log::debug($request->user());
        Log::info($request->cookie());
        Log::debug(json_encode($request));
        Log::info($request->expectsJson());
        return $request->expectsJson() ? null : route('login');
    }
}
