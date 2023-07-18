<?php

use App\Http\Middleware\CheckModelOwnership;
use App\Http\Middleware\CheckStackOwnership;
use Illuminate\Support\Facades\Route;
use App\Models\Stack;
use App\Models\User;
use App\Models\Notecard;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//Route::post('/login', 'AuthController@login');
$models = [
    Stack::class => "stacks",
    User::class => "users",
    Notecard::class => "notecards",
];

foreach ($models as $model_class => $model_plural) {
    Route::group(['middleware' => ['auth:sanctum', 'auth']], function () use ($model_class, $model_plural) {
        // Apply 'auth' middleware to all model endpoints
        $model_capital_plural = ucfirst($model_plural);
        $model_singular = substr($model_plural, 0, -1);

        // Route for all model endpoints
        Route::resource($model_plural, "App\Http\Controllers\\{$model_capital_plural}Controller");
        
        $middleware = [CheckModelOwnership::class . ":". $model_class];
        //Ensure the stack we are trying to associate with a notecard or review notecard is ours
        if ($model_singular === 'notecard' || $model_singular === 'reviewnotecard') {
            $middleware[] = CheckStackOwnership::class;
        }

        // CRD routes with 'CheckModelOwnership' middleware
        Route::middleware($middleware)->group(function () use ($model_singular, $model_plural, $model_capital_plural) {
            Route::get("{$model_plural}/{{$model_singular}}", "App\Http\Controllers\\{$model_capital_plural}Controller@show");
            Route::post($model_plural, "App\Http\Controllers\\{$model_capital_plural}Controller@store"); // Add this line for the POST route
            Route::put("{$model_plural}/{{$model_singular}}", "App\Http\Controllers\\{$model_capital_plural}Controller@update");
            Route::delete("{$model_plural}/{{$model_singular}}", "App\Http\Controllers\\{$model_capital_plural}Controller@destroy");
        });

        
    });
}
// routes/api.php

Route::post('/reviewnotecards', [App\Http\Controllers\MailController::class, 'index'])->middleware('auth');
Route::get('/reviewnotecards/{user_id}/', [App\Http\Controllers\ReviewnotecardsController::class, 'show'])->middleware([ 'auth:sanctum', 'auth',  'checkRequestedReviewNotecards']);
Route::get('/access-token', function () {
    Log::debug(auth()->user());
    return response()->json([
        "data" => auth()->user()->accessToken()
    ]);
})->middleware('auth.session');
