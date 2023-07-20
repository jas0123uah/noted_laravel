<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
    [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'email_verified_at' => now()->subDays(10),
        'password' => Hash::make('password'),
        'is_unsubscribed' => false,
        'subscription_token' => Str::random(32),
        'remember_token' => '',
        'created_at' => now()->subDays(10),
        'updated_at' => now()->subDays(10),
    ],
    [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
        'email_verified_at' => now()->subDays(10),
        'password' => Hash::make('password'),
        'is_unsubscribed' => false,
        'subscription_token' => Str::random(32),
        'remember_token' => '',
        'created_at' => now()->subDays(10),
        'updated_at' => now()->subDays(10),
    ],
    // Add more users as needed
]);

    }
}
