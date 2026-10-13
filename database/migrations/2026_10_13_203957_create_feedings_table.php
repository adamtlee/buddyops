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
        Schema::create('feedings', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('dog_id')->comment('Dog that was fed')->constrained('dogs')->cascadeOnDelete();
            $table->dateTime('fed_at')->comment('When the dog was fed');
            $table->string('food_type')->comment('Type of food given');
            $table->unsignedInteger('amount_g')->nullable()->comment('Amount in grams');
            $table->enum('portion_eaten', ['all', 'most', 'some', 'none'])->default('all')->comment('How much of the portion the dog ate');
            $table->text('notes')->nullable()->comment('Free-form notes');
            $table->enum('source', ['web', 'chat', 'seed'])->default('web')->comment('Where the record was created');
            $table->timestamps();

            $table->index(['dog_id', 'fed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedings');
    }
};
