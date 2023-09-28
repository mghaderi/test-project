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
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table
                ->unsignedBigInteger('user_id')
                ->foreign('user_id', 'fk-user_achievements-user_id')
                ->on('users')
                ->references('id')
                ->restrictOnUpdate()
                ->restrictOnDelete();
            $table
                ->unsignedBigInteger('achievement_id')
                ->foreign('achievement_id', 'fk-user_achievements-achievement_id')
                ->on('achievements')
                ->references('id')
                ->restrictOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};
