<?php

use App\Http\Controllers\Api\Academic\ClassController;
use App\Http\Controllers\Api\Academic\SubjectController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\Examination\ExamController;
use App\Http\Controllers\Api\Examination\ExamSessionController;
use App\Http\Controllers\Api\Examination\ExamTokenController;
use App\Http\Controllers\Api\Examination\StudentExamController;
use App\Http\Controllers\Api\Grading\GradingController;
use App\Http\Controllers\Api\ImportExport\DapodikController;
use App\Http\Controllers\Api\ImportExport\ImportExportController;
use App\Http\Controllers\Api\ItemAnalysisController;
use App\Http\Controllers\Api\Kiosk\KioskController;
use App\Http\Controllers\Api\Lms\AssignmentController;
use App\Http\Controllers\Api\Lms\CourseController;
use App\Http\Controllers\Api\Lms\DiscussionController;
use App\Http\Controllers\Api\Lms\LearningMaterialController;
use App\Http\Controllers\Api\Monitoring\MonitoringController;
use App\Http\Controllers\Api\Question\QuestionBankController;
use App\Http\Controllers\Api\Question\QuestionController;
use App\Http\Controllers\Api\Result\ResultController;
use App\Http\Controllers\Api\Rpp\RppController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API routes for CBT SMA Kartika III-1 Banyubiru.
| All routes are prefixed with /api/v1/
|
*/

// Public routes (no authentication required)
Route::prefix('v1')->name('api.')->group(function () {

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/login', [LoginController::class, 'login']);
        Route::post('/register', [RegisterController::class, 'register']);
    });

    // Kiosk (public access for exam launch)
    Route::prefix('kiosk')->group(function () {
        Route::get('/launch', [KioskController::class, 'launch']);
    });

    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {

        // Logout
        Route::post('/auth/logout', [LoginController::class, 'logout']);

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // User Management (Super Admin & Admin only)
        Route::middleware('api.role:super_admin,admin')->group(function () {
            Route::apiResource('users', UserController::class);
        });

        // Academic Management (Super Admin & Admin only)
        Route::middleware('api.role:super_admin,admin')->group(function () {
            Route::apiResource('classes', ClassController::class);
            Route::apiResource('subjects', SubjectController::class);
        });

        // Questions (Guru, Admin, Super Admin)
        Route::middleware('api.role:super_admin,admin,guru')->group(function () {
            Route::apiResource('questions', QuestionController::class);
            Route::post('/questions/import', [QuestionController::class, 'import']);
            Route::get('/questions/export', [QuestionController::class, 'export']);

            // Question Banks
            Route::apiResource('question-banks', QuestionBankController::class);
        });

        // Exams (Guru, Admin, Super Admin)
        Route::middleware('api.role:super_admin,admin,guru')->group(function () {
            Route::apiResource('exams', ExamController::class);
            Route::post('/exams/{exam}/publish', [ExamController::class, 'publish']);
        });

        // Exam Sessions (Admin, Super Admin)
        Route::middleware('api.role:super_admin,admin')->group(function () {
            Route::apiResource('sessions', ExamSessionController::class);
            Route::post('/sessions/{session}/open', [ExamSessionController::class, 'open']);
            Route::post('/sessions/{session}/close', [ExamSessionController::class, 'close']);
            Route::post('/sessions/{session}/generate-tokens', [ExamSessionController::class, 'generateTokens']);
            Route::get('/sessions/{session}/tokens', [ExamSessionController::class, 'printTokens']);

            // Exam Tokens
            Route::apiResource('tokens', ExamTokenController::class);
            Route::post('/tokens/validate', [ExamTokenController::class, 'validate']);
        });

        // Student Exam (All authenticated users)
        Route::prefix('exam')->group(function () {
            Route::post('/token/validate', [StudentExamController::class, 'validateToken']);
            Route::post('/start', [StudentExamController::class, 'startExam']);
            Route::post('/answer', [StudentExamController::class, 'saveAnswer']);
            Route::post('/submit', [StudentExamController::class, 'submitExam']);
            Route::get('/status/{attemptId}', [StudentExamController::class, 'getAttemptStatus']);
            Route::post('/heartbeat', [StudentExamController::class, 'heartbeat']);
            Route::get('/result/{attemptId}', [StudentExamController::class, 'getResult']);
        });

        // Grading (Guru, Admin, Super Admin)
        Route::middleware('api.role:super_admin,admin,guru')->group(function () {
            Route::get('/grading', [GradingController::class, 'index']);
            Route::get('/grading/{attemptId}', [GradingController::class, 'show']);
            Route::post('/grading/{attemptId}/grade', [GradingController::class, 'grade']);
            Route::post('/grading/{attemptId}/finalize', [GradingController::class, 'finalize']);
        });

        // Monitoring (Proktor, Admin, Super Admin)
        Route::middleware('api.role:super_admin,admin,proktor')->group(function () {
            Route::get('/monitoring', [MonitoringController::class, 'index']);
            Route::get('/monitoring/sessions/{sessionId}', [MonitoringController::class, 'session']);
            Route::post('/monitoring/reset-attempt', [MonitoringController::class, 'resetAttempt']);
            Route::get('/monitoring/stats', [MonitoringController::class, 'stats']);
        });

        // RPP Management (Guru, Admin, Super Admin)
        Route::middleware('api.role:super_admin,admin,guru')->group(function () {
            Route::apiResource('rpps', RppController::class);
            Route::get('/rpps/{rpp}/integration-status', [RppController::class, 'getIntegrationStatus']);
        });

        // LMS (Guru, Admin, Super Admin for management; Siswa for viewing)
        Route::middleware('api.role:super_admin,admin,guru')->group(function () {
            Route::apiResource('courses', CourseController::class);
            Route::apiResource('courses/{course}/materials', LearningMaterialController::class);
            Route::apiResource('courses/{course}/assignments', AssignmentController::class);
            Route::apiResource('courses/{course}/discussions', DiscussionController::class);
        });

        // Results & Reporting (All authenticated users)
        Route::prefix('results')->group(function () {
            Route::get('/', [ResultController::class, 'index']);
            Route::get('/by-exam/{examId}', [ResultController::class, 'byExam']);
            Route::get('/by-class/{classId}', [ResultController::class, 'byClass']);
            Route::get('/by-student/{studentId}', [ResultController::class, 'byStudent']);
            Route::get('/rekap', [ResultController::class, 'rekap']);
            Route::get('/laporan', [ResultController::class, 'laporan']);
        });

        // Import/Export (Admin, Super Admin for all; Guru for questions)
        Route::middleware('api.role:super_admin,admin')->group(function () {
            Route::post('/import/siswa', [ImportExportController::class, 'importSiswa']);
            Route::post('/import/guru', [ImportExportController::class, 'importGuru']);
            Route::post('/import/kelas', [ImportExportController::class, 'importKelas']);
            Route::get('/export/siswa', [ImportExportController::class, 'exportSiswa']);
            Route::get('/export/guru', [ImportExportController::class, 'exportGuru']);
            Route::get('/export/kelas', [ImportExportController::class, 'exportKelas']);
            Route::get('/export/participant', [ImportExportController::class, 'exportParticipant']);
            Route::get('/export/rekap', [ImportExportController::class, 'exportRekapNilai']);
            Route::get('/export/laporan', [ImportExportController::class, 'exportLaporan']);
        });

        // Dapodik Integration (Admin, Super Admin)
        Route::middleware('api.role:super_admin,admin')->prefix('dapodik')->group(function () {
            Route::get('/template', [DapodikController::class, 'template']);
            Route::post('/import', [DapodikController::class, 'import']);
            Route::get('/export', [DapodikController::class, 'export']);
        });

        // Item Analysis (Guru, Admin, Super Admin)
        Route::middleware('api.role:super_admin,admin,guru')->group(function () {
            Route::get('/item-analysis', [ItemAnalysisController::class, 'index']);
            Route::get('/item-analysis/{exam}', [ItemAnalysisController::class, 'show']);
        });
    });
});
