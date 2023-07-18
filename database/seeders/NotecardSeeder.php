<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotecardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('notecards')->insert([
            [
                'user_id' => 1, // User ID of John Doe
                'stack_id' => 1, // Stack ID of Stack 1
                'front' => '<p>Front of Notecard 1</p>',
                'back' => '<p>Back of Notecard 1</p>',
                'e_factor' => 2.5,
                'repetition' => 0,
                'next_repetition' => now()->subDays(7),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'user_id' => 2, // User ID of Jane Smith
                'stack_id' => 2, // Stack ID of Stack 2
                'front' => '<p>Front of Notecard 2</p>',
                'back' => '<p>Back of Notecard 2</p>',
                'e_factor' => 2.5,
                'repetition' => 0,
                'next_repetition' => now()->subDays(7),
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
        ]);

    }
}
