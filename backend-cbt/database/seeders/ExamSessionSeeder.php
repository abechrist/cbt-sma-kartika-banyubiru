<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ExamToken;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExamSessionSeeder extends Seeder
{
    public function run(): void
    {
        $exam = Exam::where('name', 'PTS Ganjil Matematika Kelas X')->first();

        if (! $exam) {
            return;
        }

        $startAt = now()->addDays(1);
        $endAt = $startAt->copy()->addMinutes($exam->duration_minutes + 60);

        $session = ExamSession::updateOrCreate(
            ['name' => 'Sesi 1 - Gelombang Pagi'],
            [
                'exam_id' => $exam->id,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'room' => 'Lab Komputer 1',
                'max_participants' => 40,
                'status' => ExamSession::STATUS_SCHEDULED,
                'token_prefix' => 'PTS1',
                'instructions' => 'Selamat mengerjakan! Bacalah soal dengan teliti sebelum menjawab.',
                'allow_resume' => true,
                'auto_submit_on_timeout' => true,
            ]
        );

        // Generate 10 sample tokens for the session
        for ($i = 0; $i < 10; $i++) {
            ExamToken::create([
                'exam_session_id' => $session->id,
                'token' => strtoupper('PTS1-'.Str::random(8)),
                'expires_at' => $endAt,
                'is_active' => true,
                'is_single_use' => true,
            ]);
        }
    }
}
