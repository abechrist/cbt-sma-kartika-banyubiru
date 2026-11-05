<x-layouts.app :title="'Import & Export Data'">
    <div class="mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Import &amp; Export Data Massal</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Sinkronisasi data peserta didik, guru pendidik, kelas/rombel, dan butir soal dalam format CSV.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- IMPORT CARD -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 text-brand-800 border border-brand-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Import Data CSV</h2>
                        <p class="text-xs text-slate-500">Unggah berkas CSV. Baris pertama wajib memuat header kolom.</p>
                    </div>
                </div>
            </div>
            
            <div class="p-5 sm:p-6 space-y-4">
                <!-- Import Siswa -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="flex justify-between items-start mb-1.5">
                        <h3 class="font-bold text-sm text-slate-900">Import Data Siswa</h3>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-white text-slate-600 border border-slate-200">CSV</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Kolom wajib: <code class="text-brand-800 font-mono">name, email</code>. Opsional: <code class="font-mono">nisn, class, gender, phone</code>.</p>
                    <form method="POST" action="{{ route('import.siswa') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                        @csrf
                        <input type="file" name="file" accept=".csv,.txt" required class="flex-1 text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:border file:border-slate-300">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs transition shrink-0">
                            Unggah Siswa
                        </button>
                    </form>
                </div>

                <!-- Import Guru -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="flex justify-between items-start mb-1.5">
                        <h3 class="font-bold text-sm text-slate-900">Import Data Guru</h3>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-white text-slate-600 border border-slate-200">CSV</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Kolom wajib: <code class="text-brand-800 font-mono">name, email</code>. Opsional: <code class="font-mono">nip, gender, phone, address</code>.</p>
                    <form method="POST" action="{{ route('import.guru') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                        @csrf
                        <input type="file" name="file" accept=".csv,.txt" required class="flex-1 text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:border file:border-slate-300">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs transition shrink-0">
                            Unggah Guru
                        </button>
                    </form>
                </div>

                <!-- Import Kelas -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="flex justify-between items-start mb-1.5">
                        <h3 class="font-bold text-sm text-slate-900">Import Rombel / Kelas</h3>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-white text-slate-600 border border-slate-200">CSV</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Kolom wajib: <code class="text-brand-800 font-mono">name, grade, academic_year</code>.</p>
                    <form method="POST" action="{{ route('import.kelas') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                        @csrf
                        <input type="file" name="file" accept=".csv,.txt" required class="flex-1 text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:border file:border-slate-300">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs transition shrink-0">
                            Unggah Kelas
                        </button>
                    </form>
                </div>

                <!-- Import Soal -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="flex justify-between items-start mb-1.5">
                        <h3 class="font-bold text-sm text-slate-900">Import Bank Soal Massal</h3>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-white text-slate-600 border border-slate-200">CSV</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Kolom: <code class="text-brand-800 font-mono">question_text, type, subject, score, difficulty</code>.</p>
                    <form method="POST" action="{{ route('import.question') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                        @csrf
                        <input type="file" name="file" accept=".csv,.txt" required class="flex-1 text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:border file:border-slate-300">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs transition shrink-0">
                            Unggah Soal
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- EXPORT CARD -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gold-50 text-gold-700 border border-gold-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Export &amp; Unduh Data</h2>
                        <p class="text-xs text-slate-500">Unduh data terkini dalam format berkas spreadsheet CSV.</p>
                    </div>
                </div>
            </div>

            <div class="p-5 sm:p-6 space-y-3 text-sm">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Data Akun Siswa</h3>
                        <p class="text-xs text-slate-500">Daftar akun seluruh siswa aktif &amp; NISN</p>
                    </div>
                    <a href="{{ route('export.siswa') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Data Akun Guru</h3>
                        <p class="text-xs text-slate-500">Daftar tenaga pendidik &amp; NIP</p>
                    </div>
                    <a href="{{ route('export.guru') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Data Rombongan Belajar (Kelas)</h3>
                        <p class="text-xs text-slate-500">Daftar seluruh kelas binaan dan tingkat</p>
                    </div>
                    <a href="{{ route('export.kelas') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Bank Butir Soal</h3>
                        <p class="text-xs text-slate-500">Seluruh butir soal dan kunci jawaban</p>
                    </div>
                    <a href="{{ route('export.question') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Daftar Peserta Ujian</h3>
                        <p class="text-xs text-slate-500">Log kehadiran peserta dalam seluruh sesi</p>
                    </div>
                    <a href="{{ route('export.participant') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Rekapitulasi Nilai Akhir</h3>
                        <p class="text-xs text-slate-500">Hasil skor &amp; evaluasi seluruh peserta</p>
                    </div>
                    <a href="{{ route('export.rekap_nilai') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg border border-slate-200">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Laporan Lengkap &amp; Distribusi</h3>
                        <p class="text-xs text-slate-500">Distribusi nilai A-E dan statistik pimpinan</p>
                    </div>
                    <a href="{{ route('export.laporan') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">
                        Unduh CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- DAPODIK INTEGRATION CARD -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Integrasi Sinkronisasi Data Dapodik</h2>
                    <p class="text-xs text-slate-500">Sinkronisasi data peserta didik dengan format tabel standar Dapodik untuk administrasi akademik sekolah.</p>
                </div>
            </div>
        </div>
        
        <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">Unduh Format Template</h3>
                    <p class="text-xs text-slate-500 mb-4">Template standar kolom Dapodik (NISN, Nama, Rombel, dsb) untuk diisi sebelum impor.</p>
                </div>
                <a href="{{ route('dapodik.template') }}" class="w-full py-2 rounded-lg bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs text-center transition block">
                    Unduh Template Dapodik
                </a>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">Import dari Dapodik</h3>
                    <p class="text-xs text-slate-500 mb-4">Pencocokan akun dan pendaftaran siswa otomatis berdasarkan NISN Dapodik.</p>
                </div>
                <form method="POST" action="{{ route('dapodik.import') }}" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <input type="file" name="file" accept=".csv,.txt" required class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2 file:rounded-md file:text-xs file:font-semibold file:bg-white file:border file:border-slate-300">
                    <button type="submit" class="w-full py-2 rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs transition">
                        Sinkronkan Data Siswa
                    </button>
                </form>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">Export ke Dapodik</h3>
                    <p class="text-xs text-slate-500 mb-4">Ekstrak data siswa terdaftar dalam struktur tabel sesuai validasi Dapodik.</p>
                </div>
                <a href="{{ route('dapodik.export') }}" class="w-full py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs text-center transition block">
                    Unduh CSV Format Dapodik
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
