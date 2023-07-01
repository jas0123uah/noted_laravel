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
        Schema::create('stacks', function (Blueprint $table) {
            $table->id("stack_id");
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->nullable(false)->onUpdate('cascade')->onDelete('cascade');
            // $table->foreignId('user_id')->constrained()->nullable(false)->onUpdate('cascade')->onDelete('cascade');
            $table->string('name', 255)->nullable(false);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stacks');
    }
};
