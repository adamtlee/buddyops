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
        Schema::create('play_sessions', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('dog_id')->comment('Dog that played')->constrained('dogs')->cascadeOnDelete();
            $table->dateTime('played_at')->comment('When the play session happened');
            $table->enum('activity', ['fetch', 'tug', 'training', 'other'])->default('other')->comment('Type of play activity');
            $table->unsignedSmallInteger('duration_min')->comment('Duration in minutes');
            $table->unsignedTinyInteger('energy_level')->nullable()->comment('Dog energy level from 1 (low) to 5 (high)');
            $table->text('notes')->nullable()->comment('Free-form notes');
            $table->enum('source', ['web', 'chat', 'seed'])->default('web')->comment('Where the record was created');
            $table->timestamps();

            $table->index(['dog_id', 'played_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('play_sessions');
    }
};
