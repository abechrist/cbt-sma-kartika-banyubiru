<?php

namespace App\Http\Controllers\ImportExport;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportController extends Controller
{
    private const ROLE_SISWA = 'siswa';

    private const ROLE_GURU = 'guru';

    public function index()
    {
        return view('import_export.index');
    }

    /* ------------------------------------------------------------------ */
    /* IMPORT */
    /* ------------------------------------------------------------------ */

    public function importSiswa(Request $request)
    {
        return $this->importUsers($request, self::ROLE_SISWA, 'User <span class="text-green-600">siswa</span> berhasil diimpor.');
    }

    public function importGuru(Request $request)
    {
        return $this->importUsers($request, self::ROLE_GURU, 'Data <span class="text-green-600">guru</span> berhasil diimpor.');
    }

    public function importKelas(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        [$rows, $headers] = $this->readCsv($request->file('file'));

        $required = ['name', 'grade', 'academic_year'];
        if (! $this->hasColumns($headers, $required)) {
            return back()->with('error', 'CSV harus memiliki kolom: '.implode(', ', $required));
        }

        $imported = 0;
        foreach ($rows as $row) {
            if (! $row['name'] || $row['name'] === '') {
                continue;
            }
            StudentClass::updateOrCreate(
                ['name' => $row['name'], 'academic_year' => $row['academic_year'] ?? now()->year.'/'.(now()->year + 1)],
                [
                    'grade' => (int) ($row['grade'] ?? 10),
                    'description' => $row['description'] ?? null,
                    'is_active' => isset($row['is_active']) ? filter_var($row['is_active'], FILTER_VALIDATE_BOOLEAN) : true,
                ]
            );
            $imported++;
        }

        return back()->with('success', "{$imported} kelas berhasil diimpor.");
    }

    public function importQuestion(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        [$rows, $headers] = $this->readCsv($request->file('file'));

        $required = ['question_text', 'type', 'subject'];
        if (! $this->hasColumns($headers, $required)) {
            return back()->with('error', 'CSV harus memiliki kolom: '.implode(', ', $required));
        }

        $imported = 0;
        $errors = [];
        foreach ($rows as $index => $row) {
            if (! $row['question_text']) {
                continue;
            }
            $subject = Subject::where('name', $row['subject'])->first();
            if (! $subject) {
                $subject = Subject::create(['name' => $row['subject'], 'code' => 'SUB-'.Str::upper(Str::random(4))]);
            }

            $type = $row['type'];
            if (! in_array($type, [
                Question::TYPE_PG, Question::TYPE_ESAI, Question::TYPE_BENAR_SALAH,
                Question::TYPE_PG_KOMPLEKS, Question::TYPE_MENJODOHKAN,
            ])) {
                $errors[] = 'Baris '.($index + 2).": tipe soal tidak dikenal ('{$type}').";

                continue;
            }

            Question::create([
                'question_text' => $row['question_text'],
                'type' => $type,
                'score' => (float) ($row['score'] ?? 1),
                'difficulty' => in_array($row['difficulty'] ?? '', ['easy', 'medium', 'hard']) ? $row['difficulty'] : 'medium',
                'subject_id' => $subject->id,
                'answer_type' => $row['answer_type'] ?? 'auto',
                'created_by' => auth()->id(),
                'is_active' => true,
            ]);
            $imported++;
        }

        $message = "{$imported} soal berhasil diimpor.";
        if ($errors) {
            $message .= ' Perhatian: '.implode(' ', $errors);

            return back()->with('warning', $message);
        }

        return back()->with('success', $message);
    }

    private function importUsers(Request $request, string $roleName, string $successMsg)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        [$rows, $headers] = $this->readCsv($request->file('file'));

        $required = ['name', 'email'];
        if (! $this->hasColumns($headers, $required)) {
            return back()->with('error', 'CSV harus memiliki kolom: '.implode(', ', $required));
        }

        $role = Role::where('name', $roleName)->first();
        if (! $role) {
            return back()->with('error', 'Peran '.$roleName.' tidak ditemukan.');
        }

        $imported = 0;
        $updated = 0;
        $errors = [];
        $password = 'password';

        foreach ($rows as $index => $row) {
            if (! $row['name'] || ! $row['email']) {
                continue;
            }
            $classId = null;
            if (! empty($row['class'])) {
                $class = StudentClass::where('name', $row['class'])->first();
                $classId = $class?->id;
                if (! $class) {
                    $errors[] = 'Baris '.($index + 2).": kelas '{$row['class']}' tidak ditemukan.";

                    continue;
                }
            }

            $data = [
                'name' => $row['name'],
                'email' => $row['email'],
                'role_id' => $role->id,
                'class_id' => $classId,
                'nisn' => $row['nisn'] ?? null,
                'nip' => $row['nip'] ?? null,
                'phone' => $row['phone'] ?? null,
                'gender' => $row['gender'] ?? null,
                'address' => $row['address'] ?? null,
                'is_active' => isset($row['is_active']) ? filter_var($row['is_active'], FILTER_VALIDATE_BOOLEAN) : true,
            ];

            $existing = User::where('email', $row['email'])->first();
            if ($existing) {
                $existing->update($data);
                $updated++;
            } else {
                $data['password'] = Hash::make($password);
                User::create($data);
                $imported++;
            }
        }

        $msg = "{$imported} baru diimpor, {$updated} diperbarui. ".$successMsg;
        if ($errors) {
            $msg .= ' Perhatian: '.implode(' ', $errors);

            return back()->with('warning', $msg);
        }

        return back()->with('success', $msg);
    }

    /* ------------------------------------------------------------------ */
    /* EXPORT */
    /* ------------------------------------------------------------------ */

    public function exportSiswa()
    {
        $users = User::whereHas('role', fn ($q) => $q->where('name', self::ROLE_SISWA))
            ->with('class')->orderBy('name')->get();

        return $this->streamCsv('siswa.csv', [
            'name', 'email', 'nisn', 'class', 'gender', 'birth_date', 'phone', 'address', 'is_active',
        ], $users->map(fn ($u) => [
            $u->name, $u->email, $u->nisn, optional($u->class)->name, $u->gender,
            $u->birth_date?->format('Y-m-d'), $u->phone, $u->address, $u->is_active ? 'true' : 'false',
        ]));
    }

    public function exportGuru()
    {
        $users = User::whereHas('role', fn ($q) => $q->where('name', self::ROLE_GURU))
            ->orderBy('name')->get();

        return $this->streamCsv('guru.csv', [
            'name', 'email', 'nip', 'gender', 'phone', 'address', 'is_active',
        ], $users->map(fn ($u) => [
            $u->name, $u->email, $u->nip, $u->gender, $u->phone, $u->address, $u->is_active ? 'true' : 'false',
        ]));
    }

    public function exportKelas()
    {
        $classes = StudentClass::orderBy('name')->get();

        return $this->streamCsv('kelas.csv', [
            'name', 'grade', 'academic_year', 'description', 'is_active',
        ], $classes->map(fn ($c) => [
            $c->name, $c->grade, $c->academic_year, $c->description, $c->is_active ? 'true' : 'false',
        ]));
    }

    public function exportQuestion(Request $request)
    {
        $query = Question::with('subject');
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        $questions = $query->orderBy('id')->get();

        $headers = ['question_text', 'type', 'subject', 'score', 'difficulty', 'is_active'];
        $rows = $questions->map(fn ($q) => [
            $q->question_text, $q->type, optional($q->subject)->name, $q->score,
            $q->difficulty, $q->is_active ? 'true' : 'false',
        ]);

        return $this->streamCsv('soal.csv', $headers, $rows);
    }

    public function exportParticipant()
    {
        $attempts = ExamAttempt::with(['user', 'session.exam'])->orderBy('session_id')->get();

        return $this->streamCsv('peserta.csv', [
            'No', 'Siswa', 'Email', 'Ujian', 'Sesi', 'Status', 'Dibuka', 'Diserahkan',
        ], $attempts->map(fn ($a, $i) => [
            $i + 1, optional($a->user)->name, optional($a->user)->email,
            optional(optional($a->session)->exam)->name, optional($a->session)->name,
            $a->status, $a->started_at?->format('Y-m-d H:i'), $a->submitted_at?->format('Y-m-d H:i'),
        ]));
    }

    public function exportRekapNilai(Request $request)
    {
        $query = ExamResult::with(['attempt.user', 'attempt.session.exam', 'attempt.session.exam.subject']);

        if ($request->filled('exam_id')) {
            $query->whereHas('attempt.session', fn ($q) => $q->where('exam_id', $request->exam_id));
        }
        if ($request->filled('class_id')) {
            $query->whereHas('attempt.user', fn ($q) => $q->where('class_id', $request->class_id));
        }

        $results = $query->orderBy('id')->get();

        $rows = $results->map(fn ($r) => [
            optional($r->attempt)->user?->name,
            optional($r->attempt)->user?->nisn,
            optional(optional($r->attempt)->session)->exam?->name,
            optional(optional($r->attempt)->session)->exam?->subject?->name,
            $r->total_score,
            $r->max_possible_score,
            number_format((float) $r->percentage, 1).'%',
            $r->correct_count,
            $r->incorrect_count,
            $r->unanswered_count,
            $r->grading_status,
        ]);

        return $this->streamCsv('rekap_nilai.csv', [
            'Siswa', 'NISN', 'Ujian', 'Mapel', 'Total', 'Maks', 'Persentase',
            'Benar', 'Salah', 'Tidak Dijawab', 'Status',
        ], $rows);
    }

    public function exportLaporan(Request $request)
    {
        $query = ExamResult::with(['attempt.user', 'attempt.session.exam.subject']);

        if ($request->filled('exam_id')) {
            $query->whereHas('attempt.session', fn ($q) => $q->where('exam_id', $request->exam_id));
        }

        $results = $query->get();
        $all = $results->map(fn ($r) => (float) $r->percentage);

        $summary = [
            'Total Hasil', $all->count(),
            'Rata-rata', $all->count() ? round($all->avg(), 1).'%' : '-',
            'Nilai Tertinggi', $all->count() ? max($all).'%' : '-',
            'Nilai Terendah', $all->count() ? min($all).'%' : '-',
            'Grade A (>=90)', $all->filter(fn ($p) => $p >= 90)->count(),
            'Grade B (80-89)', $all->filter(fn ($p) => $p >= 80 && $p < 90)->count(),
            'Grade C (70-79)', $all->filter(fn ($p) => $p >= 70 && $p < 80)->count(),
            'Grade D (60-69)', $all->filter(fn ($p) => $p >= 60 && $p < 70)->count(),
            'Grade E (<60)', $all->filter(fn ($p) => $p < 60)->count(),
            'Status Completed', $results->where('grading_status', 'completed')->count(),
            'Status Partial', $results->where('grading_status', 'partial')->count(),
        ];

        $detailHeaders = ['Siswa', 'NISN', 'Ujian', 'Mapel', 'Nilai', 'Status'];
        $detailRows = $results->map(fn ($r) => [
            optional($r->attempt)->user?->name,
            optional($r->attempt)->user?->nisn,
            optional(optional($r->attempt)->session)->exam?->name,
            optional(optional($r->attempt)->session)->exam?->subject?->name,
            number_format((float) $r->percentage, 1).'%',
            $r->grading_status,
        ]);

        $lines = ['Laporan Hasil Ujian CBT SMA Kartika III-1 Banyubiru', 'Dibuat: '.now()->format('Y-m-d H:i'), ''];
        $lines[] = 'LAPORAN';
        $lines[] = implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', (string) $v).'"', $summary))."\n";
        $lines[] = 'RINCIAN';
        $lines[] = implode(',', array_map(fn ($h) => '"'.$h.'"', $detailHeaders));
        foreach ($detailRows as $row) {
            $lines[] = implode(',', array_map(fn ($v) => '"'.str_replace('"', '""', (string) $v).'"', $row));
        }

        $filename = 'laporan_hasil_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($lines) {
            echo implode("\n", $lines);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /* ------------------------------------------------------------------ */
    /* HELPERS */
    /* ------------------------------------------------------------------ */

    private function streamCsv(string $filename, array $headers, $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, (array) $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function readCsv($file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $headers = array_map(fn ($h) => strtolower(trim((string) $h, "\xEF\xBB\xBF")), $headers);

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            $row = [];
            foreach ($headers as $i => $name) {
                $row[$name] = $data[$i] ?? null;
            }
            $rows[] = $row;
        }
        fclose($handle);

        return [$rows, $headers];
    }

    private function hasColumns(array $headers, array $required): bool
    {
        return count(array_intersect($required, $headers)) === count($required);
    }
}
