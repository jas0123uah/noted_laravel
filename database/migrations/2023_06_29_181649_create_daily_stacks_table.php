<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_stacks', function (Blueprint $table) {
            $table->id(column:'daily_stacks_id');
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->nullable(false)->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('stack_id');
            $table->foreign('stack_id')->references('stack_id')->on('stacks')->nullable(false)->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('notecard_id');
            $table->foreign('notecard_id')->references('notecard_id')->on('notecards')->nullable(false)->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stacks');
    }
};
