<?php

namespace App\Http\Controllers\ImportExport;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Dapodik integration (agent-prompt #38, PRD "Integrasi dengan Dapodik").
 *
 * Dapodik exposes a web-service API, but per the agent-prompt "Integrasi
 * eksternal tidak boleh menghambat operasi CBT lokal". This controller offers a
 * CSV-based sync that mirrors the fields Dapodik exports for peserta didik
 * (murid): NISN, Nama, Jenis Kelamin, Tempat/Tanggal Lahir, Alamat, Rombel.
 * Import matches on NISN (Dapodik's stable primary key). A live web-service
 * sync can be added later behind these same methods without affecting local CBT.
 */
class DapodikController extends Controller
{
    private const ROLE_SISWA = 'siswa';

    private function csvHasColumns(array $headers, array $required): bool
    {
        return count(array_intersect($required, $headers)) === count($required);
    }

    private function readCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        $headers = array_map(fn ($h) => strtolower(trim((string) $h, "\xEF\xBB\xBF")), fgetcsv($handle));

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

    /** Map Dapodik gender codes (L/P/W)/friends lists to our L/P. */
    private function mapGender($val): ?string
    {
        if ($val === null) {
            return null;
        }
        $val = strtoupper(trim((string) $val));
        if ($val === 'P' || $val === 'PEREMPUAN' || $val === 'F' || $val === 'FEMALE') {
            return 'P';
        }
        if ($val === 'L' || $val === 'LAKI-LAKI' || $val === 'M' || $val === 'MALE') {
            return 'L';
        }

        return null;
    }

    /* ------------------------------------------------------------------ */
    /* TEMPLATE */
    /* ------------------------------------------------------------------ */

    public function template()
    {
        $headers = ['nisn', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'rombel'];

        return $this->streamCsv('dapodik_import_template.csv', $headers, [
            ['0000000000', 'Nama Lengkap Siswa', 'L', '2010-01-01', 'Alamat lengkap', 'X-A'],
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* IMPORT */
    /* ------------------------------------------------------------------ */

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        [$rows, $headers] = $this->readCsv($request->file('file')->getRealPath());

        // Accept either Dapodik field names or our internal ones.
        $required = ['nama', 'nisn'];
        if (! $this->csvHasColumns($headers, $required)) {
            return back()->with('error', 'CSV Dapodik harus memuat kolom: '.implode(', ', $required).' (bisa disertai jenis_kelamin, tanggal_lahir, alamat, rombel).');
        }

        $role = Role::where('name', self::ROLE_SISWA)->first();
        if (! $role) {
            return back()->with('error', 'Peran '.self::ROLE_SISWA.' tidak ditemukan.');
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $idx => $row) {
            $nama = trim((string) ($row['nama'] ?? ''));
            $nisn = strval($row['nisn'] ?? '');

            if ($nama === '' || $nisn === '') {
                $skipped++;

                continue;
            }

            $classId = null;
            if (! empty($row['rombel'])) {
                $rombel = trim((string) $row['rombel']);
                $class = StudentClass::where('name', $rombel)->first();
                $classId = $class?->id;
                if (! $class) {
                    $errors[] = 'Baris '.($idx + 2).": rombel '{$rombel}' tidak ditemukan di CBT.";
                }
            }

            $email = trim((string) ($row['email'] ?? '')) ?: $nisn.'@kartika.sch.id';
            $birth = null;
            if (! empty($row['tanggal_lahir'])) {
                try {
                    $birth = Carbon::parse($row['tanggal_lahir'])->format('Y-m-d');
                } catch (\Throwable $e) {
                    $birth = null;
                }
            }

            $data = [
                'name' => $nama,
                'email' => $email,
                'role_id' => $role->id,
                'class_id' => $classId,
                'nisn' => $nisn,
                'gender' => $this->mapGender($row['jenis_kelamin'] ?? null),
                'birth_date' => $birth,
                'address' => $row['alamat'] ?? null,
                'is_active' => true,
            ];

            $existing = User::where('nisn', $nisn)->orWhere('email', $email)->first();
            if ($existing) {
                $existing->update($data);
                $updated++;
            } else {
                $data['password'] = Hash::make('password');
                User::create($data);
                $imported++;
            }
        }

        $msg = "Dapodik: {$imported} siswa baru, {$updated} diperbarui, {$skipped} dilewati tanpa NISN.";
        if ($errors) {
            $msg .= ' Perhatian: '.implode(' ', $errors);

            return back()->with('warning', $msg);
        }

        return back()->with('success', $msg);
    }

    /* ------------------------------------------------------------------ */
    /* EXPORT */
    /* ------------------------------------------------------------------ */

    public function export()
    {
        $students = User::whereHas('role', fn ($q) => $q->where('name', self::ROLE_SISWA))
            ->with('class')->orderBy('name')->get();

        return $this->streamCsv('dapodik_siswa.csv', [
            'nisn', 'nama', 'email', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'rombel',
        ], $students->map(fn ($u) => [
            $u->nisn, $u->name, $u->email, $u->gender,
            $u->birth_date?->format('Y-m-d'), $u->address, optional($u->class)->name,
        ]));
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
}
