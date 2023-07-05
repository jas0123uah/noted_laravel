<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRequestedReviewNotecards
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_id = $request->route('user_id');
        // Check if the auth user should have access to the review notecards
        if ($user_id != auth()->id()) {
            return response()->json(['message' => "USER_NOT_FOUND"], 404);
        }

        return $next($request);
    }
}
