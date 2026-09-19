<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $subject = Subject::where('code', 'MTK')->first();
        $class = StudentClass::first();
        $guru = User::where('email', 'budi@kartika.sch.id')->first();

        if (! $subject || ! $class || ! $guru) {
            return;
        }

        // Soal PG (20 soal) - Matematika
        $pgQuestions = [
            ['Hasil dari 25 + 17 adalah...', 'medium', 5, ['A' => '40', 'B' => '42', 'C' => '43', 'D' => '45', 'correct' => 'B']],
            ['Hasil dari 15 × 8 adalah...', 'easy', 5, ['A' => '100', 'B' => '110', 'C' => '120', 'D' => '130', 'correct' => 'C']],
            ['Hasil dari 96 ÷ 12 adalah...', 'easy', 5, ['A' => '6', 'B' => '7', 'C' => '8', 'D' => '9', 'correct' => 'C']],
            ['Hasil dari 5² adalah...', 'easy', 5, ['A' => '10', 'B' => '15', 'C' => '20', 'D' => '25', 'correct' => 'D']],
            ['Akar kuadrat dari 144 adalah...', 'medium', 5, ['A' => '10', 'B' => '11', 'C' => '12', 'D' => '13', 'correct' => 'C']],
            ['Hasil dari 3x + 2x adalah...', 'easy', 5, ['A' => '5x', 'B' => '6x', 'C' => '5x²', 'D' => '6x²', 'correct' => 'A']],
            ['Nilai x dari persamaan 2x + 4 = 12 adalah...', 'medium', 5, ['A' => '2', 'B' => '3', 'C' => '4', 'D' => '5', 'correct' => 'C']],
            ['Faktorisasi dari x² - 9 adalah...', 'medium', 5, ['A' => '(x+3)(x-3)', 'B' => '(x+9)(x-9)', 'C' => '(x+3)²', 'D' => '(x-3)²', 'correct' => 'A']],
            ['Gradien garis y = 2x + 3 adalah...', 'medium', 5, ['A' => '2', 'B' => '3', 'C' => '-2', 'D' => '-3', 'correct' => 'A']],
            ['Hasil dari (a⁴)² adalah...', 'easy', 5, ['A' => 'a⁶', 'B' => 'a⁸', 'C' => 'a¹⁶', 'D' => 'a²', 'correct' => 'B']],
            ['Jumlah sudut dalam segitiga adalah...', 'easy', 5, ['A' => '90°', 'B' => '180°', 'C' => '270°', 'D' => '360°', 'correct' => 'B']],
            ['Luas persegi dengan sisi 7 cm adalah...', 'easy', 5, ['A' => '14 cm²', 'B' => '28 cm²', 'C' => '49 cm²', 'D' => '56 cm²', 'correct' => 'C']],
            ['Keliling lingkaran dengan jari-jari 7 cm (π = 22/7) adalah...', 'hard', 5, ['A' => '22 cm', 'B' => '44 cm', 'C' => '88 cm', 'D' => '154 cm', 'correct' => 'B']],
            ['Volume kubus dengan rusuk 5 cm adalah...', 'medium', 5, ['A' => '25 cm³', 'B' => '50 cm³', 'C' => '100 cm³', 'D' => '125 cm³', 'correct' => 'D']],
            ['Koordinat titik puncak fungsi y = x² - 4x + 3 adalah...', 'hard', 5, ['A' => '(2, -1)', 'B' => '(2, 1)', 'C' => '(-2, -1)', 'D' => '(-2, 1)', 'correct' => 'A']],
            ['Hasil dari 2/3 + 1/6 adalah...', 'medium', 5, ['A' => '3/9', 'B' => '4/6', 'C' => '5/6', 'D' => '3/6', 'correct' => 'C']],
            ['Persentase dari 15 dari 60 adalah...', 'medium', 5, ['A' => '15%', 'B' => '25%', 'C' => '30%', 'D' => '40%', 'correct' => 'B']],
            ['Himpunan penyelesaian dari 3x - 6 = x + 10 adalah...', 'medium', 5, ['A' => 'x = 4', 'B' => 'x = 6', 'C' => 'x = 8', 'D' => 'x = 10', 'correct' => 'C']],
            ['Barisan aritmetika 3, 7, 11, 15, ... suku ke-10 adalah...', 'hard', 5, ['A' => '35', 'B' => '39', 'C' => '43', 'D' => '47', 'correct' => 'B']],
            ['Jika sin θ = 3/5, maka cos θ adalah...', 'hard', 5, ['A' => '4/5', 'B' => '3/4', 'C' => '5/4', 'D' => '5/3', 'correct' => 'A']],
        ];

        foreach ($pgQuestions as $i => [$text, $difficulty, $score, $opts]) {
            $question = Question::create([
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'type' => Question::TYPE_PG,
                'question_text' => $text,
                'difficulty' => $difficulty,
                'score' => $score,
                'competency_code' => 'MTK-3.'.($i + 1),
                'is_active' => true,
                'created_by' => $guru->id,
            ]);

            $labels = ['A', 'B', 'C', 'D'];
            foreach ($labels as $label) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'label' => $label,
                    'option_text' => $opts[$label],
                    'is_correct' => $opts['correct'] === $label,
                    'sort_order' => array_search($label, $labels) + 1,
                ]);
            }
        }

        // Soal Isian Singkat (5 soal)
        $isianQuestions = [
            ['Hasil dari 7 × 8 adalah?', 'easy', 5, '56'],
            ['Hasil dari 121 ÷ 11 adalah?', 'medium', 5, '11'],
            ['Nilai dari 2³ × 3² adalah?', 'medium', 5, '72'],
            ['Luas segitiga dengan alas 10 cm dan tinggi 6 cm (dalam cm²) adalah?', 'medium', 5, '30'],
            ['Fungsi f(x) = 2x + 1, nilai f(3) = ?', 'easy', 5, '7'],
        ];

        foreach ($isianQuestions as $i => [$text, $difficulty, $score, $answer]) {
            $question = Question::create([
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'type' => Question::TYPE_ISIAN_SINGKAT,
                'question_text' => $text,
                'difficulty' => $difficulty,
                'score' => $score,
                'competency_code' => 'MTK-4.'.($i + 1),
                'is_active' => true,
                'created_by' => $guru->id,
            ]);

            QuestionOption::create([
                'question_id' => $question->id,
                'label' => 'KUNCI',
                'option_text' => $answer,
                'is_correct' => true,
                'sort_order' => 1,
            ]);
        }

        // Soal Benar/Salah (5 soal)
        $benarSalahQuestions = [
            ['Bilangan prima terkecil adalah 1.', 'easy', 5, 'salah'],
            ['Setiap persegi adalah persegi panjang.', 'medium', 5, 'benar'],
            ['0 dibagi dengan bilangan berapa pun hasilnya adalah 0.', 'easy', 5, 'benar'],
            ['Akar kuadrat dari 225 adalah 15.', 'medium', 5, 'benar'],
            ['Hasil dari (-3) × (-4) adalah -12.', 'easy', 5, 'salah'],
        ];

        foreach ($benarSalahQuestions as $i => [$text, $difficulty, $score, $answer]) {
            $question = Question::create([
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'type' => Question::TYPE_BENAR_SALAH,
                'question_text' => $text,
                'difficulty' => $difficulty,
                'score' => $score,
                'competency_code' => 'MTK-5.'.($i + 1),
                'is_active' => true,
                'created_by' => $guru->id,
            ]);

            QuestionOption::create([
                'question_id' => $question->id,
                'label' => 'BENAR',
                'option_text' => 'Benar',
                'is_correct' => $answer === 'benar',
                'sort_order' => 1,
            ]);

            QuestionOption::create([
                'question_id' => $question->id,
                'label' => 'SALAH',
                'option_text' => 'Salah',
                'is_correct' => $answer === 'salah',
                'sort_order' => 2,
            ]);
        }

        // Soal PG Kompleks (2 soal)
        $pgKompleksQuestions = [
            ['Manakah pernyataan berikut yang BENAR tentang bilangan prima? (pilih semua pernyataan yang benar)', 'medium', 5, [
                'A' => ['2 adalah bilangan prima', true],
                'B' => ['1 adalah bilangan prima', false],
                'C' => ['3 adalah bilangan prima', true],
                'D' => ['9 adalah bilangan prima', false],
            ]],
            ['Manakah karakteristik berikut yang benar tentang persegi? (pilih semua yang benar)', 'easy', 5, [
                'A' => ['Memiliki 4 sisi sama panjang', true],
                'B' => ['Semua sudutnya 90°', true],
                'C' => ['Memiliki 3 sisi', false],
                'D' => ['Diagonalnya sama panjang', true],
            ]],
        ];

        foreach ($pgKompleksQuestions as $i => [$text, $difficulty, $score, $opts]) {
            $question = Question::create([
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'type' => Question::TYPE_PG_KOMPLEKS,
                'question_text' => $text,
                'difficulty' => $difficulty,
                'score' => $score,
                'competency_code' => 'MTK-6.'.($i + 1),
                'is_active' => true,
                'created_by' => $guru->id,
            ]);

            foreach ($opts as $label => [$textOpt, $correct]) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'label' => $label,
                    'option_text' => $textOpt,
                    'is_correct' => $correct,
                    'sort_order' => array_search($label, array_keys($opts)) + 1,
                ]);
            }
        }

        // Soal Menjodohkan (2 soal)
        $menjodohkanQuestions = [
            ['Pasangkan operasi hitung berikut dengan hasil yang benar!', 'medium', 5, [
                'A' => ['7 × 8', '56'],
                'B' => ['96 ÷ 12', '8'],
                'C' => ['15 × 8', '120'],
                'D' => ['121 ÷ 11', '11'],
            ]],
            ['Pasangkan nama bangun datar dengan sifat yang sesuai!', 'easy', 5, [
                'A' => ['Persegi', '4 sisi sama panjang'],
                'B' => ['Segitiga', '3 sisi'],
                'C' => ['Lingkaran', 'Tidak memiliki sisi lurus'],
                'D' => ['Persegi panjang', '2 pasang sisi sama panjang'],
            ]],
        ];

        foreach ($menjodohkanQuestions as $i => [$text, $difficulty, $score, $pairs]) {
            $question = Question::create([
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'type' => Question::TYPE_MENJODOHKAN,
                'question_text' => $text,
                'difficulty' => $difficulty,
                'score' => $score,
                'competency_code' => 'MTK-7.'.($i + 1),
                'is_active' => true,
                'created_by' => $guru->id,
            ]);

            foreach ($pairs as $label => [$textOpt, $match]) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'label' => $label,
                    'option_text' => $textOpt,
                    'correct_match' => $match,
                    'is_correct' => false,
                    'sort_order' => array_search($label, array_keys($pairs)) + 1,
                ]);
            }
        }

        // Soal Esai (3 soal) - dikoreksi manual
        $esaiQuestions = [
            ['Jelaskan langkah-langkah menghitung luas lingkaran beserta contohnya!', 'hard', 10],
            ['Sebuah persegi panjang memiliki panjang 12 cm dan lebar 5 cm. Tuliskan langkah-langkah menghitung luas dan kelilingnya!', 'medium', 10],
            ['Tentukan himpunan penyelesaian dari 2x - 3 = 7 dan jelaskan cara mencarinya!', 'medium', 10],
        ];

        foreach ($esaiQuestions as $i => [$text, $difficulty, $score]) {
            Question::create([
                'subject_id' => $subject->id,
                'class_id' => $class->id,
                'type' => Question::TYPE_ESAI,
                'question_text' => $text,
                'difficulty' => $difficulty,
                'score' => $score,
                'competency_code' => 'MTK-8.'.($i + 1),
                'is_active' => true,
                'created_by' => $guru->id,
            ]);
        }
    }
}
