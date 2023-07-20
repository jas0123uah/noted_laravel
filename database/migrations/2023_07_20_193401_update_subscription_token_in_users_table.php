<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;


class UpdateSubscriptionTokenInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Generate a random 32-character string for the subscription_token field
        $users = \DB::table('users')->get();
        foreach ($users as $user) {
            \DB::table('users')
                ->where('id', $user->id)
                ->update(['subscription_token' => Str::random(32)]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
