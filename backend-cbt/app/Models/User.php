<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    #[Fillable(['name', 'email', 'password', 'role_id', 'class_id', 'nisn', 'nip', 'phone', 'birth_date', 'address', 'gender', 'is_active'])]
    #[Hidden(['password', 'remember_token'])]
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'class_id',
        'nisn', 'nip', 'phone', 'birth_date', 'address', 'gender', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'birth_date' => 'date',
        ];
    }

    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_GURU = 'guru';

    public const ROLE_KEPALA_SEKOLAH = 'kepala_sekolah';

    public const ROLE_PROKTOR = 'proktor';

    public const ROLE_SISWA = 'siswa';

    public const ROLE_WALI_KELAS = 'wali_kelas';

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'user_subjects');
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function createdQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'created_by');
    }

    public function createdExams(): HasMany
    {
        return $this->hasMany(Exam::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->name === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role?->name === self::ROLE_ADMIN;
    }

    public function isGuru(): bool
    {
        return $this->role?->name === self::ROLE_GURU;
    }

    public function isSiswa(): bool
    {
        return $this->role?->name === self::ROLE_SISWA;
    }

    public function isProktor(): bool
    {
        return $this->role?->name === self::ROLE_PROKTOR;
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role?->name === self::ROLE_KEPALA_SEKOLAH;
    }

    public function isWaliKelas(): bool
    {
        return $this->role?->name === self::ROLE_WALI_KELAS;
    }

    public function canAccessExam(): bool
    {
        return in_array($this->role?->name, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            self::ROLE_GURU,
            self::ROLE_KEPALA_SEKOLAH,
            self::ROLE_PROKTOR,
            self::ROLE_SISWA,
            self::ROLE_WALI_KELAS,
        ]);
    }

    public function teachingCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'user_id');
    }
}
