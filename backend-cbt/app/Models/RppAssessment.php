<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RppAssessment extends Model
{
    protected $fillable = [
        'rpp_id',
        'exam_id',
        'tp_id',
        'deskripsi_tp',
        'jumlah_soal_direkomendasikan',
        'tipe_soal',
        'tingkat_kesulitan',
        'kata_kunci_indokator_soal',
    ];

    public function rpp(): BelongsTo
    {
        return $this->belongsTo(Rpp::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
