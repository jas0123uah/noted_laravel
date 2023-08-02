<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
class UnsubscribeController extends Controller
{
    public function unsubscribe(Request $request, string $subscription_token)
    {
        
        // Find the user based on the token
        $user = User::where('subscription_token', $subscription_token)->first();

        if ($user) {
            // Update the user's email preferences or perform necessary actions
            $user->is_unsubscribed = true;
            $user->subscription_token = Str::random(32);
            $user->save();

            return view('welcome')->with(
                ['message' => 'Unsubscribed from daily emails successfully!']
            );
        } else {
        return response()->json([
            'message' => 'Invalid token. Please log into your account and unsubscribe through your account page.',
        ]);
        }
    }
    
}
