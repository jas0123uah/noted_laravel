<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        Log::info('Home controller');
        $user = Auth::user();
        //$token = $request->user()->createToken($request->token_name);
       // $token = $request->user()->createToken($request->token_name);
        $token_name = 'API Token'; // Set a default token name if the request parameter is not available

        $token = $request->user()->createToken($token_name);

        $my_token = ['token' => $token->plainTextToken];
        $stacks = $user->stacks()->with('notecards')->get();
        $stacks_and_first_notecard = [];
        foreach ($stacks as $stack) {
            $first_notecard = $stack->notecards->first();
            $stacks_and_first_notecard[$stack->name] = $first_notecard;
        }
        return view('home', compact('stacks_and_first_notecard', 'my_token'));

    }
}
