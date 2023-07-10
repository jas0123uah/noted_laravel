<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
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
    return view('welcome');
});
Route::resource('stacks', '\App\Http\Controllers\StacksController')->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', [App\Http\Controllers\AboutController::class, 'index'])->name('about');

Route::get('/email/verify/{id}/{hash}', [
    'as' => 'verification.verify',
    'uses' => 'App\Http\Controllers\Auth\VerificationController@verify',
]);

Route::get('/unsubscribe/{subscription_token}', 'App\Http\Controllers\UnsubscribeController@unsubscribe')->name('unsubscribe');
