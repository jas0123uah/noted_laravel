<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    // public function authenticated(Request $request, $user) {
    //     Log::info('Making token');
    //     $token = $user-> createToken('API Token')->plainTextToken;
    //     Log::info($token);
    //     $cookie = cookie('access_token', $token, 60*24*7, null, null, false, true);
    //     // return response()->json([
    //     //     'token' => $token
    //     // ]);
    //     $responseData = [
    //     'access_token' => $token,
    // ];

    // return redirect('/home')->with($responseData);
    //     //return redirect('/home')->cookie($cookie);
        
    // }
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
