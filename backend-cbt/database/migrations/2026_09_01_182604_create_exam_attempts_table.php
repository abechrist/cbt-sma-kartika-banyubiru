<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'auto_submitted', 'expired', 'cancelled'])->default('not_started');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->integer('current_question_order')->default(0);
            $table->boolean('is_resumed')->default(false);
            $table->timestamps();
            $table->index(['exam_session_id', 'user_id']);
            $table->index(['status']);
            $table->index(['exam_session_id', 'status']);
            $table->unique(['exam_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
