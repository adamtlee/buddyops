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
        Schema::create('walks', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('dog_id')->comment('Dog that was walked')->constrained('dogs')->cascadeOnDelete();
            $table->dateTime('started_at')->comment('When the walk started');
            $table->unsignedSmallInteger('duration_min')->comment('Duration in minutes');
            $table->decimal('distance_km', 5, 2)->nullable()->comment('Distance in kilometers');
            $table->text('notes')->nullable()->comment('Free-form notes');
            $table->enum('source', ['web', 'chat', 'seed'])->default('web')->comment('Where the record was created');
            $table->timestamps();

            $table->index(['dog_id', 'started_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walks');
    }
};
