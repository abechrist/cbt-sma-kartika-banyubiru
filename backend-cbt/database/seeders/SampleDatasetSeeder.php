<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\ExamToken;
use App\Models\LearningMaterial;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Role;
use App\Models\Rpp;
use App\Models\RppAssessment;
use App\Models\RppMaterial;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleDatasetSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedSampleData();
        });
    }

    private function seedSampleData(): void
    {
        $roleGuru = Role::firstOrCreate(['name' => User::ROLE_GURU], ['description' => 'Guru Mata Pelajaran']);
        $roleSiswa = Role::firstOrCreate(['name' => User::ROLE_SISWA], ['description' => 'Siswa CBT']);

        $classXA = StudentClass::firstOrCreate(['name' => 'X-A'], ['grade' => 10, 'academic_year' => '2026/2027', 'description' => 'Kelas X - A', 'is_active' => true]);
        $classXB = StudentClass::firstOrCreate(['name' => 'X-B'], ['grade' => 10, 'academic_year' => '2026/2027', 'description' => 'Kelas X - B', 'is_active' => true]);
        $classXIA = StudentClass::firstOrCreate(['name' => 'XI-A'], ['grade' => 11, 'academic_year' => '2026/2027', 'description' => 'Kelas XI - A', 'is_active' => true]);

        // -------------------------------------------------------------
        // 1. 5 GURU & 5 MATA PELAJARAN MASTER
        // -------------------------------------------------------------
        $teacherData = [
            'FIS' => [
                'name' => 'Drs. Agus Setiawan, M.Si.',
                'email' => 'agus.fisika@kartika.sch.id',
                'nip' => '19820315 200801 1 008',
                'subject_name' => 'Fisika',
                'desc' => 'Fisika Fase E & F Kurikulum Merdeka SMA Kartika',
            ],
            'BIO' => [
                'name' => 'Siti Aminah, S.Si., M.Pd.',
                'email' => 'siti.biologi@kartika.sch.id',
                'nip' => '19861120 201201 2 009',
                'subject_name' => 'Biologi',
                'desc' => 'Biologi & Sains Hayati SMA Kartika',
            ],
            'KIM' => [
                'name' => 'Bambang Pamungkas, S.Pd.',
                'email' => 'bambang.kimia@kartika.sch.id',
                'nip' => '19840412 200901 1 010',
                'subject_name' => 'Kimia',
                'desc' => 'Kimia Terapan & Reaksi Kimia SMA Kartika',
            ],
            'BIG' => [
                'name' => 'Sarah Wijaya, M.Pd.',
                'email' => 'sarah.inggris@kartika.sch.id',
                'nip' => '19890918 201401 2 011',
                'subject_name' => 'Bahasa Inggris',
                'desc' => 'English for Communication & Academic Purposes',
            ],
            'SJR' => [
                'name' => 'Hendra Gunawan, S.Pd.',
                'email' => 'hendra.sejarah@kartika.sch.id',
                'nip' => '19850125 201001 1 012',
                'subject_name' => 'Sejarah',
                'desc' => 'Sejarah Indonesia & Wawasan Kebangsaan SMA Kartika',
            ],
        ];

        $teachers = [];
        $subjects = [];

        foreach ($teacherData as $code => $data) {
            $teacher = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => 'password123',
                    'role_id' => $roleGuru->id,
                    'nip' => $data['nip'],
                    'is_active' => true,
                    'phone' => '0812' . rand(10000000, 99999999),
                ]
            );
            $teachers[$code] = $teacher;

            $subject = Subject::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $data['subject_name'],
                    'description' => $data['desc'],
                    'is_active' => true,
                    'teacher_id' => $teacher->id,
                ]
            );
            $subject->teachers()->syncWithoutDetaching([$teacher->id]);
            $subjects[$code] = $subject;
        }

        // -------------------------------------------------------------
        // 2. 5 DATA SISWA CBT RESMI
        // -------------------------------------------------------------
        $studentsSample = [
            [
                'nisn' => '0081234501',
                'name' => 'Ananda Dimas Pratama',
                'email' => 'dimas.pratama@siswa.kartika.sch.id',
                'class_id' => $classXA->id,
                'gender' => 'L',
                'birth_date' => '2009-04-12',
                'address' => 'Jl. Diponegoro No. 14, Banyubiru, Kab. Semarang',
                'phone' => '081234567801',
            ],
            [
                'nisn' => '0081234502',
                'name' => 'Clarissa Aurelia Putri',
                'email' => 'clarissa.putri@siswa.kartika.sch.id',
                'class_id' => $classXA->id,
                'gender' => 'P',
                'birth_date' => '2009-07-23',
                'address' => 'Jl. Raya Muncul No. 45, Banyubiru, Kab. Semarang',
                'phone' => '081234567802',
            ],
            [
                'nisn' => '0081234503',
                'name' => 'Fajar Ramadhan',
                'email' => 'fajar.ramadhan@siswa.kartika.sch.id',
                'class_id' => $classXB->id,
                'gender' => 'L',
                'birth_date' => '2009-09-15',
                'address' => 'Dusun Kebondowo RT 02/RW 03, Banyubiru',
                'phone' => '081234567803',
            ],
            [
                'nisn' => '0081234504',
                'name' => 'Nabila Syifa Az-Zahra',
                'email' => 'nabila.syifa@siswa.kartika.sch.id',
                'class_id' => $classXB->id,
                'gender' => 'P',
                'birth_date' => '2009-02-18',
                'address' => 'Jl. Tentara Pelajar No. 8, Ambarawa, Kab. Semarang',
                'phone' => '081234567804',
            ],
            [
                'nisn' => '0071234505',
                'name' => 'Rizky Arya Pratama',
                'email' => 'rizky.arya@siswa.kartika.sch.id',
                'class_id' => $classXIA->id,
                'gender' => 'L',
                'birth_date' => '2008-11-05',
                'address' => 'Jl. Palagan No. 21, Banyubiru, Kab. Semarang',
                'phone' => '081234567805',
            ],
        ];

        foreach ($studentsSample as $stud) {
            User::updateOrCreate(
                ['nisn' => $stud['nisn']],
                [
                    'name' => $stud['name'],
                    'email' => $stud['email'],
                    'password' => 'password123',
                    'role_id' => $roleSiswa->id,
                    'class_id' => $stud['class_id'],
                    'gender' => $stud['gender'],
                    'birth_date' => $stud['birth_date'],
                    'address' => $stud['address'],
                    'phone' => $stud['phone'],
                    'is_active' => true,
                ]
            );
        }

        // -------------------------------------------------------------
        // 3. 5 KURSUS LMS LENGKAP DENGAN MATERI & TUGAS
        // -------------------------------------------------------------
        $lmsConfig = [
            'FIS' => [
                'class' => $classXA,
                'desc' => 'Kursus Pembelajaran Fisika Terapan Fase E (Kelas X). Fokus pada mekanika gerak lurus dan dinamika Hukum Newton.',
                'materials' => [
                    [
                        'title' => 'Diktat Interaktif: Konsep Inersia dan Hukum Newton I, II, III',
                        'chapter' => 'Bab 1: Dinamika Gerak Partikel',
                        'type' => 'article',
                        'content_text' => "Hukum Newton mengenai gerak merupakan dasar mekanika klasik:\n1. Hukum I Newton (Inersia): Setiap benda akan tetap diam atau bergerak lurus beraturan jika tidak ada resultan gaya luar yang bekerja padanya (ΣF = 0).\n2. Hukum II Newton: Percepatan berbanding lurus dengan resultan gaya dan berbanding terbalik dengan massa (ΣF = m * a).\n3. Hukum III Newton: Gaya aksi sama besar dengan reaksi berlawanan arah (F_aksi = -F_reaksi).\n\nPelajarilah bagaimana gaya gesekan statis dan kinetis mempengaruhi benda pada bidang miring.",
                    ],
                    [
                        'title' => 'Simulasi Laboratorium PhET: Eksperimen Gaya Gesek dan Percepatan Balok',
                        'chapter' => 'Bab 1: Dinamika Gerak Partikel',
                        'type' => 'video',
                        'video_url' => 'https://www.youtube.com/watch?v=kKKM8Y-u7ds',
                        'content_text' => 'Tonton video demonstrasi praktikum virtual gaya gesek sebelum mengerjakan Lembar Kerja Siswa.',
                    ],
                ],
                'assignment' => [
                    'title' => 'Laporan Praktikum Mandiri: Koefisien Gesek Statis Benda Meluncur',
                    'instructions' => 'Lakukan pengamatan mandiri gerak balok pada bidang miring. Hitung nilai sudut kritis tan(θ) saat balok mulai meluncur dan bandingkan hasilnya dengan teori mekanika.',
                    'due_days' => 14,
                    'max_score' => 100,
                ],
            ],
            'BIO' => [
                'class' => $classXA,
                'desc' => 'Kursus Biologi Konservasi Keanekaragaman Hayati dan Kearifan Lokal Danau Rawa Pening Banyubiru.',
                'materials' => [
                    [
                        'title' => 'Studi Kasus: Biodiversitas Endemik dan Ekosistem Perairan Rawa Pening',
                        'chapter' => 'Bab 2: Keanekaragaman Hayati',
                        'type' => 'article',
                        'content_text' => "Keanekaragaman hayati mencakup tiga tingkatan: Genetik, Spesies, dan Ekosistem.\nDanau Rawa Pening yang berlokasi di Banyubiru merupakan ekosistem perairan darat yang kaya akan keanekaragaman ikan endemik (wader pari, betutu), namun menghadapi ancaman eutrofikasi akibat ledakan populasi eceng gondok (Eichhornia crassipes).\nUpaya pelestarian in-situ dan pemanfaatan biomassa eceng gondok menjadi kunci keseimbangan ekologis.",
                    ],
                    [
                        'title' => 'Video Edukasi: Dinamika Jaring Makanan & Konservasi Lahan Basah',
                        'chapter' => 'Bab 2: Keanekaragaman Hayati',
                        'type' => 'video',
                        'video_url' => 'https://www.youtube.com/watch?v=0k5G9uWb59A',
                        'content_text' => 'Video analisis hubungan trofik produsen, konsumen primer, hingga dekomposer dalam danau air tawar.',
                    ],
                ],
                'assignment' => [
                    'title' => 'Analisis Ekologis: Dampak Eutrofikasi Eceng Gondok di Banyubiru',
                    'instructions' => 'Tuliskan kajian kritis 500 kata mengenai peran produsen air tawar dan dampak penutupan permukaan danau terhadap kadar oksigen terlarut (DO) bagi biota ikan.',
                    'due_days' => 14,
                    'max_score' => 100,
                ],
            ],
            'KIM' => [
                'class' => $classXB,
                'desc' => 'Kursus Kimia Dasar Kelas X-B Kurikulum Merdeka. Membahas konfigurasi elektron, teori orbital atom, dan tabel periodik unsur.',
                'materials' => [
                    [
                        'title' => 'Panduan Konfigurasi Elektron: Aturan Aufbau, Larangan Pauli, dan Kaidah Hund',
                        'chapter' => 'Bab 1: Struktur Atom & Periodisitas',
                        'type' => 'article',
                        'content_text' => "Penulisan konfigurasi elektron modern didasarkan pada tingkat energi orbital subkulit:\n1. Prinsip Aufbau: Pengisian elektron dimulai dari orbital energi terendah (1s, 2s, 2p, 3s, 3p, 4s, 3d, dst).\n2. Larangan Pauli: Tidak boleh ada dua elektron dalam satu atom yang memiliki keempat bilangan kuantum sama.\n3. Kaidah Hund: Pengisian elektron pada orbital setingkat dilakukan secara paralel (tidak berpasangan dulu).\n\nPelajari hubungan konfigurasi elektron dengan letak golongan dan periode.",
                    ],
                    [
                        'title' => 'Visualisasi Animasi 3D: Orbital Elektron s, p, d, f',
                        'chapter' => 'Bab 1: Struktur Atom & Periodisitas',
                        'type' => 'video',
                        'video_url' => 'https://www.youtube.com/watch?v=sMt5DxFzxKU',
                        'content_text' => 'Video visualisasi ruang orbital kuantum penemuan Erwin Schrodinger.',
                    ],
                ],
                'assignment' => [
                    'title' => 'Lembar Kerja Kimia: Penentuan 4 Bilangan Kuantum Elektron Terakhir',
                    'instructions' => 'Tentukan bilangan kuantum utama (n), azimuth (l), magnetik (m), dan spin (s) untuk elektron terakhir dari atom 26Fe, 17Cl, dan 19K.',
                    'due_days' => 10,
                    'max_score' => 100,
                ],
            ],
            'BIG' => [
                'class' => $classXIA,
                'desc' => 'Advanced English Course for Grade XI. Exploring Analytical Exposition Texts, debate arguments, and scientific discourse.',
                'materials' => [
                    [
                        'title' => 'Mastering Analytical Exposition: Structure and Language Features',
                        'chapter' => 'Unit 1: Argumentative Writing',
                        'type' => 'article',
                        'content_text' => "An analytical exposition text evaluates a topic critically and persuades the reader that the idea is important.\nGeneric Structure:\n1. Thesis: Introduces the topic and indicates the writer's stance.\n2. Arguments: Presents series of arguments supported by evidence, facts, and logical reasoning.\n3. Reiteration / Conclusion: Restates the writer's point of view.\n\nKey Language Features:\n- Simple Present Tense\n- Causal Conjunctions (consequently, because of, therefore)\n- Evaluative words and modal auxiliaries.",
                    ],
                    [
                        'title' => 'Video Guide: How to Construct High-Impact Arguments in English',
                        'chapter' => 'Unit 1: Argumentative Writing',
                        'type' => 'video',
                        'video_url' => 'https://www.youtube.com/watch?v=Gz3d3B2yX8c',
                        'content_text' => 'Learn how to avoid logical fallacies and articulate persuasive points clearly.',
                    ],
                ],
                'assignment' => [
                    'title' => 'Essay Project: The Importance of Digital Literacy in Modern High School Education',
                    'instructions' => 'Write a 300-400 word Analytical Exposition essay discussing the benefits and critical challenges of artificial intelligence tools in student learning.',
                    'due_days' => 12,
                    'max_score' => 100,
                ],
            ],
            'SJR' => [
                'class' => $classXA,
                'desc' => 'Kursus Sejarah Kebudayaan Maritim dan Perdagangan Jalur Rempah Nusantara.',
                'materials' => [
                    [
                        'title' => 'Atlas Naratif: Poros Maritim dan Titik Perdagangan Jalur Rempah Abad ke-15',
                        'chapter' => 'Bab 3: Jalur Rempah Maritim',
                        'type' => 'article',
                        'content_text' => "Jalur Rempah merupakan jaringan niaga maritim tertua yang menghubungkan kepulauan Nusantara dengan pasar dunia di India, Timur Tengah, dan Eropa.\nKomoditas bernilai tinggi seperti cengkih (Maluku Utara) dan pala (Kepulauan Banda) menarik bangsa-bangsa dunia berlayar menyusuri Selat Malaka, Laut Jawa, dan Selat Sunda.\nPelabuhan-pelabuhan pesisir utara Jawa seperti Jepara, Tuban, dan Semarang menjadi hub transit logistik penting bagi peradaban bahari nusantara.",
                    ],
                    [
                        'title' => 'Dokumenter Sejarah: Jejak Pelayaran Kapal Pinisi dan Kerajaan Bahari',
                        'chapter' => 'Bab 3: Jalur Rempah Maritim',
                        'type' => 'video',
                        'video_url' => 'https://www.youtube.com/watch?v=3R-z5sT790A',
                        'content_text' => 'Menelusuri keahlian astronomi tradisional dan rancang bangun kapal pelaut nusantara.',
                    ],
                ],
                'assignment' => [
                    'title' => 'Resume Historis: Pengaruh Jalur Rempah terhadap Akulturasi Budaya di Pantai Jawa',
                    'instructions' => 'Uraikan minimal 3 bentuk akulturasi budaya (arsitektur, kuliner, atau bahasa) akibat interaksi pedagang asing di bandar pelabuhan nusantara tempo dulu.',
                    'due_days' => 14,
                    'max_score' => 100,
                ],
            ],
        ];

        $courses = [];
        $learningMaterials = [];

        foreach ($lmsConfig as $code => $cfg) {
            $teacher = $teachers[$code];
            $subject = $subjects[$code];
            $class = $cfg['class'];

            $course = Course::firstOrCreate(
                [
                    'subject_id' => $subject->id,
                    'class_id' => $class->id,
                    'teacher_id' => $teacher->id,
                ],
                [
                    'academic_year' => '2026/2027',
                    'semester' => 'ganjil',
                    'description' => $cfg['desc'],
                    'is_active' => true,
                ]
            );
            $courses[$code] = $course;

            foreach ($cfg['materials'] as $mat) {
                $createdMat = LearningMaterial::firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'title' => $mat['title'],
                    ],
                    [
                        'chapter' => $mat['chapter'],
                        'type' => $mat['type'],
                        'content_text' => $mat['content_text'] ?? null,
                        'video_url' => $mat['video_url'] ?? null,
                        'is_published' => true,
                        'created_by' => $teacher->id,
                    ]
                );
                $learningMaterials[$code][] = $createdMat;
            }

            Assignment::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => $cfg['assignment']['title'],
                ],
                [
                    'instructions' => $cfg['assignment']['instructions'],
                    'due_date' => Carbon::now()->addDays($cfg['assignment']['due_days']),
                    'max_score' => $cfg['assignment']['max_score'],
                    'is_published' => true,
                    'created_by' => $teacher->id,
                ]
            );
        }

        // -------------------------------------------------------------
        // 4. 5 PAKET UJIAN CBT LENGKAP (SOAL PG + ESSAY + SESI + TOKEN AKTIF)
        // -------------------------------------------------------------
        $cbtExams = [
            'FIS' => [
                'name' => 'Penilaian Sumatif Fisika: Dinamika Gerak Newton',
                'description' => 'Ujian Sumatif Harian Mata Pelajaran Fisika Fase E Materi Hukum I, II, III Newton dan Gesekan.',
                'duration' => 60,
                'token' => 'FISIKA26',
                'questions' => [
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Sebuah balok bermassa 5 kg terletak di atas lantai licin dan ditarik dengan gaya mendatar F = 20 N. Percepatan yang dialami balok adalah...',
                        'diff' => 'easy',
                        'score' => 20,
                        'code' => 'FIS-3.1',
                        'options' => [
                            ['A', '2 m/s²', false],
                            ['B', '4 m/s²', true],
                            ['C', '5 m/s²', false],
                            ['D', '10 m/s²', false],
                            ['E', '100 m/s²', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Peristiwa penumpang terdorong ke depan saat bus mendadak direm merupakan penerapan dari...',
                        'diff' => 'easy',
                        'score' => 20,
                        'code' => 'FIS-3.2',
                        'options' => [
                            ['A', 'Hukum I Newton (Kelembaman)', true],
                            ['B', 'Hukum II Newton', false],
                            ['C', 'Hukum III Newton', false],
                            ['D', 'Hukum Gravitasi Newton', false],
                            ['E', 'Hukum Kekekalan Energi', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Dua benda bermassa 2 kg dan 3 kg dihubungkan tali melalui katrol licin tanpa gesekan (g = 10 m/s²). Percepatan sistem katrol tersebut adalah...',
                        'diff' => 'medium',
                        'score' => 25,
                        'code' => 'FIS-3.3',
                        'options' => [
                            ['A', '1 m/s²', false],
                            ['B', '2 m/s²', true],
                            ['C', '3 m/s²', false],
                            ['D', '5 m/s²', false],
                            ['E', '6 m/s²', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_ESAI,
                        'text' => 'Jelaskan perbedaan mendasar antara koefisien gesekan statis dan koefisien gesekan kinetis, serta berikan satu contoh aplikasi teknologi pengereman kendaraan bermotor!',
                        'diff' => 'hard',
                        'score' => 35,
                        'code' => 'FIS-4.1',
                        'options' => [],
                    ],
                ],
            ],
            'BIO' => [
                'name' => 'Penilaian Sumatif Biologi: Biodiversitas & Ekosistem Perairan',
                'description' => 'Ujian Sumatif Biologi Bab Keanekaragaman Hayati dan Konservasi Rawa Pening Banyubiru.',
                'duration' => 60,
                'token' => 'BIOLOG26',
                'questions' => [
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Perbedaan variasi warna bunga mawar merah, putih, dan kuning merupakan bukti keanekaragaman pada tingkat...',
                        'diff' => 'easy',
                        'score' => 20,
                        'code' => 'BIO-3.1',
                        'options' => [
                            ['A', 'Genetik', true],
                            ['B', 'Spesies / Jenis', false],
                            ['C', 'Ekosistem', false],
                            ['D', 'Filum', false],
                            ['E', 'Biosfer', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Dampak negatif utama dari pertumbuhan eceng gondok yang tak terkendali di perairan danau terhadap ekosistem ikan adalah...',
                        'diff' => 'medium',
                        'score' => 20,
                        'code' => 'BIO-3.2',
                        'options' => [
                            ['A', 'Kadar oksigen terlarut menurun drastis akibat dekomposisi bahan organik', true],
                            ['B', 'Suhu air meningkat menjadi terlalu panas untuk ikan', false],
                            ['C', 'Kandungan garam di perairan danau meningkat drastis', false],
                            ['D', 'Arus air danau mengalir terlalu deras', false],
                            ['E', 'Kekeruhan danau menjadi sangat jernih', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Garis Wallace dan Garis Weber dalam biogeografi Indonesia memisahkan tipe fauna...',
                        'diff' => 'medium',
                        'score' => 25,
                        'code' => 'BIO-3.3',
                        'options' => [
                            ['A', 'Asiatis, Peralihan, dan Australis', true],
                            ['B', 'Tropis, Subtropis, dan Kutub', false],
                            ['C', 'Endemik, Kosmopolit, dan Eksotik', false],
                            ['D', 'Mamalia, Aves, dan Reptil', false],
                            ['E', 'Daratan, Perairan, dan Udara', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_ESAI,
                        'text' => 'Jelaskan konsep piramida energi dalam ekosistem dan uraikan mengapa energi yang ditransfer ke tingkat trofik berikutnya hanya sekitar 10%!',
                        'diff' => 'hard',
                        'score' => 35,
                        'code' => 'BIO-4.1',
                        'options' => [],
                    ],
                ],
            ],
            'KIM' => [
                'name' => 'Penilaian Sumatif Kimia: Konfigurasi Elektron & Tabel Periodik',
                'description' => 'Evaluasi Teori Kuantum Atom, Bilangan Kuantum, dan Sifat Keperiodikan Unsur Kelas X.',
                'duration' => 60,
                'token' => 'KIMIA26',
                'questions' => [
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Suatu unsur memiliki nomor atom 17 (Klorin). Konfigurasi elektron berdasarkan subkulit yang tepat adalah...',
                        'diff' => 'easy',
                        'score' => 20,
                        'code' => 'KIM-3.1',
                        'options' => [
                            ['A', '1s² 2s² 2p⁶ 3s² 3p⁵', true],
                            ['B', '1s² 2s² 2p⁶ 3s¹ 3p⁶', false],
                            ['C', '1s² 2s² 2p⁶ 3d⁷', false],
                            ['D', '1s² 2s² 2p⁶ 3s² 3p⁴ 4s¹', false],
                            ['E', '1s² 2s² 2p⁶ 3s² 3d⁵', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Unsur dengan nomor atom 19 terletak pada tabel periodik unsur pada golongan dan periode...',
                        'diff' => 'medium',
                        'score' => 20,
                        'code' => 'KIM-3.2',
                        'options' => [
                            ['A', 'Golongan IA, Periode 4', true],
                            ['B', 'Golongan IIA, Periode 3', false],
                            ['C', 'Golongan VIIA, Periode 4', false],
                            ['D', 'Golongan IB, Periode 4', false],
                            ['E', 'Golongan IVA, Periode 1', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Di antara unsur-unsur seperiode dari kiri ke kanan, kecenderungan energi ionisasi pertama pada umumnya adalah...',
                        'diff' => 'medium',
                        'score' => 25,
                        'code' => 'KIM-3.3',
                        'options' => [
                            ['A', 'Cenderung meningkat karena muatan inti efektif bertambah', true],
                            ['B', 'Cenderung menurun karena jumlah kulit bertambah', false],
                            ['C', 'Selalu bernilai konstan', false],
                            ['D', 'Menurun drastis hingga mencapai nol', false],
                            ['E', 'Mula-mula menurun lalu naik tajam', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_ESAI,
                        'text' => 'Tentukan keempat bilangan kuantum (n, l, m, s) untuk elektron terluar dari atom Besi (nomor atom 26)! Tunjukkan langkah pengisian orbital d!',
                        'diff' => 'hard',
                        'score' => 35,
                        'code' => 'KIM-4.1',
                        'options' => [],
                    ],
                ],
            ],
            'BIG' => [
                'name' => 'Summative Assessment: English Reading & Analytical Exposition',
                'description' => 'Assessing Reading Comprehension, Thesis Identification, and Argumentative Structure for Grade XI.',
                'duration' => 60,
                'token' => 'ENGLISH26',
                'questions' => [
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Which section of an analytical exposition text serves to restate the writer’s main viewpoint or conclusion?',
                        'diff' => 'easy',
                        'score' => 20,
                        'code' => 'ENG-3.1',
                        'options' => [
                            ['A', 'Reiteration / Conclusion', true],
                            ['B', 'Thesis Statement', false],
                            ['C', 'Orientation', false],
                            ['D', 'Complication', false],
                            ['E', 'Recommendation (Hortatory)', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Choose the most suitable connective to link these sentences: "The students studied hard throughout the semester. ________, they passed the national exam with flying colors."',
                        'diff' => 'easy',
                        'score' => 20,
                        'code' => 'ENG-3.2',
                        'options' => [
                            ['A', 'Consequently', true],
                            ['B', 'Nevertheless', false],
                            ['C', 'However', false],
                            ['D', 'On the contrary', false],
                            ['E', 'In spite of', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_PG,
                        'text' => '"Renewable energy technologies reduce greenhouse gas emissions and safeguard future generations." What is the communicative purpose of this argument?',
                        'diff' => 'medium',
                        'score' => 25,
                        'code' => 'ENG-3.3',
                        'options' => [
                            ['A', 'To persuade readers that green energy is vital by highlighting environmental protection', true],
                            ['B', 'To entertain the reader with an imaginative tale of clean air', false],
                            ['C', 'To explain step-by-step how to construct a solar panel', false],
                            ['D', 'To report recent historical events without subjective opinion', false],
                            ['E', 'To criticize government policies on electricity costs', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_ESAI,
                        'text' => 'Write a short argument paragraph (at least 4 sentences) supporting why reading digital books improves vocabulary retention in high school students. Include at least two causal connectors!',
                        'diff' => 'hard',
                        'score' => 35,
                        'code' => 'ENG-4.1',
                        'options' => [],
                    ],
                ],
            ],
            'SJR' => [
                'name' => 'Penilaian Sumatif Sejarah: Jalur Rempah & Maritim Nusantara',
                'description' => 'Ujian Sumatif Sejarah Indonesia: Titik Perdagangan Bahari dan Dinamika Kerajaan Pesisir Jawa.',
                'duration' => 60,
                'token' => 'SEJARAH26',
                'questions' => [
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Komoditas rempah asli Kepulauan Maluku yang menjadi komoditas paling bernilai tinggi di pasar dunia abad ke-16 adalah...',
                        'diff' => 'easy',
                        'score' => 20,
                        'code' => 'SJR-3.1',
                        'options' => [
                            ['A', 'Cengkih dan Pala', true],
                            ['B', 'Kopi dan Teh', false],
                            ['C', 'Tembakau dan Kakao', false],
                            ['D', 'Lada hitam dan Karet', false],
                            ['E', 'Kapur barus dan Kayu manis', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Peran utama kota-kota pelabuhan di pesisir utara Pulau Jawa (seperti Tuban, Jepara, dan Semarang) dalam jaringan niaga rempah adalah sebagai...',
                        'diff' => 'medium',
                        'score' => 20,
                        'code' => 'SJR-3.2',
                        'options' => [
                            ['A', 'Bandar transit pergudangan logistik dan lumbung beras penopang pelayaran antar-pulau', true],
                            ['B', 'Penghasil utama rempah pala terbesar di dunia', false],
                            ['C', 'Pusat pembuatan benteng pertahanan bangsa Portugis', false],
                            ['D', 'Satu-satunya produsen kapal perang di Asia Tenggara', false],
                            ['E', 'Pusat pertambangan emas dan perak nusantara', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_PG,
                        'text' => 'Pemanfaatan sistem angin muson barat dan muson timur oleh para pelaut tradisional nusantara menunjukkan bahwa peradaban maritim Indonesia didasarkan pada...',
                        'diff' => 'medium',
                        'score' => 25,
                        'code' => 'SJR-3.3',
                        'options' => [
                            ['A', 'Penguasaan kearifan navigasi astronomi dan klimatologi alam yang mendalam', true],
                            ['B', 'Ketergantungan pasif pada teknologi kapal bangsa Barat', false],
                            ['C', 'Ketidakteraturan perjalanan laut tanpa tujuan niaga', false],
                            ['D', 'Penolakan terhadap pertukaran budaya luar', false],
                            ['E', 'Penggunaan mesin uap sejak abad permulaan masehi', false],
                        ],
                    ],
                    [
                        'type' => Question::TYPE_ESAI,
                        'text' => 'Jelaskan bagaimana jalur rempah maritim nusantara tidak hanya menjadi jalur perdagangan ekonomi, melainkan juga jalur penyebaran agama, bahasa Melayu sebagai lingua franca, dan kebudayaan!',
                        'diff' => 'hard',
                        'score' => 35,
                        'code' => 'SJR-4.1',
                        'options' => [],
                    ],
                ],
            ],
        ];

        $exams = [];

        foreach ($cbtExams as $code => $examData) {
            $teacher = $teachers[$code];
            $subject = $subjects[$code];
            $class = $lmsConfig[$code]['class'];

            $exam = Exam::updateOrCreate(
                ['name' => $examData['name']],
                [
                    'subject_id' => $subject->id,
                    'description' => $examData['description'],
                    'duration_minutes' => $examData['duration'],
                    'randomize_questions' => true,
                    'randomize_options' => true,
                    'allow_back' => true,
                    'show_result_after' => true,
                    'status' => Exam::STATUS_PUBLISHED,
                    'created_by' => $teacher->id,
                ]
            );
            $exams[$code] = $exam;

            // Generate Questions
            $questionIds = [];
            foreach ($examData['questions'] as $qIndex => $q) {
                $question = Question::create([
                    'subject_id' => $subject->id,
                    'class_id' => $class->id,
                    'type' => $q['type'],
                    'question_text' => $q['text'],
                    'difficulty' => $q['diff'],
                    'score' => $q['score'],
                    'competency_code' => $q['code'],
                    'is_active' => true,
                    'created_by' => $teacher->id,
                ]);

                if (! empty($q['options'])) {
                    foreach ($q['options'] as $oIdx => [$label, $text, $isCorrect]) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'label' => $label,
                            'option_text' => $text,
                            'is_correct' => $isCorrect,
                            'sort_order' => $oIdx + 1,
                        ]);
                    }
                }

                $questionIds[$question->id] = [
                    'order_in_exam' => $qIndex + 1,
                    'score' => $q['score'],
                ];
            }

            // Sync questions to exam
            $exam->questions()->sync($questionIds);

            // Create Active Exam Session
            $startAt = Carbon::now()->subHours(2);
            $endAt = Carbon::now()->addDays(30);

            $session = ExamSession::updateOrCreate(
                ['name' => 'Sesi Ujian ' . $subject->name . ' - Gelombang Reguler'],
                [
                    'exam_id' => $exam->id,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'room' => 'Lab Komputer CBT SMA Kartika',
                    'max_participants' => 45,
                    'status' => ExamSession::STATUS_OPEN,
                    'token_prefix' => substr($examData['token'], 0, 4),
                    'instructions' => 'Periksa kembali kelengkapan soal dan jawaban Anda. Mode Kiosk Safe-Exam aktif.',
                    'allow_resume' => true,
                    'auto_submit_on_timeout' => true,
                ]
            );

            // Create Primary Token
            ExamToken::updateOrCreate(
                ['token' => $examData['token']],
                [
                    'exam_session_id' => $session->id,
                    'expires_at' => $endAt,
                    'is_active' => true,
                    'is_single_use' => false,
                    'used_count' => 0,
                ]
            );

            // Create Backup Token
            ExamToken::updateOrCreate(
                ['token' => $examData['token'] . '-ALT'],
                [
                    'exam_session_id' => $session->id,
                    'expires_at' => $endAt,
                    'is_active' => true,
                    'is_single_use' => false,
                    'used_count' => 0,
                ]
            );
        }

        // -------------------------------------------------------------
        // 5. 5 RPP / MODUL AJAR KURIKULUM MERDEKA LENGKAP
        // -------------------------------------------------------------
        $rppDefinitions = [
            'FIS' => [
                'topic' => 'Dinamika Gerak Lurus & Penerapan Hukum Newton',
                'time_allocation' => '3 x 45 Menit (Pertemuan 1-2)',
                'class' => $classXA,
                'pertemuan' => [
                    [
                        'pertemuan_ke' => 1,
                        'judul_topik' => 'Inersia dan Analisis Gaya Benda Tegar',
                        'deskripsi_aktivitas' => '1. Eksplorasi fenomena kelembaman menggunakan kereta dinamika. 2. Melukiskan diagram gaya bebas (FBD). 3. Merumuskan korelasi resultan gaya dan percepatan benda.',
                        'rekomendasi_bahan_ajar' => [
                            'Buku Siswa Fisika Fase E Kemendikbud',
                            'LKPD Eksperimen Hukum Newton',
                            'Simulasi Virtual PhET Dynamics',
                        ],
                    ],
                    [
                        'pertemuan_ke' => 2,
                        'judul_topik' => 'Aplikasi Gaya Gesek Statis dan Kinetis dalam Teknologi',
                        'deskripsi_aktivitas' => '1. Mengukur koefisien gesekan balok kayu pada kaca dan amplas. 2. Diskusi teknologi rem ABS (Anti-lock Braking System).',
                        'rekomendasi_bahan_ajar' => [
                            'Modul Interaktif CBT Kartika: Koefisien Gesek',
                            'Video Studi Kasus Pengereman Kendaraan',
                        ],
                    ],
                ],
                'tps' => [
                    [
                        'tp_id' => 'TP_FIS_01',
                        'deskripsi_tp' => 'Peserta didik mampu menerapkan Hukum I dan II Newton untuk menghitung resultan gaya dan percepatan benda pada lintasan datar dan miring.',
                        'jumlah_soal' => 3,
                        'tipe_soal' => 'Pilihan Ganda',
                        'tingkat_kesulitan' => 'Sedang',
                        'kata_kunci' => 'Gaya normal, gaya gesek, percepatan linear, diagram gaya bebas.',
                    ],
                    [
                        'tp_id' => 'TP_FIS_02',
                        'deskripsi_tp' => 'Peserta didik mampu menganalisis konsep aksi-reaksi serta membedakan gesekan statis dan kinetis dalam kehidupan praktis.',
                        'jumlah_soal' => 1,
                        'tipe_soal' => 'Esai Uraian',
                        'tingkat_kesulitan' => 'Sukar',
                        'kata_kunci' => 'Hukum III Newton, koefisien gesek, gaya kontak.',
                    ],
                ],
            ],
            'BIO' => [
                'topic' => 'Keanekaragaman Hayati dan Konservasi Ekosistem Rawa Pening',
                'time_allocation' => '3 x 45 Menit (Pertemuan 1-2)',
                'class' => $classXA,
                'pertemuan' => [
                    [
                        'pertemuan_ke' => 1,
                        'judul_topik' => 'Tingkat Biodiversitas dan Persebaran Flora-Fauna',
                        'deskripsi_aktivitas' => '1. Mengamati keanekaragaman genetik pada tanaman sekitar sekolah. 2. Memetakan zona biogeografi garis Wallace-Weber di peta Indonesia.',
                        'rekomendasi_bahan_ajar' => [
                            'Atlas Biodiversitas Indonesia',
                            'Spesimen Herbarium Mini Sekolah',
                        ],
                    ],
                    [
                        'pertemuan_ke' => 2,
                        'judul_topik' => 'Kajian Ekologis Eutrofikasi Danau Rawa Pening Banyubiru',
                        'deskripsi_aktivitas' => '1. Analisis sampel air danau dan identifikasi biomassa eceng gondok. 2. Merumuskan ide solusi konservasi berbasis ekonomi kreatif.',
                        'rekomendasi_bahan_ajar' => [
                            'Lembar Kajian Lapangan Ekosistem Air Tawar',
                            'Laporan Riset Kelimpahan Oksigen Terlarut',
                        ],
                    ],
                ],
                'tps' => [
                    [
                        'tp_id' => 'TP_BIO_01',
                        'deskripsi_tp' => 'Peserta didik dapat mengidentifikasi tingkatan keanekaragaman hayati (gen, jenis, ekosistem) dan faktor penyebab persebarannya.',
                        'jumlah_soal' => 3,
                        'tipe_soal' => 'Pilihan Ganda',
                        'tingkat_kesulitan' => 'Mudah',
                        'kata_kunci' => 'Keanekaragaman gen, spesies, bioma air tawar, Wallace.',
                    ],
                    [
                        'tp_id' => 'TP_BIO_02',
                        'deskripsi_tp' => 'Peserta didik mampu mengevaluasi ancaman kerusakan ekosistem dan merancang solusi pelestarian lingkungan lokal Banyubiru.',
                        'jumlah_soal' => 1,
                        'tipe_soal' => 'Esai Uraian',
                        'tingkat_kesulitan' => 'Sukar',
                        'kata_kunci' => 'Eutrofikasi, eceng gondok, transfer energi piramida trofik.',
                    ],
                ],
            ],
            'KIM' => [
                'topic' => 'Teori Atom Mekanika Kuantum & Sistem Periodik Unsur',
                'time_allocation' => '3 x 45 Menit (Pertemuan 1-2)',
                'class' => $classXB,
                'pertemuan' => [
                    [
                        'pertemuan_ke' => 1,
                        'judul_topik' => 'Perkembangan Model Atom dan Bilangan Kuantum',
                        'deskripsi_aktivitas' => '1. Komparasi model atom Bohr vs orbital Erwin Schrodinger. 2. Menentukan nilai bilangan kuantum utama, azimuth, magnetik, dan spin.',
                        'rekomendasi_bahan_ajar' => [
                            'Tabel Periodik Interaktif',
                            'Visualizer Model Atom 3D',
                        ],
                    ],
                    [
                        'pertemuan_ke' => 2,
                        'judul_topik' => 'Konfigurasi Elektron dan Sifat Keperiodikan Unsur',
                        'deskripsi_aktivitas' => '1. Latihan menuliskan konfigurasi elektron aturan Aufbau dan Hund. 2. Menganalisis grafik jari-jari atom dan energi ionisasi.',
                        'rekomendasi_bahan_ajar' => [
                            'Lembar Kerja Konfigurasi Elektron',
                            'Grafik Sifat Periodik Unsur',
                        ],
                    ],
                ],
                'tps' => [
                    [
                        'tp_id' => 'TP_KIM_01',
                        'deskripsi_tp' => 'Peserta didik mampu menuliskan konfigurasi elektron subkulit dan menentukan letak unsur dalam tabel periodik modern.',
                        'jumlah_soal' => 3,
                        'tipe_soal' => 'Pilihan Ganda',
                        'tingkat_kesulitan' => 'Sedang',
                        'kata_kunci' => 'Aufbau, Pauli, Hund, golongan, periode.',
                    ],
                    [
                        'tp_id' => 'TP_KIM_02',
                        'deskripsi_tp' => 'Peserta didik mampu merumuskan keempat bilangan kuantum elektron terakhir dan menganalisis kecenderungan energi ionisasi.',
                        'jumlah_soal' => 1,
                        'tipe_soal' => 'Esai Uraian',
                        'tingkat_kesulitan' => 'Sukar',
                        'kata_kunci' => 'Bilangan kuantum n-l-m-s, energi ionisasi, afinitas elektron.',
                    ],
                ],
            ],
            'BIG' => [
                'topic' => 'Analytical Exposition on Science, Technology & Environment',
                'time_allocation' => '4 x 45 Menit (Pertemuan 1-2)',
                'class' => $classXIA,
                'pertemuan' => [
                    [
                        'pertemuan_ke' => 1,
                        'judul_topik' => 'Analyzing The Structure of Analytical Exposition Texts',
                        'deskripsi_aktivitas' => '1. Reading sample texts on environmental sustainability. 2. Deconstructing thesis, arguments, and reiteration.',
                        'rekomendasi_bahan_ajar' => [
                            'English Reading Portfolio Grade XI',
                            'Interactive Vocabulary Flashcards',
                        ],
                    ],
                    [
                        'pertemuan_ke' => 2,
                        'judul_topik' => 'Developing Logical Arguments and Causal Connectors',
                        'deskripsi_aktivitas' => '1. Peer review on thesis statement formulation. 2. Applying connecting words (furthermore, consequently, thus).',
                        'rekomendasi_bahan_ajar' => [
                            'Argumentation Rubric Sheet',
                            'Grammar Guide: Modal Auxiliaries and Cause-Effect',
                        ],
                    ],
                ],
                'tps' => [
                    [
                        'tp_id' => 'TP_ENG_01',
                        'deskripsi_tp' => 'Students are able to identify communicative purposes, generic structures, and grammatical connectives in analytical exposition texts.',
                        'jumlah_soal' => 3,
                        'tipe_soal' => 'Pilihan Ganda',
                        'tingkat_kesulitan' => 'Sedang',
                        'kata_kunci' => 'Thesis, arguments, reiteration, causal connectives.',
                    ],
                    [
                        'tp_id' => 'TP_ENG_02',
                        'deskripsi_tp' => 'Students are able to compose coherent paragraphs expressing analytical arguments with proper cohesion.',
                        'jumlah_soal' => 1,
                        'tipe_soal' => 'Esai Uraian',
                        'tingkat_kesulitan' => 'Sukar',
                        'kata_kunci' => 'Essay writing, logical reasoning, evidence citation.',
                    ],
                ],
            ],
            'SJR' => [
                'topic' => 'Poros Maritim Nusantara dan Kejayaan Jalur Rempah Dunia',
                'time_allocation' => '3 x 45 Menit (Pertemuan 1-2)',
                'class' => $classXA,
                'pertemuan' => [
                    [
                        'pertemuan_ke' => 1,
                        'judul_topik' => 'Peta Niaga Laut dan Komoditas Unggulan Rempah Nusantara',
                        'deskripsi_aktivitas' => '1. Menelaah peta kuno pelayaran cengkih dan pala dari Maluku ke Selat Malaka. 2. Diskusi peran pelabuhan transit pesisir Jawa.',
                        'rekomendasi_bahan_ajar' => [
                            'Peta Rute Jalur Rempah Kemendikbudristek',
                            'Modul Sejarah Maritim SMA Kartika',
                        ],
                    ],
                    [
                        'pertemuan_ke' => 2,
                        'judul_topik' => 'Akulturasi Budaya dan Diplomasi Perdagangan Bahari',
                        'deskripsi_aktivitas' => '1. Mengidentifikasi warisan arsitektur dan bahasa lingua franca Melayu di pesisir. 2. Refleksi makna sejarah maritim bagi jati diri bangsa.',
                        'rekomendasi_bahan_ajar' => [
                            'Diktat Akulturasi Nusantara',
                            'Video Dokumenter Ekspedisi Jalur Rempah',
                        ],
                    ],
                ],
                'tps' => [
                    [
                        'tp_id' => 'TP_SJR_01',
                        'deskripsi_tp' => 'Peserta didik mampu mengidentifikasi komoditas rempah unggulan, rute perdagangan maritim, dan fungsi pelabuhan pesisir nusantara.',
                        'jumlah_soal' => 3,
                        'tipe_soal' => 'Pilihan Ganda',
                        'tingkat_kesulitan' => 'Mudah',
                        'kata_kunci' => 'Jalur rempah, Maluku, cengkih, pelabuhan transit, angin muson.',
                    ],
                    [
                        'tp_id' => 'TP_SJR_02',
                        'deskripsi_tp' => 'Peserta didik mampu menganalisis dampak multidimensi perdagangan rempah terhadap pertukaran budaya, agama, dan identitas kebangsaan.',
                        'jumlah_soal' => 1,
                        'tipe_soal' => 'Esai Uraian',
                        'tingkat_kesulitan' => 'Sukar',
                        'kata_kunci' => 'Akulturasi, lingua franca, kedaulatan maritim.',
                    ],
                ],
            ],
        ];

        foreach ($rppDefinitions as $code => $rppDef) {
            $teacher = $teachers[$code];
            $subject = $subjects[$code];
            $class = $rppDef['class'];
            $exam = $exams[$code];

            $rppJsonData = [
                'rpp_metadata' => [
                    'mata_pelajaran' => $subject->name,
                    'kelas' => (string) $class->grade,
                    'topik_utama' => $rppDef['topic'],
                    'alokasi_waktu' => $rppDef['time_allocation'],
                ],
                'lms_integration' => [
                    'pertemuan_list' => $rppDef['pertemuan'],
                ],
                'cbt_integration' => [
                    'assessment_type' => 'Sumatif',
                    'tujuan_pembelajaran_mapped' => $rppDef['tps'],
                ],
            ];

            $rpp = Rpp::updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'class_id' => $class->id,
                    'topic' => $rppDef['topic'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'academic_year' => '2026/2027',
                    'semester' => 'Ganjil',
                    'time_allocation' => $rppDef['time_allocation'],
                    'rpp_data' => $rppJsonData,
                    'status' => 'integrated',
                    'integrated_at' => Carbon::now(),
                ]
            );

            // Create RPP Materials linking to LMS materials
            $matsForSubject = $learningMaterials[$code] ?? [];
            foreach ($rppDef['pertemuan'] as $pIdx => $p) {
                $linkedMat = $matsForSubject[$pIdx] ?? null;

                RppMaterial::updateOrCreate(
                    [
                        'rpp_id' => $rpp->id,
                        'pertemuan_ke' => $p['pertemuan_ke'],
                    ],
                    [
                        'learning_material_id' => $linkedMat ? $linkedMat->id : null,
                        'judul_topik' => $p['judul_topik'],
                        'deskripsi_aktivitas' => $p['deskripsi_aktivitas'],
                        'rekomendasi_bahan_ajar' => $p['rekomendasi_bahan_ajar'],
                    ]
                );
            }

            // Create RPP Assessments linking to CBT exam
            foreach ($rppDef['tps'] as $tp) {
                RppAssessment::updateOrCreate(
                    [
                        'rpp_id' => $rpp->id,
                        'tp_id' => $tp['tp_id'],
                    ],
                    [
                        'exam_id' => $exam->id,
                        'deskripsi_tp' => $tp['deskripsi_tp'],
                        'jumlah_soal_direkomendasikan' => $tp['jumlah_soal'],
                        'tipe_soal' => $tp['tipe_soal'],
                        'tingkat_kesulitan' => $tp['tingkat_kesulitan'],
                        'kata_kunci_indokator_soal' => $tp['kata_kunci'],
                    ]
                );
            }
        }
    }
}
