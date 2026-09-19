<?php

namespace Database\Seeders;

use App\Services\RppIntegrationService;
use Illuminate\Database\Seeder;

class RppIntegrationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(RppIntegrationService::class);

        $rppData = [
            'rpp_metadata' => [
                'mata_pelajaran' => 'Bahasa Indonesia',
                'kelas' => '10',
                'topik_utama' => 'Teks Eksplanasi',
                'alokasi_waktu' => '4 x 45 Menit (2 Pertemuan)',
            ],
            'lms_integration' => [
                'pertemuan_list' => [
                    [
                        'pertemuan_ke' => 1,
                        'judul_topik' => 'Mengenal Struktur dan Ciri Kebahasaan Teks Eksplanasi',
                        'deskripsi_aktivitas' => '1. Baca teks eksplanasi pendek yang tersedia di modul tentang fenomena "Hujan Es di Musim Kemarau". 2. Identifikasi bagian-bagian teks: pernyataan umum, deretan penjelas, dan interpretasi. 3. Soroti kalimat yang menggunakan kata kerja pasif, konjungsi kausalitas, dan istilah teknis. 4. Kerjakan latihan singkat di forum diskusi.',
                        'rekomendasi_bahan_ajar' => [
                            'E-Modul Interaktif: "Anatomi Teks Eksplanasi"',
                            'Video Pembelajaran (8 menit): "Bagaimana Proses Terjadinya Pelangi"',
                        ],
                    ],
                    [
                        'pertemuan_ke' => 2,
                        'judul_topik' => 'Menyusun Teks Eksplanasi Berdasarkan Fenomena Sehari-hari',
                        'deskripsi_aktivitas' => '1. Pelajari rubrik penilaian teks eksplanasi. 2. Pilih satu fenomena sosial/sains dari daftar yang disediakan. 3. Susun kerangka teks eksplanasi. 4. Kembangkan outline menjadi paragraf utuh minimal 5 paragraf.',
                        'rekomendasi_bahan_ajar' => [
                            'Template Outlines Teks Eksplanasi',
                            'Bank Fenomena 2024-2026: 20 ide topik kontekstual',
                            'Rubrik Penilaian Auto-graded',
                        ],
                    ],
                ],
            ],
            'cbt_integration' => [
                'assessment_type' => 'Sumatif',
                'tujuan_pembelajaran_mapped' => [
                    [
                        'tp_id' => 'TP_01',
                        'deskripsi_tp' => 'Peserta didik mampu mengidentifikasi struktur organisasi teks eksplanasi (pernyataan umum, deretan penjelas, dan interpretasi) pada sebuah teks eksplanasi utuh dengan tepat.',
                        'cbt_setup' => [
                            'jumlah_soal_direkomendasikan' => 5,
                            'tipe_soal' => 'Pilihan Ganda',
                            'tingkat_kesulitan' => 'Mudah',
                            'kata_kunci_indokator_soal' => 'Menandai paragraf yang merupakan "deretan penjelas"; membedakan pernyataan umum dengan interpretasi; mengurutkan bagian teks yang acak.',
                        ],
                    ],
                    [
                        'tp_id' => 'TP_02',
                        'deskripsi_tp' => 'Peserta didik mampu menganalisis ciri kebahasaan teks eksplanasi, khususnya penggunaan kata kerja pasif, konjungsi kausalitas, dan istilah teknis dalam konteks kalimat.',
                        'cbt_setup' => [
                            'jumlah_soal_direkomendasikan' => 6,
                            'tipe_soal' => 'Pilihan Ganda',
                            'tingkat_kesulitan' => 'Sedang',
                            'kata_kunci_indokator_soal' => 'Menentukan konjungsi kausalitas yang tepat; mengidentifikasi kata kerja pasif; mengenali istilah teknis.',
                        ],
                    ],
                    [
                        'tp_id' => 'TP_03',
                        'deskripsi_tp' => 'Peserta didik mampu menyusun teks eksplanasi pendek minimal 3 paragraf dengan memperhatikan kelengkapan struktur dan kaidah kebahasaan secara koheren.',
                        'cbt_setup' => [
                            'jumlah_soal_direkomendasikan' => 2,
                            'tipe_soal' => 'Esai Uraian',
                            'tingkat_kesulitan' => 'Sukar',
                            'kata_kunci_indokator_soal' => 'Rubrik esai: kelengkapan struktur, kaidah kebahasaan, koherensi wacana.',
                        ],
                    ],
                ],
            ],
        ];

        $rpp = $service->importRpp($rppData);
        $this->command->info('RPP Demo berhasil diimpor: '.$rpp->topic);
    }
}
