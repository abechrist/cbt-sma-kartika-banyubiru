<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $subjects = [
            ['Literasi Membaca', 'TKA-LMB', 'Literasi Membaca (TKA SMA)'],
            ['Numerasi', 'TKA-NUM', 'Numerasi (TKA SMA)'],
            ['Literasi Sains', 'TKA-LMS', 'Literasi Sains (TKA SMA)'],
            ['Fisika', 'TKA-FIS', 'Fisika (TKA SMA)'],
            ['Kimia', 'TKA-KIM', 'Kimia (TKA SMA)'],
            ['Biologi', 'TKA-BIO', 'Biologi (TKA SMA)'],
            ['Ekonomi', 'TKA-EKO', 'Ekonomi (TKA SMA)'],
            ['Geografi', 'TKA-GEO', 'Geografi (TKA SMA)'],
            ['Sosiologi', 'TKA-SOS', 'Sosiologi (TKA SMA)'],
        ];

        foreach ($subjects as [$name, $code, $desc]) {
            DB::table('subjects')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $name,
                    'description' => $desc,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('subjects')->where('code', 'like', 'TKA-%')->delete();
    }
};
