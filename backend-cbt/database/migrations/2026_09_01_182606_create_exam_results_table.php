<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->decimal('total_score', 8, 2)->default(0);
            $table->decimal('max_possible_score', 8, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->integer('correct_count')->default(0);
            $table->integer('incorrect_count')->default(0);
            $table->integer('unanswered_count')->default(0);
            $table->string('grading_status')->default('pending');
            $table->dateTime('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique('exam_attempt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
