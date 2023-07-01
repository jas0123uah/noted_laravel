<?php
//Ref: https://stackoverflow.com/questions/70792440/how-to-log-laravel-get-and-post-requests
namespace App\Http\Middleware;

use Closure;

class LogRequest
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
        $response = $next($request);
        app('log')->info("Request Captured", $request->all());

        return $response;
    }
}
