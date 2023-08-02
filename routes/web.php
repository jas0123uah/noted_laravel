<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    //return view('welcome');
    return view('welcome');})->name('welcome');
Route::resource('stacks', '\App\Http\Controllers\StacksController')->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', [App\Http\Controllers\AboutController::class, 'index'])->name('about');

Route::get('/email/verify/{id}/{hash}', [
    'as' => 'verification.verify',
    'uses' => 'App\Http\Controllers\Auth\VerificationController@verify',
]);

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
})->middleware(['auth'])->name('verification.send');

Route::get('/unsubscribe/{subscription_token}', 'App\Http\Controllers\UnsubscribeController@unsubscribe')->name('unsubscribe');
Route::get('/subscribe/{subscription_token}', 'App\Http\Controllers\SubscribeController@subscribe')->name('subscribe');
Route::post('/demo', 'App\Http\Controllers\Auth\LoginController@demo')->name('demo');
Route::get('/stacks/{stack_id}/study/', [App\Http\Controllers\StacksController::class, 'study'])->middleware(['auth:sanctum', 'auth']);
