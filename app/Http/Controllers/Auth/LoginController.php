<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
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

public function demo(Request $request) {
    $user = User::firstOrCreate(['email' => 'demo@example.com'], [
        'first_name' => 'Demo',
        'last_name' => 'User',
        'password' => bcrypt('password'),
        'subscription_token' => Str::random(32),
    ]);

    // Log in the user
    Auth::login($user);

    $token_name = 'API Token'; // Set a default token name if the request parameter is not available

    $token = $user->createToken($token_name);

    $my_token = ['token' => $token->plainTextToken];
    $stacks = $user->stacks()->with('notecards')->get();
    $stacks_and_first_notecard = [];
    foreach ($stacks as $stack) {
        $first_notecard = $stack->notecards->first();
        $stacks_and_first_notecard[$stack->name] = $first_notecard;
    }
    return view('home', compact('stacks_and_first_notecard', 'my_token'));
}


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
