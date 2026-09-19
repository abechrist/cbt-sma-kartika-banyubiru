<?php

use App\Filament\Student\Pages\ExamResult;
use App\Filament\Student\Pages\StudentDashboard;
use App\Filament\Student\Pages\TakeExam;
use App\Filament\Student\Pages\TokenEntry;
use App\Http\Controllers\Academic\ClassController;
use App\Http\Controllers\Academic\SubjectController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Examination\ExamController;
use App\Http\Controllers\Examination\ExamSessionController;
use App\Http\Controllers\Examination\ExamTokenController;
use App\Http\Controllers\Examination\StudentExamController;
use App\Http\Controllers\Grading\GradingController;
use App\Http\Controllers\ImportExport\DapodikController;
use App\Http\Controllers\ImportExport\ImportExportController;
use App\Http\Controllers\Kiosk\KioskController;
use App\Http\Controllers\Lms\AssignmentController;
use App\Http\Controllers\Lms\CourseController;
use App\Http\Controllers\Lms\DiscussionController;
use App\Http\Controllers\Lms\LearningMaterialController;
use App\Http\Controllers\Monitoring\MonitoringController;
use App\Http\Controllers\Question\QuestionController;
use App\Http\Controllers\Result\ItemAnalysisController;
use App\Http\Controllers\Result\ResultController;
use App\Http\Controllers\Rpp\RppController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('portal');
Route::get('/portal', fn () => view('welcome'));

// Authentication
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management (Super Admin & Admin)
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('classes', ClassController::class);
        Route::resource('subjects', SubjectController::class);
    });

    // Questions (Guru, Admin, Super Admin)
    Route::middleware('role:super_admin,admin,guru')->group(function () {
        Route::resource('questions', QuestionController::class);
        Route::resource('exams', ExamController::class);
        Route::post('exams/{exam}/publish', [ExamController::class, 'publish'])->name('exams.publish');

        // RPP Management
        Route::resource('rpps', RppController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        Route::get('rpps/{rpp}/integration-status', [RppController::class, 'getIntegrationStatus'])->name('rpps.integration-status');

        // Import/Export - Guru scope (soal, export)
        Route::get('import-export', [ImportExportController::class, 'index'])->name('import_export');
        Route::post('import/question', [ImportExportController::class, 'importQuestion'])->name('import.question');
        Route::get('export/question', [ImportExportController::class, 'exportQuestion'])->name('export.question');

        // Analisis Butir Soal
        Route::get('item-analysis', [ItemAnalysisController::class, 'index'])->name('item-analysis.index');
        Route::get('item-analysis/{exam}', [ItemAnalysisController::class, 'show'])->name('item-analysis.show');
    });

    // Import/Export - Admin scope (users/kelas/results)
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::post('import/siswa', [ImportExportController::class, 'importSiswa'])->name('import.siswa');
        Route::post('import/guru', [ImportExportController::class, 'importGuru'])->name('import.guru');
        Route::post('import/kelas', [ImportExportController::class, 'importKelas'])->name('import.kelas');
        Route::get('export/siswa', [ImportExportController::class, 'exportSiswa'])->name('export.siswa');
        Route::get('export/guru', [ImportExportController::class, 'exportGuru'])->name('export.guru');
        Route::get('export/kelas', [ImportExportController::class, 'exportKelas'])->name('export.kelas');
        Route::get('export/participant', [ImportExportController::class, 'exportParticipant'])->name('export.participant');
        Route::get('export/rekap', [ImportExportController::class, 'exportRekapNilai'])->name('export.rekap_nilai');
        Route::get('export/laporan', [ImportExportController::class, 'exportLaporan'])->name('export.laporan');
        Route::get('dapodik/template', [DapodikController::class, 'template'])->name('dapodik.template');
        Route::post('dapodik/import', [DapodikController::class, 'import'])->name('dapodik.import');
        Route::get('dapodik/export', [DapodikController::class, 'export'])->name('dapodik.export');
    });

    // Exam Sessions & Tokens (Admin, Super Admin)
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::resource('sessions', ExamSessionController::class);
        Route::post('sessions/{session}/open', [ExamSessionController::class, 'open'])->name('sessions.open');
        Route::post('sessions/{session}/close', [ExamSessionController::class, 'close'])->name('sessions.close');
        Route::post('sessions/{session}/generate-tokens', [ExamSessionController::class, 'generateTokens'])->name('sessions.generate-tokens');
        Route::get('sessions/{session}/print-tokens', [ExamSessionController::class, 'printTokens'])->name('sessions.print-tokens');
        Route::resource('tokens', ExamTokenController::class);
        Route::post('tokens/validate', [ExamTokenController::class, 'validate'])->name('tokens.validate');
    });

    // Student Exam
    Route::middleware('role:super_admin,admin,guru,siswa,proktor,kepala_sekolah,wali_kelas')->group(function () {
        // Legacy routes - kept for backwards compatibility
        Route::get('exam/token', [StudentExamController::class, 'showTokenForm'])->name('exam.token');
        Route::post('exam/token/validate', [StudentExamController::class, 'validateToken'])->name('exam.token.validate');
        Route::get('exam/start/{token}', [StudentExamController::class, 'start'])->name('exam.start');
        Route::get('exam/take/{attempt}', [StudentExamController::class, 'take'])->name('exam.take');
        Route::post('exam/answer/save', [StudentExamController::class, 'saveAnswer'])->name('exam.answer.save');
        Route::post('exam/heartbeat', [StudentExamController::class, 'heartbeat'])->name('exam.heartbeat');
        Route::post('exam/activity', [StudentExamController::class, 'logActivityEvent'])->name('exam.activity');
        Route::post('exam/submit/{attempt}', [StudentExamController::class, 'submit'])->name('exam.submit');
        Route::get('exam/result/{attempt}', [StudentExamController::class, 'result'])->name('exam.result');
    });

    // Student Panel Routes
    Route::middleware('role:siswa,admin,super_admin,guru,proktor,kepala_sekolah,wali_kelas')->group(function () {
        Route::prefix('student')->name('student.')->group(function () {
            Route::get('/dashboard', [StudentDashboard::class, 'mount'])
                ->name('dashboard');
            Route::get('/token', [TokenEntry::class, 'mount'])
                ->name('exam.token');
            Route::post('/token/validate', [TokenEntry::class, 'validateToken'])
                ->name('exam.token.validate');
            Route::post('/exam/start', [TokenEntry::class, 'startExam'])
                ->name('exam.start');
            Route::get('/exam/take/{attempt}', [TakeExam::class, 'mount'])
                ->name('exam.take');
            Route::post('/exam/heartbeat', [TakeExam::class, 'heartbeat'])
                ->name('exam.heartbeat');
            Route::post('/exam/save', [TakeExam::class, 'saveCurrentAnswer'])
                ->name('exam.answer.save');
            Route::post('/exam/submit', [TakeExam::class, 'submitExam'])
                ->name('exam.submit');
            Route::get('/exam/result/{attempt}', [ExamResult::class, 'mount'])
                ->name('exam.result');
        });
    });

    // Results
    Route::middleware('role:super_admin,admin,guru,kepala_sekolah,wali_kelas')->group(function () {
        Route::resource('results', ResultController::class)->only(['index', 'show']);
        Route::get('results/exam/{exam}', [ResultController::class, 'byExam'])->name('results.by-exam');
        Route::get('results/class/{class}', [ResultController::class, 'byClass'])->name('results.by-class');
        Route::get('results/student/{student}', [ResultController::class, 'byStudent'])->name('results.by-student');
    });

    Route::middleware('role:super_admin,admin,guru')->group(function () {
        Route::get('grading', [GradingController::class, 'index'])->name('grading.index');
        Route::get('grading/{result}/grade', [GradingController::class, 'grade'])->name('grading.grade');
        Route::post('grading/{result}', [GradingController::class, 'update'])->name('grading.update');
        Route::get('grading/{result}', [GradingController::class, 'show'])->name('grading.show');
        Route::post('grading/{result}/regrade', [GradingController::class, 'regrade'])->name('grading.regrade');
    });

    // Monitoring (Proktor, Admin, Super Admin)
    Route::middleware('role:super_admin,admin,proktor')->group(function () {
        Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('monitoring/{session}', [MonitoringController::class, 'session'])->name('monitoring.session');
        Route::post('monitoring/{attempt}/reset', [MonitoringController::class, 'resetAttempt'])->name('monitoring.reset');
        Route::get('kiosk/launch', [KioskController::class, 'launch'])->name('kiosk.launch');
        Route::get('kiosk/{attempt}', [KioskController::class, 'show'])->name('kiosk.show');
    });

    // LMS (Learning Management System)
    Route::prefix('lms')->name('lms.')->group(function () {
        // Courses
        Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
        Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
        Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');

        // Learning Materials
        Route::get('courses/{course}/materials/{material}', [LearningMaterialController::class, 'show'])->name('materials.show');
        Route::post('courses/{course}/materials', [LearningMaterialController::class, 'store'])->name('materials.store');
        Route::post('courses/{course}/materials/{material}/toggle-complete', [LearningMaterialController::class, 'toggleComplete'])->name('materials.toggle-complete');
        Route::delete('courses/{course}/materials/{material}', [LearningMaterialController::class, 'destroy'])->name('materials.destroy');

        // Assignments
        Route::get('courses/{course}/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
        Route::post('courses/{course}/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::post('courses/{course}/assignments/{assignment}/submit', [AssignmentController::class, 'submit'])->name('assignments.submit');
        Route::post('courses/{course}/assignments/{assignment}/grade/{submission}', [AssignmentController::class, 'grade'])->name('assignments.grade');

        // Discussions
        Route::post('courses/{course}/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
        Route::post('courses/{course}/discussions/{discussion}/reply', [DiscussionController::class, 'reply'])->name('discussions.reply');
    });
});
