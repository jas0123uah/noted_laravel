<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stacks')->insert([
    [
        'user_id' => 1, // User ID of John Doe
        'name' => 'Stack 1',
        'created_at' => now()->subDays(9),
        'updated_at' => now()->subDays(9),
    ],
    [
        'user_id' => 2, // User ID of Jane Smith
        'name' => 'Stack 2',
        'created_at' => now()->subDays(9),
        'updated_at' => now()->subDays(9),
    ],
]);

    }
}
