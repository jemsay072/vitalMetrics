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
        Schema::create('workout_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type'); // treadmill, running, walking
            $table->integer('duration_minutes');
            $table->decimal('distance_km', 5, 2)->nullable();
            $table->integer('calories_burned')->nullable();
            $table->decimal('speed_kmh', 5, 2)->nullable();
            $table->integer('steps')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_trackers');
    }
};
