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
        Schema::create('health_flags', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('dog_id')->comment('Dog the concern is about')->constrained('dogs')->cascadeOnDelete();
            $table->dateTime('flagged_at')->comment('When the concern was flagged');
            $table->string('description')->comment('Description of the concern');
            $table->enum('severity', ['low', 'medium', 'high'])->default('low')->comment('How serious the concern is');
            $table->enum('status', ['open', 'sent_to_vet', 'resolved'])->default('open')->comment('Current status of the flag');
            $table->enum('created_by', ['user', 'agent'])->default('user')->comment('Who raised the flag');
            $table->dateTime('resolved_at')->nullable()->comment('When the flag was resolved');
            $table->timestamps();

            $table->index(['dog_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_flags');
    }
};
