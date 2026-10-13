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
        Schema::create('vet_visits', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('dog_id')->comment('Dog that visited the vet')->constrained('dogs')->cascadeOnDelete();
            $table->date('visited_on')->comment('Date of the visit');
            $table->string('clinic')->nullable()->comment('Name of the clinic');
            $table->string('reason')->comment('Reason for the visit');
            $table->text('diagnosis')->nullable()->comment('Diagnosis given by the vet');
            $table->text('notes')->nullable()->comment('Free-form notes');
            $table->date('next_due_on')->nullable()->comment('Date of the next recommended visit');
            $table->timestamps();

            $table->index(['dog_id', 'visited_on']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vet_visits');
    }
};
