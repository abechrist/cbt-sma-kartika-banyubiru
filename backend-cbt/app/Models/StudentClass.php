<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = ['name', 'grade', 'academic_year', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'class_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'class_id');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'class_id');
    }
}
