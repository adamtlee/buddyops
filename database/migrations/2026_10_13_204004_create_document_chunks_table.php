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
        Schema::create('document_chunks', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('document_id')->comment('Document the chunk belongs to')->constrained('documents')->cascadeOnDelete();
            $table->unsignedInteger('chunk_index')->comment('Position of the chunk within the document');
            $table->longText('content')->comment('Text content of the chunk');
            $table->json('embedding')->nullable()->comment('Embedding vector stored as a JSON array of floats');
            $table->timestamps();

            $table->index(['document_id', 'chunk_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_chunks');
    }
};
