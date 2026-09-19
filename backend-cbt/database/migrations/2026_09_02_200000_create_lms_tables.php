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
        // 1. Courses (Mata Pelajaran yang diajarkan oleh Guru di Rombel Kelas tertentu)
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('academic_year')->default('2026/2027');
            $table->string('semester')->default('ganjil'); // ganjil, genap
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Learning Materials (Modul Dokumen, Teks Artikel, atau Video Pembelajaran)
        Schema::create('learning_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->string('chapter')->nullable(); // misal: "Bab 1: Eksponen & Logaritma"
            $table->string('type')->default('file'); // file, video, article
            $table->string('file_path')->nullable(); // Dokumen PDF/PPT/Word di storage
            $table->string('video_url')->nullable(); // URL YouTube / streaming video
            $table->longText('content_text')->nullable(); // Teks artikel / rangkuman kaya
            $table->boolean('is_published')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Material Progress (Pelacakan status selesai membaca siswa)
        Schema::create('material_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('learning_materials')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['material_id', 'user_id']);
        });

        // 4. Assignments (Tugas Harian / Proyek Siswa)
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->integer('max_score')->default(100);
            $table->string('file_attachment')->nullable(); // File lembar kerja dari guru
            $table->boolean('is_published')->default(true);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 5. Assignment Submissions (Pengumpulan berkas tugas siswa & penilaian guru)
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path')->nullable(); // Berkas tugas siswa
            $table->text('notes')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->decimal('score', 5, 2)->nullable(); // Nilai angka 0 - 100
            $table->text('feedback')->nullable(); // Catatan perbaikan dari guru
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('graded_at')->nullable();
            $table->string('status')->default('submitted'); // submitted, late, graded, resubmit
            $table->timestamps();

            $table->unique(['assignment_id', 'user_id']);
        });

        // 6. Course Discussions (Forum interaksi kelas per mata pelajaran)
        Schema::create('course_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        // 7. Discussion Replies (Balasan pesan forum diskusi)
        Schema::create('discussion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('course_discussions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_replies');
        Schema::dropIfExists('course_discussions');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('material_progress');
        Schema::dropIfExists('learning_materials');
        Schema::dropIfExists('courses');
    }
};
