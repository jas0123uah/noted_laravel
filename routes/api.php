<?php

use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StacksController;
use App\Http\Controllers\NotecardsController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::resource('users', );
Route::resource('stacks', StacksController::class);
Route::get('/stacks', function(){
return view('welcome');
});
Route::resource('users', UsersController::class);
Route::resource('notecards', NotecardsController::class);
