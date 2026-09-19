<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained('exam_sessions')->onDelete('cascade');
            $table->string('token')->unique();
            $table->dateTime('expires_at');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_single_use')->default(false);
            $table->integer('used_count')->default(0);
            $table->timestamps();
            $table->index(['exam_session_id', 'token']);
            $table->index(['is_active', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_tokens');
    }
};
