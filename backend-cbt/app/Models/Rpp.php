<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rpp extends Model
{
    protected $fillable = [
        'subject_id',
        'class_id',
        'teacher_id',
        'academic_year',
        'semester',
        'topic',
        'time_allocation',
        'rpp_data',
        'status',
        'integrated_at',
    ];

    protected $casts = [
        'rpp_data' => 'array',
        'integrated_at' => 'datetime',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(StudentClass::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(RppMaterial::class, 'rpp_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(RppAssessment::class, 'rpp_id');
    }
}
