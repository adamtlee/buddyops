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
        Schema::create('documents', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('dog_id')->comment('Dog the document belongs to')->constrained('dogs')->cascadeOnDelete();
            $table->string('title')->comment('Display title of the document');
            $table->enum('type', ['vet_record', 'food_label', 'training', 'other'])->default('other')->comment('Kind of document');
            $table->string('file_path')->comment('Storage path of the uploaded file');
            $table->enum('status', ['uploaded', 'processed', 'failed'])->default('uploaded')->comment('Text extraction and embedding status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
