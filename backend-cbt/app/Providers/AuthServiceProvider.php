<?php

namespace App\Providers;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Policies\ExamAttemptPolicy;
use App\Policies\ExamPolicy;
use App\Policies\QuestionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Exam::class => ExamPolicy::class,
        Question::class => QuestionPolicy::class,
        ExamAttempt::class => ExamAttemptPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
