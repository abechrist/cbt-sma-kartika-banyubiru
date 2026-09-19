<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseDiscussion;
use App\Models\DiscussionReply;
use App\Models\LearningMaterial;
use App\Models\MaterialProgress;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class LmsSeeder extends Seeder
{
    public function run(): void
    {
        $budi = User::where('email', 'budi@kartika.sch.id')->first();
        $indah = User::where('email', 'indah@kartika.sch.id')->first();
        $math = Subject::where('name', 'Matematika')->first();
        $indo = Subject::where('name', 'Bahasa Indonesia')->first();
        $classXA = StudentClass::where('name', 'X-A')->first();
        $classXB = StudentClass::where('name', 'X-B')->first();
        $siswa1 = User::where('email', 'siswa1@kartika.sch.id')->first();
        $siswa2 = User::where('email', 'siswa2@kartika.sch.id')->first();

        if (! $budi || ! $math || ! $classXA) {
            return;
        }

        // 1. Kursus Matematika Kelas X-A (Guru: Budi)
        $courseMath = Course::firstOrCreate([
            'subject_id' => $math->id,
            'class_id' => $classXA->id,
            'teacher_id' => $budi->id,
        ], [
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'description' => 'Mata pelajaran Matematika Wajib Kelas X Kurikulum Merdeka. Membahas Eksponen, Logaritma, Vektor, dan Trigonometri.',
            'is_active' => true,
        ]);

        // 2. Kursus Bahasa Indonesia Kelas X-A (Guru: Indah)
        if ($indah && $indo) {
            $courseIndo = Course::firstOrCreate([
                'subject_id' => $indo->id,
                'class_id' => $classXA->id,
                'teacher_id' => $indah->id,
            ], [
                'academic_year' => '2026/2027',
                'semester' => 'ganjil',
                'description' => 'Mata pelajaran Bahasa Indonesia Kelas X. Membahas Teks Laporan Hasil Observasi (LHO), Teks Anekdot, dan Hikayat.',
                'is_active' => true,
            ]);
        }

        // 3. Materi Pembelajaran Matematika
        $mat1 = LearningMaterial::firstOrCreate([
            'course_id' => $courseMath->id,
            'title' => 'Konsep Dasar Eksponen dan Bilangan Berpangkat',
        ], [
            'chapter' => 'Bab 1: Eksponen & Logaritma',
            'type' => 'article',
            'content_text' => 'Eksponen atau bilangan berpangkat adalah bentuk perkalian berulang dari suatu bilangan dengan bilangan itu sendiri sebanyak n kali. Sifat-sifat eksponen: 1) a^m * a^n = a^(m+n), 2) a^m / a^n = a^(m-n), 3) (a^m)^n = a^(m*n). Pastikan memahami sifat dasar ini sebelum masuk ke materi persamaan fungsi eksponensial.',
            'is_published' => true,
            'created_by' => $budi->id,
        ]);

        $mat2 = LearningMaterial::firstOrCreate([
            'course_id' => $courseMath->id,
            'title' => 'Video Pembelajaran: Penyelesaian Soal Cerita Persamaan Eksponen',
        ], [
            'chapter' => 'Bab 1: Eksponen & Logaritma',
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'content_text' => 'Simak video tutorial pembahasan soal cerita pemodelan pertumbuhan bakteri dan peluruhan zat radioaktif menggunakan formula eksponen.',
            'is_published' => true,
            'created_by' => $budi->id,
        ]);

        $mat3 = LearningMaterial::firstOrCreate([
            'course_id' => $courseMath->id,
            'title' => 'Modul PDF: Rangkuman Logaritma dan Sifat-sifat Operasi',
        ], [
            'chapter' => 'Bab 1: Eksponen & Logaritma',
            'type' => 'file',
            'file_path' => null,
            'content_text' => 'Modul ajar kurikulum merdeka Bab 1 Logaritma tingkat lanjut SMA Kartika III-1 Banyubiru.',
            'is_published' => true,
            'created_by' => $budi->id,
        ]);

        // 4. Progress Membaca Siswa 1
        if ($siswa1) {
            MaterialProgress::firstOrCreate([
                'material_id' => $mat1->id,
                'user_id' => $siswa1->id,
            ], [
                'completed_at' => now()->subDay(),
            ]);
        }

        // 5. Tugas Harian Matematika
        $assign1 = Assignment::firstOrCreate([
            'course_id' => $courseMath->id,
            'title' => 'Tugas Mandiri 1: Latihan Soal Sifat-Sifat Eksponen',
        ], [
            'instructions' => "Kerjakan 5 soal latihan sifat-sifat eksponen di buku catatan matematika Anda.\n1. Sederhanakan bentuk (2^3 * 2^4) / 2^2\n2. Tentukan nilai x jika 3^(2x-1) = 27\n3. Sederhanakan ((x^2 * y^-3) / (x^-1 * y^2))^2\nFoto lembar jawaban Anda dengan jelas atau ubah ke format PDF, lalu unggah melalui tombol pengumpulan tugas di bawah.",
            'due_date' => now()->addDays(5)->setTime(23, 59),
            'max_score' => 100,
            'is_published' => true,
            'created_by' => $budi->id,
        ]);

        // 6. Submission Tugas oleh Siswa 1
        if ($siswa1) {
            AssignmentSubmission::firstOrCreate([
                'assignment_id' => $assign1->id,
                'user_id' => $siswa1->id,
            ], [
                'notes' => 'Berikut lembar pengerjaan tugas 1 sifat eksponen saya Pak Budi. Terima kasih.',
                'submitted_at' => now()->subHours(6),
                'score' => 95.00,
                'feedback' => 'Sangat bagus dan rapi! Langkah penyelesaian nomor 2 sudah tepat. Pertahankan!',
                'graded_by' => $budi->id,
                'graded_at' => now()->subHours(2),
                'status' => 'graded',
            ]);
        }

        // 7. Forum Diskusi
        $disc1 = CourseDiscussion::firstOrCreate([
            'course_id' => $courseMath->id,
            'title' => 'Diskusi Materi: Soal Nomor 3 Bagian Pangkat Negatif',
        ], [
            'user_id' => $siswa1 ? $siswa1->id : $budi->id,
            'content' => 'Selamat siang Pak Budi dan teman-teman, untuk soal nomor 3 apakah pangkat negatifnya perlu dijadikan pecahan positif terlebih dahulu di dalam kurung atau setelah dipangkatkan 2?',
            'is_pinned' => true,
        ]);

        DiscussionReply::firstOrCreate([
            'discussion_id' => $disc1->id,
            'user_id' => $budi->id,
        ], [
            'content' => 'Bisa diselesaikan dengan kedua cara, Ahmad. Namun lebih mudah dan meminimalkan kesalahan hitung jika pangkat negatif di dalam kurung disederhanakan terlebih dahulu.',
        ]);
    }
}
