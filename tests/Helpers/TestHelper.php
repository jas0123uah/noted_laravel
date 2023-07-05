<?php
namespace Tests\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestHelper
{
    public static function createFakeUser(string $first_name, string $last_name)
    {
        $lowercase_name = strtolower($first_name).strtolower($last_name);
        return User::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => "{$lowercase_name}@example.com",
            'password' => Hash::make('password'),
        ]);
    }
}
