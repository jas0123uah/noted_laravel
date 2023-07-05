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
        Schema::create('notecards', function (Blueprint $table) {
            $table->id(column: 'notecard_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->nullable(false)->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('stack_id');
            $table->foreign('stack_id')->references('stack_id')->on('stacks')->nullable(false)->onUpdate('cascade')->onDelete('cascade');
            $table->text('front')->nullable(false)->min(1);
            $table->text('back')->nullable(false)->min(1);
            $table->decimal('e_factor', 10, 2)->nullable(false)->default(2.50);
            $table->integer('repetition')->nullable(false)->default(0);
            $table->timestamp('next_repetition')->default(DB::raw('DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY)'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notecards');
    }
};
