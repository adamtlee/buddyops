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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->foreignId('user_id')
                ->comment('User who owns the message')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('dog_id')
                ->nullable()
                ->comment('Dog associated with the message')
                ->constrained('dogs')
                ->nullOnDelete();
            $table->enum('role', ['user', 'assistant', 'tool'])
                ->comment('Who produced the message');
            $table->longText('content')
                ->nullable()
                ->comment('Message content');
            $table->json('tool_calls')
                ->nullable()
                ->comment('Tool calls requested by the assistant, as [{id,name,input}]');
            $table->string('tool_call_id')
                ->nullable()
                ->comment('For role=tool, which call this answers');
            $table->boolean('is_error')
                ->default(false)
                ->comment('Whether the tool response represents an error');
            $table->timestamp('created_at')
                ->nullable()
                ->comment('When the message was created');
            $table->timestamp('updated_at')
                ->nullable()
                ->comment('When the message was last updated');

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
