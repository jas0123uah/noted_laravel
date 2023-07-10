<?php
namespace Tests\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestHelper
{
    public static function createFakeUser(string $first_name, string $last_name, $email_verified = false, $is_unsubscribed = false)
    {
        $lowercase_name = strtolower($first_name).strtolower($last_name);
        $user = User::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => "{$lowercase_name}@example.com",
            'password' => Hash::make('password'),
        ]);
        if($email_verified){
            $user["email_verified_at"] = now()->format('Y-m-d\TH:i:s.u\Z');
        }
        $user["is_unsubscribed"] = $is_unsubscribed;
        $user->save();
        return $user;
    }
}
