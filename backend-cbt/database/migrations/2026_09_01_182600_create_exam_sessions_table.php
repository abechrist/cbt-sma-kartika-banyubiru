<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->string('name');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('room')->nullable();
            $table->integer('max_participants')->default(40);
            $table->string('status')->default('scheduled');
            $table->string('token_prefix')->length(4)->default('EXAM');
            $table->text('instructions')->nullable();
            $table->boolean('allow_resume')->default(true);
            $table->boolean('auto_submit_on_timeout')->default(true);
            $table->timestamps();
            $table->index(['exam_id', 'start_at']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
