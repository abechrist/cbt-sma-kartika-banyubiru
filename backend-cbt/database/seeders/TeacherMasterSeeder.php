<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherMasterSeeder extends Seeder
{
    public const DEFAULT_PASSWORD = 'password';

    public function run(): void
    {
        DB::transaction(function () {
            $guruRole = Role::firstOrCreate(
                ['name' => User::ROLE_GURU],
                ['description' => 'Guru Mata Pelajaran']
            );

            $koordinatorRole = Role::firstOrCreate(
                ['name' => User::ROLE_KOORDINATOR_KURIKULUM],
                ['description' => 'Guru Koordinator Kurikulum - Mengajar serta memantau perencanaan & pelaksanaan kurikulum guru']
            );

            // 1. Pastikan seluruh kelas X 1-6, XI 1-6, XII 1-6 tersedia
            $classMap = [];
            $grades = [
                'X' => 10,
                'XI' => 11,
                'XII' => 12,
            ];

            foreach ($grades as $roman => $gradeNumber) {
                for ($i = 1; $i <= 6; $i++) {
                    $className = "{$roman}-{$i}";
                    $classMap[$className] = StudentClass::updateOrCreate(
                        ['name' => $className],
                        [
                            'grade' => $gradeNumber,
                            'academic_year' => '2026/2027',
                            'description' => "Kelas {$roman} {$i}",
                            'is_active' => true,
                        ]
                    );
                }
            }

            // 2. Pastikan seluruh mata pelajaran kurikulum tersedia
            $subjectsCatalog = [
                'BIG' => ['Bahasa Inggris', 'Bahasa Inggris'],
                'BIG-TL' => ['Bahasa Inggris TL', 'Bahasa Inggris Tingkat Lanjut'],
                'BIN' => ['Bahasa Indonesia', 'Bahasa Indonesia'],
                'PJOK' => ['Penjasorkes', 'Pendidikan Jasmani, Olahraga, dan Kesehatan'],
                'SNM' => ['Seni Musik', 'Seni Musik'],
                'BIO' => ['Biologi', 'Biologi'],
                'EKO' => ['Ekonomi', 'Ekonomi'],
                'FIS' => ['Fisika', 'Fisika'],
                'SJR' => ['Sejarah', 'Sejarah'],
                'SJR-TL' => ['Sejarah TL', 'Sejarah Tingkat Lanjut'],
                'PKWU' => ['PKWU', 'Prakarya dan Kewirausahaan'],
                'MTK' => ['Matematika', 'Matematika'],
                'MTK-TL' => ['Matematika TL', 'Matematika Tingkat Lanjut'],
                'BJW' => ['Bahasa Jawa', 'Bahasa Jawa'],
                'PAB' => ['Agama Buddha', 'Pendidikan Agama Buddha dan Budi Pekerti'],
                'PAK' => ['Agama Kristen/Katholik', 'Pendidikan Agama Kristen/Katolik dan Budi Pekerti'],
                'PAI' => ['Agama Islam', 'Pendidikan Agama Islam dan Budi Pekerti'],
                'PAH' => ['Agama Hindu', 'Pendidikan Agama Hindu dan Budi Pekerti'],
                'PP' => ['Pendidikan Pancasila', 'Pendidikan Pancasila'],
                'INF' => ['Informatika', 'Informatika'],
                'KIM' => ['Kimia', 'Kimia'],
                'GEO' => ['Geografi', 'Geografi'],
                'SOS' => ['Sosiologi', 'Sosiologi'],
                'BK' => ['Bimbingan Konseling', 'Bimbingan dan Konseling'],
            ];

            $subjectMap = [];
            foreach ($subjectsCatalog as $code => [$name, $description]) {
                $subjectMap[$code] = Subject::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'description' => $description,
                        'is_active' => true,
                    ]
                );
            }

            // 3. Data 30 Guru SMA Kartika III-1 Banyubiru beserta pengampu Mapel & Kelas
            $teachers = [
                [
                    'name' => 'IMAM HIDAYAT, S.Pd., Gr',
                    'username' => 'imam.hidayat',
                    'assignments' => [
                        'BIG' => ['X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XI-6'],
                        'BIG-TL' => ['XII-3', 'XII-4'],
                    ],
                ],
                [
                    'name' => 'TITIK SUMARTININGSIH, S.Pd',
                    'username' => 'titik.sumartiningsih',
                    'assignments' => [
                        'BIN' => ['X-1', 'X-2', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'LILI MAYANGSARI, S.Pd',
                    'username' => 'lili.mayangsari',
                    'assignments' => [
                        'PJOK' => ['X-1', 'X-2', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'KAPTEN CAJ. SUTRISNO',
                    'username' => 'sutrisno',
                    'assignments' => [
                        'SNM' => ['XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'SUMERI, S.Pd',
                    'username' => 'sumeri',
                    'assignments' => [
                        'BIO' => ['XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'MALIANA AGUSTINI, S.Pd',
                    'username' => 'maliana.agustini',
                    'assignments' => [
                        'EKO' => ['XI-3', 'XI-4', 'XI-5', 'XI-6', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'M. RIANA DEWI P., S.Pt',
                    'username' => 'riana.dewi',
                    'assignments' => [
                        'FIS' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XII-1', 'XII-2'],
                    ],
                ],
                [
                    'name' => 'ANI SULISTYOWATI, S.Pd',
                    'username' => 'ani.sulistyowati',
                    'assignments' => [
                        'SJR' => ['XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XI-6', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                        'SJR-TL' => ['XI-3', 'XI-4'],
                    ],
                ],
                [
                    'name' => 'WIWIK WIDYOWATI, S.Pd',
                    'username' => 'wiwik.widyowati',
                    'assignments' => [
                        'PKWU' => ['XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XI-6', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'HANIF ULI RONA, S.Pd., Gr',
                    'username' => 'hanif.rona',
                    'assignments' => [
                        'MTK' => ['XI-1', 'XI-2', 'XI-3', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'NOVI NUR KHASANAH, S.Pd., Gr',
                    'username' => 'novi.khasanah',
                    'assignments' => [
                        'BIN' => ['X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XI-6'],
                    ],
                ],
                [
                    'name' => 'IKA SULISTYANINGRUM, S.Pd., Gr',
                    'username' => 'ika.sulistyaningrum',
                    'assignments' => [
                        'BJW' => [
                            'X-1', 'X-2', 'X-3', 'X-4', 'X-5',
                            'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XI-6',
                            'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6',
                        ],
                    ],
                ],
                [
                    'name' => 'ANIK SUHARNI, S.Pd',
                    'username' => 'anik.suharni',
                    'assignments' => [
                        'EKO' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5'],
                    ],
                ],
                [
                    'name' => 'WAHYUTI, S.Pd.B',
                    'username' => 'wahyuti',
                    'assignments' => [
                        'PAB' => ['X-2', 'XI-1', 'XI-4', 'XII-1', 'XII-4'],
                    ],
                ],
                [
                    'name' => 'DAVID, S.Pd.K, S.Th, M.Th., Gr',
                    'username' => 'david',
                    'assignments' => [
                        'PAK' => ['X-2', 'XI-1', 'XI-4', 'XII-1', 'XII-4'],
                        'PP' => ['XI-3', 'XI-4', 'XI-5', 'XI-6', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'MAULIDDHA RACHMI, S.Pd., Gr',
                    'username' => 'mauliddha.rachmi',
                    'assignments' => [
                        'MTK' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-4', 'XI-5', 'XI-6'],
                        'MTK-TL' => ['XI-1', 'XI-2'],
                    ],
                ],
                [
                    'name' => 'CATUR KRISTIAN SAHENDRA, S.Pd',
                    'username' => 'catur.sahendra',
                    'assignments' => [
                        'MTK-TL' => ['XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                        'INF' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5'],
                    ],
                ],
                [
                    'name' => 'IQNATU NAZILA AMIQUE, S.Pd., Gr',
                    'username' => 'iqnatu.amique',
                    'assignments' => [
                        'KIM' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XII-1', 'XII-2'],
                    ],
                ],
                [
                    'name' => 'PELDA (Purn) SURADI',
                    'username' => 'suradi',
                    'assignments' => [
                        'PJOK' => ['X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XI-6'],
                    ],
                ],
                [
                    'name' => 'WAHYU TRI LESTARI, S.Pd., Gr',
                    'username' => 'wahyu.lestari',
                    'assignments' => [
                        'BIG' => ['X-1', 'X-2', 'X-3', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                        'BIG-TL' => ['XI-5', 'XI-6'],
                    ],
                ],
                [
                    'name' => 'SYAHRUL ANDIKA PRATAMA, S.Pd',
                    'username' => 'syahrul.pratama',
                    'assignments' => [
                        'GEO' => ['XI-1', 'XI-2', 'XI-3', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'F. WIDIASTO, Amd',
                    'username' => 'f.widiasto',
                    'assignments' => [
                        'SNM' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3', 'XI-4', 'XI-5', 'XI-6'],
                    ],
                ],
                [
                    'name' => 'RAPIK, M.Pd',
                    'username' => 'rapik',
                    'assignments' => [
                        'PAI' => ['XI-3', 'XI-4', 'XI-5', 'XI-6', 'XII-1', 'XII-2', 'XII-3', 'XII-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'ANINDIKA LARASATI, S.Pd',
                    'username' => 'anindika.larasati',
                    'assignments' => [
                        'PAI' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2', 'XI-3'],
                        'INF' => ['XI-5', 'XI-6'],
                    ],
                ],
                [
                    'name' => 'ANINDYA FARINSA PURBA, S.Pd',
                    'username' => 'anindya.purba',
                    'assignments' => [
                        'SOS' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-3', 'XI-4', 'XII-5', 'XII-6'],
                    ],
                ],
                [
                    'name' => 'DEMETRIUS RYAN EKA PUTRA, S.Pd',
                    'username' => 'demetrius.putra',
                    'assignments' => [
                        'BK' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5'],
                    ],
                ],
                [
                    'name' => 'KURNIA OKTA NIRMALA, S.Geo',
                    'username' => 'kurnia.nirmala',
                    'assignments' => [
                        'GEO' => ['X-5', 'X-6', 'XI-3', 'XI-4', 'XI-5', 'XI-6'],
                    ],
                ],
                [
                    'name' => 'apt. LESTARI PUTRI, S.Farm',
                    'username' => 'lestari.putri',
                    'assignments' => [
                        'PAH' => ['XI-1'],
                    ],
                ],
                [
                    'name' => 'ALVINA KURNIAWATI, S.Pd',
                    'username' => 'alvina.kurniawati',
                    'assignments' => [
                        'PP' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2'],
                        'SJR' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5'],
                    ],
                ],
                [
                    'name' => 'HASNA RAISA AYU JULITA, S.Pd',
                    'username' => 'hasna.julita',
                    'assignments' => [
                        'BIO' => ['X-1', 'X-2', 'X-3', 'X-4', 'X-5', 'XI-1', 'XI-2'],
                    ],
                ],
            ];

            foreach ($teachers as $teacherRow) {
                $email = $teacherRow['username'] . '@kartika.sch.id';
                $existingUser = User::where('email', $email)->first();
                $preserveCoordinatorRole = $existingUser && $existingUser->role_id === $koordinatorRole->id;

                $teacher = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $teacherRow['name'],
                        'nip' => $teacherRow['username'],
                        'password' => self::DEFAULT_PASSWORD,
                        'role_id' => $preserveCoordinatorRole ? $koordinatorRole->id : $guruRole->id,
                        'is_active' => true,
                    ]
                );

                $subjectIds = [];
                foreach ($teacherRow['assignments'] as $subjectCode => $classNames) {
                    $subject = $subjectMap[$subjectCode];
                    $subjectIds[] = $subject->id;

                    if (! $subject->teacher_id) {
                        $subject->update(['teacher_id' => $teacher->id]);
                    }

                    foreach ($classNames as $className) {
                        $studentClass = $classMap[$className] ?? null;
                        if (! $studentClass) {
                            continue;
                        }

                        Course::updateOrCreate(
                            [
                                'subject_id' => $subject->id,
                                'class_id' => $studentClass->id,
                                'teacher_id' => $teacher->id,
                            ],
                            [
                                'academic_year' => '2026/2027',
                                'semester' => 'ganjil',
                                'description' => "Kelas Pembelajaran {$subject->name} — Kelas {$studentClass->name} ({$teacher->name})",
                                'is_active' => true,
                            ]
                        );
                    }
                }

                $teacher->subjects()->syncWithoutDetaching($subjectIds);
            }

            // 4. Akun Default Koordinator Kurikulum (Wakakur) untuk pengujian & operasional awal
            User::updateOrCreate(
                ['email' => 'wakakur@kartika.sch.id'],
                [
                    'name' => 'Koordinator Kurikulum (Wakakur)',
                    'nip' => 'koordinator.kurikulum',
                    'password' => self::DEFAULT_PASSWORD,
                    'role_id' => $koordinatorRole->id,
                    'is_active' => true,
                ]
            );
        });
    }
}
