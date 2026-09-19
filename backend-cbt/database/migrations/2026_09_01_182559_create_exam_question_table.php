<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->integer('order_in_exam')->default(0);
            $table->decimal('score', 5, 2)->default(1);
            $table->timestamps();
            $table->unique(['exam_id', 'question_id']);
            $table->index(['exam_id', 'order_in_exam']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_question');
    }
};
