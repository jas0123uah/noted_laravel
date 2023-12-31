<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
class SubscribeController extends Controller
{
    public function subscribe(Request $request, string $subscription_token)
    {
        
        // Find the user based on the token
        $user = User::where('subscription_token', $subscription_token)->first();

        if ($user) {
            // Update the user's email preferences or perform necessary actions
            $user->is_unsubscribed = false;
            $user->subscription_token = Str::random(32);
            $user->save();

            return response()->json([
                'message' => 'Subscribed successfully!',
                'user' => $user   
            ]);
        } else {
        return response()->json([
            'message' => 'Invalid token. Please log into your account and unsubscribe through your account page.',
        ]);
        }
    }
    
}
