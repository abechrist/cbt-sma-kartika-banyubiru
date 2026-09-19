<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $subject = Subject::where('code', 'MTK')->first();
        $guru = User::where('email', 'budi@kartika.sch.id')->first();

        if (! $subject || ! $guru) {
            return;
        }

        $exam = Exam::updateOrCreate(
            ['name' => 'PTS Ganjil Matematika Kelas X'],
            [
                'subject_id' => $subject->id,
                'description' => 'Penilaian Tengah Semester Ganjil Mata Pelajaran Matematika Kelas X',
                'duration_minutes' => 60,
                'randomize_questions' => true,
                'randomize_options' => true,
                'allow_back' => true,
                'show_result_after' => true,
                'status' => Exam::STATUS_PUBLISHED,
                'created_by' => $guru->id,
            ]
        );

        $questions = Question::where('subject_id', $subject->id)
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $exam->questions()->sync(
            $questions->mapWithKeys(function ($q, $index) {
                return [$q->id => ['order_in_exam' => $index + 1, 'score' => $q->score]];
            })->toArray()
        );
    }
}
