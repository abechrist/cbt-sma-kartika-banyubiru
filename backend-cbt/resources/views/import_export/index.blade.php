<x-layouts.app :title="'Import & Export Data'">
    <div class="mb-8">
        <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold bg-brand-100 text-brand-800 border border-brand-200 mb-2">
            Pusat Pertukaran Data & Dapodik
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Import &amp; Export Data Massal</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Sinkronisasi data peserta didik, guru pendidik, kelas/rombel, dan butir soal dalam format CSV.</p>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="mb-6 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-6 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs font-semibold">
            {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-4 py-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- IMPORT CARD -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-800 flex items-center justify-center font-bold">
                        📥
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Import Data CSV</h2>
                        <p class="text-xs text-slate-500">Unggah berkas CSV. Baris pertama wajib memuat header kolom.</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-5">
                <!-- Import Siswa -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl hover:border-brand-300 transition">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-sm text-slate-900">Import Data Siswa</h3>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-white text-slate-600 border">CSV</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Kolom wajib: <code class="text-brand-800 font-mono">name, email</code>. Opsional: <code class="font-mono">nisn, class, gender, phone</code>.</p>
                    <form method="POST" action="{{ route('import.siswa') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        @csrf
                        <input type="file" name="file" accept=".csv,.txt" required class="flex-1 text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:border file:border-slate-300">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs transition shrink-0">
                            Unggah Siswa
                        </button>
                    </form>
                </div>

                <!-- Import Guru -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl hover:border-brand-300 transition">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-sm text-slate-900">Import Data Guru</h3>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-white text-slate-600 border">CSV</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Kolom wajib: <code class="text-brand-800 font-mono">name, email</code>. Opsional: <code class="font-mono">nip, gender, phone, address</code>.</p>
                    <form method="POST" action="{{ route('import.guru') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        @csrf
                        <input type="file" name="file" accept=".csv,.txt" required class="flex-1 text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:border file:border-slate-300">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs transition shrink-0">
                            Unggah Guru
                        </button>
                    </form>
                </div>

                <!-- Import Kelas -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl hover:border-brand-300 transition">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-sm text-slate-900">Import Rombel / Kelas</h3>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-white text-slate-600 border">CSV</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Kolom wajib: <code class="text-brand-800 font-mono">name, grade, academic_year</code>.</p>
                    <form method="POST" action="{{ route('import.kelas') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        @csrf
                        <input type="file" name="file" accept=".csv,.txt" required class="flex-1 text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:border file:border-slate-300">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs transition shrink-0">
                            Unggah Kelas
                        </button>
                    </form>
                </div>

                <!-- Import Soal -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl hover:border-brand-300 transition">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-sm text-slate-900">Import Bank Soal Massal</h3>
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-white text-slate-600 border">CSV</span>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">Kolom: <code class="text-brand-800 font-mono">question_text, type, subject, score, difficulty</code>.</p>
                    <form method="POST" action="{{ route('import.question') }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        @csrf
                        <input type="file" name="file" accept=".csv,.txt" required class="flex-1 text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-white file:text-slate-700 file:border file:border-slate-300">
                        <button type="submit" class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs transition shrink-0">
                            Unggah Soal
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- EXPORT CARD -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gold-50 text-gold-700 flex items-center justify-center font-bold">
                        📤
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Export &amp; Unduh Data</h2>
                        <p class="text-xs text-slate-500">Unduh data terkini dalam format berkas spreadsheet CSV.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-3 text-sm">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-100/70 transition">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Data Akun Siswa</h3>
                        <p class="text-xs text-slate-500">Daftar akun seluruh siswa aktif & NISN</p>
                    </div>
                    <a href="{{ route('export.siswa') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-100/70 transition">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Data Akun Guru</h3>
                        <p class="text-xs text-slate-500">Daftar tenaga pendidik & NIP</p>
                    </div>
                    <a href="{{ route('export.guru') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-100/70 transition">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Data Rombongan Belajar (Kelas)</h3>
                        <p class="text-xs text-slate-500">Daftar seluruh kelas binaan dan tingkat</p>
                    </div>
                    <a href="{{ route('export.kelas') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-100/70 transition">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Bank Butir Soal</h3>
                        <p class="text-xs text-slate-500">Seluruh butir soal dan kunci jawaban</p>
                    </div>
                    <a href="{{ route('export.question') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-100/70 transition">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Daftar Peserta Ujian</h3>
                        <p class="text-xs text-slate-500">Log kehadiran peserta dalam seluruh sesi</p>
                    </div>
                    <a href="{{ route('export.participant') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-100/70 transition">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Rekapitulasi Nilai Akhir</h3>
                        <p class="text-xs text-slate-500">Hasil skor & evaluasi seluruh peserta</p>
                    </div>
                    <a href="{{ route('export.rekap_nilai') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                        Unduh CSV
                    </a>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200 hover:bg-slate-100/70 transition">
                    <div>
                        <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Laporan Lengkap & Distribusi</h3>
                        <p class="text-xs text-slate-500">Distribusi nilai A-E dan statistik pimpinan</p>
                    </div>
                    <a href="{{ route('export.laporan') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                        Unduh CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- DAPODIK INTEGRATION CARD -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50/70 flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                    🏛
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Integrasi Sinkronisasi Dapodik Kemendikbud</h2>
                    <p class="text-xs text-slate-500">Sinkronisasi data peserta didik offline/online dengan format resmi Dapodik. Sistem CBT beroperasi mandiri tanpa lag jaringan.</p>
                </div>
            </div>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">Unduh Format Template</h3>
                    <p class="text-xs text-slate-500 mb-4">Template standar kolom Dapodik (NISN, Nama, Rombel, dsb) untuk diisi sebelum impor.</p>
                </div>
                <a href="{{ route('dapodik.template') }}" class="w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs text-center transition block">
                    Unduh Template Dapodik
                </a>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">Import dari Dapodik</h3>
                    <p class="text-xs text-slate-500 mb-4">Pencocokan akun dan pendaftaran siswa otomatis berdasarkan NISN Dapodik.</p>
                </div>
                <form method="POST" action="{{ route('dapodik.import') }}" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <input type="file" name="file" accept=".csv,.txt" required class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-white file:border file:border-slate-300">
                    <button type="submit" class="w-full py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs transition">
                        Sinkronkan ke CBT
                    </button>
                </form>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 mb-1">Export ke Dapodik</h3>
                    <p class="text-xs text-slate-500 mb-4">Ekstrak data siswa terdaftar di CBT dalam struktur tabel sesuai validasi Dapodik.</p>
                </div>
                <a href="{{ route('dapodik.export') }}" class="w-full py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs text-center transition block">
                    Unduh CSV Format Dapodik
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>