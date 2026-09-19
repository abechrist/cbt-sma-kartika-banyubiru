<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RppMaterial extends Model
{
    protected $fillable = [
        'rpp_id',
        'learning_material_id',
        'pertemuan_ke',
        'judul_topik',
        'deskripsi_aktivitas',
        'rekomendasi_bahan_ajar',
    ];

    protected $casts = [
        'rekomendasi_bahan_ajar' => 'array',
    ];

    public function rpp(): BelongsTo
    {
        return $this->belongsTo(Rpp::class);
    }

    public function learningMaterial(): BelongsTo
    {
        return $this->belongsTo(LearningMaterial::class);
    }
}
