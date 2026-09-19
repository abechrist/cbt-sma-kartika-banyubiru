<?php

namespace Tests\Feature\Jobs;

use App\Jobs\FinalizeExamResult;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['description' => $roleName]);

        return User::create([
            'name' => 'Test '.ucfirst($roleName),
            'email' => str_replace('_', '.', $roleName).Str::random(4).'@test.com',
            'password' => 'password',
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    private function makeAttempt(): array
    {
        $admin = $this->makeUser(User::ROLE_SUPER_ADMIN);
        $class = StudentClass::create(['name' => 'X-A', 'grade' => 10, 'academic_year' => '2026/2027']);
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK-'.Str::random(3)]);

        $q1 = Question::create([
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'type' => Question::TYPE_PG,
            'question_text' => '2 + 2 = ?',
            'difficulty' => 'easy',
            'score' => 5,
            'is_active' => true,
            'created_by' => $admin->id,
        ]);
        $o1c = QuestionOption::create(['question_id' => $q1->id, 'label' => 'A', 'option_text' => '4', 'is_correct' => true, 'sort_order' => 1]);
        QuestionOption::create(['question_id' => $q1->id, 'label' => 'B', 'option_text' => '5', 'is_correct' => false, 'sort_order' => 2]);

        $exam = Exam::create([
            'name' => 'Ujian Queue',
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'duration_minutes' => 30,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);
        $exam->questions()->attach($q1->id, ['score' => 5]);

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'name' => 'Sesi Queue',
            'start_at' => now()->subMinutes(5),
            'end_at' => now()->addMinutes(30),
            'status' => ExamSession::STATUS_OPEN,
        ]);

        $siswa = $this->makeUser(User::ROLE_SISWA);
        $attempt = ExamAttempt::create([
            'exam_session_id' => $session->id,
            'user_id' => $siswa->id,
            'status' => ExamAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ended_at' => now()->addMinutes(30),
        ]);

        Answer::create([
            'exam_attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'selected_options' => [$o1c->id],
            'answered_at' => now(),
        ]);

        return compact('attempt', 'siswa', 'o1c');
    }

    public function test_finalize_job_creates_completed_result(): void
    {
        $data = $this->makeAttempt();
        $attempt = $data['attempt'];

        FinalizeExamResult::dispatch($attempt->id);

        $result = ExamResult::where('exam_attempt_id', $attempt->id)->first();
        $this->assertNotNull($result);
        $this->assertEquals(5.0, $result->total_score);
        $this->assertEquals('completed', $result->grading_status);
        $this->assertTrue($attempt->refresh()->status !== ExamAttempt::STATUS_IN_PROGRESS);
    }

    public function test_submit_dispatches_finalize_job(): void
    {
        Queue::fake();
        $data = $this->makeAttempt();
        $siswa = $data['siswa'];

        $this->actingAs($siswa)->post(route('exam.submit', $data['attempt']), [
            'exam_attempt_id' => $data['attempt']->id,
        ]);

        Queue::assertPushed(FinalizeExamResult::class);
    }
}
