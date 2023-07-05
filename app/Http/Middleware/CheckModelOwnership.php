<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckModelOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $model_class): Response
    {
        $model_class_name = strtoupper(array_slice(explode('\\', $model_class), -1)[0]);

        $model_id = $request->route()->parameter(strtolower($model_class_name));
        if(!$model_id){
            //Allows us to have cleaner code and apply this to POST routes even though it's irrelevant
            return $next($request);
        }
        // Retrieve the model using the provided model class and ID
        $model = $model_class::find($model_id);

        // Check if the model exists and if the authenticated user is associated with it
        if (!$model || $model->user_id !== auth()->id()) {
            return response()->json(['message' => "{$model_class_name}_NOT_FOUND"], 404);
        }

        return $next($request);
    }
}
