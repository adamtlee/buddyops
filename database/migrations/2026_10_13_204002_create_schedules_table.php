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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('dog_id')->comment('Dog the schedule belongs to')->constrained('dogs')->cascadeOnDelete();
            $table->enum('type', ['feeding', 'walk', 'medication', 'vet'])->comment('Kind of scheduled event');
            $table->string('title')->comment('Display title of the schedule');
            $table->time('time_of_day')->comment('Local time the event is due');
            $table->json('days_of_week')->comment('Array of weekday numbers, 0=Sunday through 6=Saturday');
            $table->boolean('is_active')->default(true)->comment('Whether the schedule is active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
