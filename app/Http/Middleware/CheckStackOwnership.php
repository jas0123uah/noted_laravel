<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Models\Stack;

class CheckStackOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info("checking stack");
        $stack_id = $request["stack_id"];
        if(!$stack_id){
            //Our validator will return the message saying we need a stack
            return $next($request);
        }
        $stack = Stack::find($stack_id);

        // Check if the stack exists and if the authenticated user is associated with it
        if (!$stack || $stack->user_id !== auth()->id()) {
            return response()->json(['message' => "STACK_NOT_FOUND"], 404);
        }

        return $next($request);
    }
}
