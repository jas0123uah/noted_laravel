<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewnotecardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('review_notecards')->insert([
            [
                'user_id' => 1, // User ID of John Doe
                'stack_id' => 1, // Stack ID of Stack 1
                'notecard_id' => 1, // Notecard ID of Notecard 1
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2, // User ID of Jane Smith
                'stack_id' => 2, // Stack ID of Stack 2
                'notecard_id' => 2, // Notecard ID of Notecard 2
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
