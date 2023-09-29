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
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')
                ->foreign('user_id', 'fk-user_badges-user_id')
                ->on('users')
                ->references('id')
                ->restrictOnUpdate()
                ->restrictOnDelete();
            $table
                ->unsignedBigInteger('badge_id')
                ->foreign('badge_id', 'fk-user_achievements-badge_id')
                ->on('badges')
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
        Schema::dropIfExists('user_badges');
    }
};
