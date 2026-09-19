<?php

namespace App\Http\Controllers\Examination;

use App\Events\ActivityLogged;
use App\Events\AttemptHeartbeat;
use App\Events\AttemptStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Examination\SaveAnswerRequest;
use App\Http\Requests\Examination\SubmitExamRequest;
use App\Http\Requests\Examination\ValidateTokenRequest;
use App\Jobs\FinalizeExamResult;
use App\Models\ActivityLog;
use App\Models\ExamAttempt;
use App\Models\ExamToken;
use App\Models\Question;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentExamController extends Controller
{
    public function showTokenForm()
    {
        return view('exam.token');
    }

    public function validateToken(ValidateTokenRequest $request)
    {
        $user = Auth::user();
        $token = $request->input('token');
        $examToken = ExamToken::with(['session.exam'])
            ->where('token', $token)
            ->first();

        if (! $examToken) {
            ActivityLogger::log(ActivityLog::ACTION_TOKEN_REJECTED, 'Token tidak dikenal: '.$token, user: $user, metadata: ['token' => $token]);

            return back()->withErrors(['token' => 'Token ujian tidak valid.'])->withInput();
        }

        if (! $examToken->isValid()) {
            $message = $examToken->is_active ? 'Token ujian telah kedaluwarsa.' : 'Token ujian tidak aktif.';
            ActivityLogger::log(ActivityLog::ACTION_TOKEN_REJECTED, 'Token tidak valid / kedaluwarsa.', user: $user, metadata: ['token' => $token]);

            return back()->withErrors(['token' => $message])->withInput();
        }

        $session = $examToken->session;

        if (! $session->isAccessible()) {
            ActivityLogger::log(ActivityLog::ACTION_TOKEN_REJECTED, 'Sesi tidak terbuka saat token dipakai.', user: $user, metadata: ['token' => $token]);

            return back()->withErrors(['token' => 'Sesi ujian belum dibuka atau telah ditutup.'])->withInput();
        }

        return redirect()->route('exam.start', ['token' => $token]);
    }

    public function start(Request $request)
    {
        $token = (string) $request->route('token');
        $examToken = ExamToken::with(['session.exam.questions.options'])
            ->where('token', $token)
            ->first();

        if (! $examToken || ! $examToken->isValid()) {
            ActivityLogger::log(ActivityLog::ACTION_TOKEN_REJECTED, 'Token ditolak saat memulai ujian.', user: Auth::user(), metadata: ['token' => $token]);

            return redirect()->route('exam.token')->withErrors(['token' => 'Token tidak valid atau kedaluwarsa.']);
        }

        $user = Auth::user();
        $session = $examToken->session;

        DB::transaction(function () use ($user, $session, $examToken, &$attempt) {
            $attempt = ExamAttempt::firstOrCreate(
                [
                    'exam_session_id' => $session->id,
                    'user_id' => $user->id,
                ],
                [
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'ended_at' => $session->end_at,
                    'last_seen_at' => now(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            );

            $resuming = false;
            if ($attempt->wasRecentlyCreated || $attempt->status === 'not_started') {
                $attempt->update([
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'ended_at' => $session->end_at,
                    'last_seen_at' => now(),
                    'is_resumed' => false,
                ]);
            } else {
                $resuming = $attempt->status === 'in_progress';
                $attempt->update([
                    'status' => 'in_progress',
                    'ended_at' => $session->end_at,
                    'last_seen_at' => now(),
                    'is_resumed' => true,
                ]);
            }

            if ($resuming) {
                ActivityLogger::log(ActivityLog::ACTION_SESSION_RESUMED, 'Siswa melanjutkan ujian.', $attempt, $user);
            }
            ActivityLogger::log(ActivityLog::ACTION_EXAM_STARTED, 'Ujian dimulai.', $attempt, $user);

            $examToken->markUsed();
        });

        $attempt->load('user');
        broadcast(new AttemptStatusUpdated($attempt));

        return redirect()->route('exam.take', ['attempt' => $attempt->id]);
    }

    public function take(ExamAttempt $attempt)
    {
        $this->authorize('view', $attempt);

        $attempt->load(['session.exam.questions.options']);

        $questions = $attempt->session->exam->questions;

        if ($attempt->session->exam->randomize_questions) {
            $questions = $questions->shuffle();
        }

        foreach ($questions as $index => $question) {
            if ($attempt->session->exam->randomize_options) {
                $question->options = $question->options->shuffle()->values();
            }
        }

        $attempt->load('answers');

        return view('exam.take', compact('attempt', 'questions'));
    }

    public function saveAnswer(SaveAnswerRequest $request)
    {
        $attempt = ExamAttempt::findOrFail($request->exam_attempt_id);

        if ($attempt->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Ujian tidak dalam status berlangsung.'], 403);
        }

        if ($attempt->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses.'], 403);
        }

        $question = Question::findOrFail($request->question_id);

        $answer = $attempt->answers()->updateOrCreate(
            ['question_id' => $question->id],
            [
                'answer_text' => $request->answer_text,
                'selected_options' => $request->selected_options ?? [],
                'is_flagged' => $request->boolean('is_flagged'),
                'answered_at' => now(),
            ]
        );

        $attempt->update(['last_seen_at' => now()]);

        ActivityLogger::log(ActivityLog::ACTION_ANSWER_SAVED, 'Jawaban untuk soal #'.$question->id.' disimpan.', $attempt);

        broadcast(new AttemptStatusUpdated($attempt));

        return response()->json(['success' => true, 'message' => 'Jawaban berhasil disimpan.']);
    }

    public function heartbeat(Request $request)
    {
        $attempt = ExamAttempt::findOrFail($request->exam_attempt_id);

        if ($attempt->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses.'], 403);
        }

        if ($attempt->status === 'in_progress') {
            $attempt->update(['last_seen_at' => now()]);
            broadcast(new AttemptHeartbeat($attempt));
        }

        return response()->json([
            'success' => true,
            'server_time' => now()->toIso8601String(),
            'time_remaining' => $attempt->getTimeRemaining(),
            'status' => $attempt->status,
        ]);
    }

    public function logActivityEvent(Request $request)
    {
        $request->validate([
            'exam_attempt_id' => 'required|integer|exists:exam_attempts,id',
            'event' => 'required|string|max:50|in:tab_blur,tab_focus,window_blur,window_focus,reconnect,paste,resize',
            'suspicious' => 'boolean',
        ]);

        $attempt = ExamAttempt::findOrFail($request->exam_attempt_id);

        if ($attempt->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses.'], 403);
        }

        if ($attempt->status !== 'in_progress') {
            return response()->json(['success' => false, 'message' => 'Ujian tidak berlangsung.'], 409);
        }

        $suspicious = (bool) $request->input('suspicious', false);

        $description = match ($request->event) {
            'tab_blur' => 'Berpindah ke tab lain.',
            'tab_focus' => 'Kembali ke tab ujian.',
            'window_blur' => 'Kehilangan fokus jendela browser.',
            'window_focus' => 'Kembali mengaktifkan jendela browser.',
            'reconnect' => 'Koneksi terputus lalu tersambung kembali.',
            'paste' => 'Paste teks terdeteksi.',
            default => $request->event,
        };

        $log = ActivityLogger::log(
            ActivityLog::ACTION_SUSPICIOUS_ACTIVITY,
            $description,
            $attempt,
            Auth::user(),
            ['event' => $request->event, 'suspicious' => $suspicious],
        );

        if ($suspicious) {
            $attempt->increment('suspicious_flags');
            $attempt->refresh();
            broadcast(new AttemptStatusUpdated($attempt));
        }
        broadcast(new ActivityLogged($log));

        return response()->json(['success' => true]);
    }

    public function submit(SubmitExamRequest $request)
    {
        $attempt = ExamAttempt::findOrFail($request->exam_attempt_id);

        if ($attempt->user_id !== Auth::id()) {
            return redirect()->route('exam.take', $attempt)->withErrors(['submit' => 'Tidak memiliki akses.']);
        }

        if ($attempt->status === ExamAttempt::STATUS_CANCELLED) {
            return response()->json(['success' => false, 'message' => 'Ujian telah dibatalkan.'], 403);
        }

        if ($attempt->status === 'submitted' || $attempt->status === 'auto_submitted') {
            return redirect()->route('exam.result', $attempt);
        }

        if ($attempt->ended_at && $attempt->ended_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'Ujian telah berakhir.'], 403);
        }

        DB::transaction(function () use ($attempt) {
            $attempt->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'ended_at' => now(),
            ]);

            $this->gradeExam($attempt);
            ActivityLogger::log(ActivityLog::ACTION_EXAM_SUBMITTED, 'Ujian dikumpulkan secara manual.', $attempt);
        });

        $attempt->load('user');

        // Reconciliation + monitoring refresh runs on the background queue so
        // the student's page redirect is never blocked by derived work.
        FinalizeExamResult::dispatch($attempt->id);

        return redirect()->route('exam.result', $attempt);
    }

    public function autoSubmit(ExamAttempt $attempt)
    {
        if ($attempt->status === 'submitted' || $attempt->status === 'auto_submitted') {
            return;
        }

        DB::transaction(function () use ($attempt) {
            $attempt->update([
                'status' => 'auto_submitted',
                'submitted_at' => now(),
                'ended_at' => now(),
            ]);

            $this->gradeExam($attempt);
            ActivityLogger::log(ActivityLog::ACTION_AUTO_SUBMITTED, 'Ujian dikumpulkan otomatis karena waktu habis.', $attempt);
        });

        $attempt->load('user');

        FinalizeExamResult::dispatch($attempt->id);
    }

    public function result(ExamAttempt $attempt)
    {
        $attempt->load(['result', 'session.exam']);

        return view('exam.result', compact('attempt'));
    }

    private function gradeExam(ExamAttempt $attempt)
    {
        $exam = $attempt->session->exam;
        $totalScore = 0;
        $maxScore = 0;
        $correctCount = 0;
        $incorrectCount = 0;
        $unansweredCount = 0;
        $pendingEsaiCount = 0;

        foreach ($exam->questions as $question) {
            $maxScore += $question->pivot->score ?? $question->score;

            $answer = $attempt->answers()->where('question_id', $question->id)->first();

            if (! $answer) {
                $unansweredCount++;

                continue;
            }

            if ($question->isAutoGradable()) {
                $isCorrect = $answer->isCorrect();
                $score = $isCorrect ? ($question->pivot->score ?? $question->score) : 0;

                $answer->update(['score' => $score]);

                if ($isCorrect) {
                    $correctCount++;
                    $totalScore += $score;
                } else {
                    $incorrectCount++;
                }
            } else {
                // Esai - perlu grading manual
                if (($answer->score ?? null) !== null) {
                    $totalScore += $answer->score;
                } else {
                    $pendingEsaiCount++;
                }
            }
        }

        $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;

        $result = $attempt->result()->updateOrCreate(
            ['exam_attempt_id' => $attempt->id],
            [
                'total_score' => $totalScore,
                'max_possible_score' => $maxScore,
                'percentage' => $percentage,
                'correct_count' => $correctCount,
                'incorrect_count' => $incorrectCount,
                'unanswered_count' => $unansweredCount + $pendingEsaiCount,
                'grading_status' => $pendingEsaiCount > 0 ? 'partial' : 'completed',
            ]
        );
    }
}
