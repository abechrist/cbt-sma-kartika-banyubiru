<?php

namespace App\Filament\Student\Pages;

use App\Models\ActivityLog;
use App\Models\ExamAttempt;
use App\Models\ExamSession;
use App\Models\ExamToken;
use App\Services\ActivityLogger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TokenEntry extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Masukkan Token';

    protected static ?string $title = 'Masukkan Token Ujian';

    protected static string $view = 'filament.student.pages.token-entry';

    protected static bool $shouldRegisterNavigation = true;

    public ?string $token = null;

    public ?string $error = null;

    public ?array $sessionInfo = null;

    public function mount(): void
    {
        // Check if there's an active attempt
        $user = Auth::user();
        $activeAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if ($activeAttempt) {
            redirect()->route('student.exam.take', $activeAttempt);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('token')
                    ->label('Token Ujian')
                    ->placeholder('Masukkan token ujian Anda')
                    ->required()
                    ->maxLength(50)
                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()']),
            ]);
    }

    public function validateToken(): void
    {
        $this->error = null;
        $this->sessionInfo = null;

        $user = Auth::user();

        // Check for existing active attempt
        $activeAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if ($activeAttempt) {
            $this->redirectRoute('student.exam.take', ['attempt' => $activeAttempt]);

            return;
        }

        // Find the token
        $examToken = ExamToken::with(['session.exam'])
            ->where('token', strtoupper($this->token))
            ->first();

        if (! $examToken) {
            $this->error = 'Token ujian tidak valid.';
            ActivityLogger::log(ActivityLog::ACTION_TOKEN_REJECTED, 'Token tidak dikenal: '.$this->token, user: $user, metadata: ['token' => $this->token]);

            return;
        }

        if (! $examToken->isValid()) {
            $message = $examToken->is_active ? 'Token ujian telah kedaluwarsa.' : 'Token ujian tidak aktif.';
            $this->error = $message;
            ActivityLogger::log(ActivityLog::ACTION_TOKEN_REJECTED, 'Token tidak valid / kedaluwarsa.', user: $user, metadata: ['token' => $this->token]);

            return;
        }

        $session = $examToken->session;

        if (! $session->isAccessible()) {
            $this->error = 'Sesi ujian belum dibuka atau telah ditutup.';
            ActivityLogger::log(ActivityLog::ACTION_TOKEN_REJECTED, 'Sesi tidak terbuka saat token dipakai.', user: $user, metadata: ['token' => $this->token]);

            return;
        }

        // Check if user already has an attempt for this session
        $existingAttempt = ExamAttempt::where('user_id', $user->id)
            ->where('exam_session_id', $session->id)
            ->whereNotIn('status', ['cancelled'])
            ->first();

        if ($existingAttempt && $existingAttempt->status !== 'not_started') {
            $this->redirectRoute('student.exam.take', ['attempt' => $existingAttempt]);

            return;
        }

        // Store session info for confirmation
        $this->sessionInfo = [
            'token' => $examToken->token,
            'session_id' => $session->id,
            'exam_name' => $session->exam->name,
            'session_name' => $session->name,
            'duration' => $session->exam->duration_minutes,
            'start_at' => $session->start_at->format('d M Y H:i'),
            'end_at' => $session->end_at->format('d M Y H:i'),
            'instructions' => $session->instructions,
        ];
    }

    public function startExam(): void
    {
        if (! $this->sessionInfo) {
            $this->error = 'Silakan masukkan token terlebih dahulu.';

            return;
        }

        $user = Auth::user();
        $session = ExamSession::with('exam')->findOrFail($this->sessionInfo['session_id']);
        $examToken = ExamToken::where('token', $this->sessionInfo['token'])->first();

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

        $this->redirectRoute('student.exam.take', ['attempt' => $attempt->id]);
    }
}
