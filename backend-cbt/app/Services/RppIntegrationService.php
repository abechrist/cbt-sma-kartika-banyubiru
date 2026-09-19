<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Exam;
use App\Models\LearningMaterial;
use App\Models\Question;
use App\Models\Rpp;
use App\Models\RppAssessment;
use App\Models\RppMaterial;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RppIntegrationService
{
    /**
     * Import RPP from JSON and create related LMS/CBT resources
     */
    public function importRpp(array $rppData): Rpp
    {
        return DB::transaction(function () use ($rppData) {
            // Extract RPP metadata
            $metadata = $rppData['rpp_metadata'];

            // Get or create Subject
            $subject = Subject::firstOrCreate(
                ['name' => $metadata['mata_pelajaran']],
                [
                    'code' => strtoupper(substr($metadata['mata_pelajaran'], 0, 3)),
                    'description' => "Subject for {$metadata['mata_pelajaran']}",
                    'is_active' => true,
                ]
            );

            // Get or create Class (we'll need to map from kelas)
            $class = $this->getOrCreateClass($metadata['kelas']);

            // Get or create Teacher (we'll use a default or need to specify)
            $teacher = User::where('role_id', 2)->first() ?? User::first(); // Assuming role_id 2 is teacher

            // Create RPP record
            $rpp = Rpp::create([
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'teacher_id' => $teacher->id,
                'academic_year' => '2026/2027', // Default, could be from input
                'semester' => 'Ganjil', // Default, could be from input
                'topic' => $metadata['topik_utama'],
                'time_allocation' => $metadata['alokasi_waktu'],
                'rpp_data' => $rppData,
                'status' => 'draft',
            ]);

            // Create LMS Materials (Jembatan 1: Konten)
            $this->createLmsMaterials($rpp, $rppData['lms_integration']['pertemuan_list']);

            // Create CBT Assessments (Jembatan 2: Asesmen)
            $this->createCbtAssessments($rpp, $rppData['cbt_integration']);

            // Update RPP status
            $rpp->update(['status' => 'integrated']);

            return $rpp;
        });
    }

    /**
     * Get or create class based on kelas string
     */
    private function getOrCreateClass(string $kelas): StudentClass
    {
        $grade = null;
        $name = $kelas;

        // Parse kelas like "10" or "X" or "10 MIPA"
        if (preg_match('/^(\d+)/', $kelas, $matches)) {
            $grade = (int) $matches[1];
            $name = "Kelas {$grade}";
        }

        return StudentClass::firstOrCreate(
            ['name' => $name, 'grade' => $grade ?? '10'],
            [
                'academic_year' => '2026/2027',
                'description' => 'Auto-created for RPP integration',
                'is_active' => true,
            ]
        );
    }

    /**
     * Create LMS Materials from RPP data
     */
    private function createLmsMaterials(Rpp $rpp, array $pertemuanList): void
    {
        // Get or create Course for this RPP
        $course = Course::firstOrCreate(
            [
                'subject_id' => $rpp->subject_id,
                'class_id' => $rpp->class_id,
                'teacher_id' => $rpp->teacher_id,
                'academic_year' => $rpp->academic_year,
                'semester' => $rpp->semester,
            ],
            [
                'description' => "Course for {$rpp->subject->name} - {$rpp->topic}",
                'is_active' => true,
            ]
        );

        foreach ($pertemuanList as $pertemuan) {
            // Create Learning Material
            $material = LearningMaterial::create([
                'course_id' => $course->id,
                'title' => $pertemuan['judul_topik'],
                'chapter' => "Pertemuan {$pertemuan['pertemuan_ke']}",
                'type' => 'modul',
                'content_text' => $pertemuan['deskripsi_aktivitas'],
                'is_published' => true,
                'created_by' => $rpp->teacher_id,
            ]);

            // Create bridge record
            RppMaterial::create([
                'rpp_id' => $rpp->id,
                'learning_material_id' => $material->id,
                'pertemuan_ke' => $pertemuan['pertemuan_ke'],
                'judul_topik' => $pertemuan['judul_topik'],
                'deskripsi_aktivitas' => $pertemuan['deskripsi_aktivitas'],
                'rekomendasi_bahan_ajar' => $pertemuan['rekomendasi_bahan_ajar'],
            ]);
        }
    }

    /**
     * Create CBT Assessments from RPP data
     */
    private function createCbtAssessments(Rpp $rpp, array $cbtData): void
    {
        $assessmentType = $cbtData['assessment_type'] === 'Sumatif' ? Exam::STATUS_PUBLISHED : Exam::STATUS_DRAFT;

        // Create Exam
        $exam = Exam::create([
            'subject_id' => $rpp->subject_id,
            'name' => "Ujian {$rpp->subject->name} - {$rpp->topic}",
            'description' => "Exam generated from RPP: {$rpp->topic}",
            'duration_minutes' => 60, // Default
            'status' => $assessmentType,
            'randomize_questions' => true,
            'show_result_after' => true,
            'created_by' => $rpp->teacher_id,
        ]);

        // Link exam to RPP via assessments for each TP
        foreach ($cbtData['tujuan_pembelajaran_mapped'] as $tp) {
            $assessment = RppAssessment::create([
                'rpp_id' => $rpp->id,
                'exam_id' => $exam->id,
                'tp_id' => $tp['tp_id'],
                'deskripsi_tp' => $tp['deskripsi_tp'],
                'jumlah_soal_direkomendasikan' => $tp['cbt_setup']['jumlah_soal_direkomendasikan'],
                'tipe_soal' => $tp['cbt_setup']['tipe_soal'],
                'tingkat_kesulitan' => $tp['cbt_setup']['tingkat_kesulitan'],
                'kata_kunci_indokator_soal' => $tp['cbt_setup']['kata_kunci_indokator_soal'],
            ]);

            // TODO: Generate actual questions based on kata_kunci_indokator_soal
            // For now, we'll just note that questions need to be created separately
            // In a real system, this would either:
            // 1. Pull existing questions from question bank matching the keywords
            // 2. Generate new questions using AI/templates
            // 3. Create placeholder questions for teacher to fill in
        }
    }
}
