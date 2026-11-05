<x-layouts.app :title="'Dashboard Utama'">
    <!-- School Assessment UI Kit Page Header -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">
                Selamat Datang, {{ auth()->user()->name }}!
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed">
                Sistem Asesmen & Pembelajaran SMA Kartika III-1 Banyubiru. Kelola modul asesmen, jadwal sesi ujian, dan rekapitulasi nilai akademik.
            </p>
        </div>
        <div class="self-stretch sm:self-auto shrink-0 flex items-center justify-between sm:justify-start gap-3 bg-slate-50 px-3.5 py-2.5 rounded-lg border border-slate-200">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-800 border border-brand-200 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider block sm:hidden">Tanggal</span>
            </div>
            <div class="text-right">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider hidden sm:block">Tanggal Hari Ini</div>
                <div class="text-xs sm:text-sm font-mono font-bold text-brand-900 tabular-nums">{{ now()->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    @switch(auth()->user()->role?->name)
        {{-- ================= SUPER ADMIN & ADMIN ================= --}}
        @case('super_admin')
        @case('admin')
            <!-- Assessment UI Kit Stat Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="bento-card p-4 sm:p-5">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Total Siswa</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums tracking-tight">{{ $totalStudents }}</p>
                    <p class="text-[11px] font-medium text-slate-500 mt-1 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Dari {{ $totalUsers }} pengguna
                    </p>
                </div>

                <div class="bento-card p-4 sm:p-5">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Guru / Tendik</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums tracking-tight">{{ $totalTeachers }}</p>
                    <p class="text-[11px] font-medium text-slate-500 mt-1 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        {{ $totalSubjects }} Mata Pelajaran
                    </p>
                </div>

                <div class="bento-card p-4 sm:p-5">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Bank Soal</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums tracking-tight">{{ $totalQuestions }}</p>
                    <p class="text-[11px] font-medium text-slate-500 mt-1 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $totalExams }} paket ujian
                    </p>
                </div>

                <div class="bento-card p-4 sm:p-5">
                    <div class="flex items-center justify-between text-slate-500 mb-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Sesi Aktif</span>
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-2xl sm:text-3xl font-extrabold text-brand-800 font-mono tabular-nums tracking-tight">{{ $activeSessions }}</p>
                    <p class="text-[11px] font-bold text-emerald-700 mt-1 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        Sesi Sedang Berlangsung
                    </p>
                </div>
            </div>

            <!-- Quick Action Hub -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <a href="{{ route('monitoring.index') }}" 
                    class="p-4 sm:p-5 bg-brand-950 hover:bg-brand-900 text-white rounded-xl transition flex items-center gap-3.5 border border-brand-800">
                    <div class="w-10 h-10 rounded-lg bg-brand-800 text-gold-400 flex items-center justify-center shrink-0 relative">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-white">Monitoring Ujian</h2>
                        <p class="text-xs text-brand-200 mt-0.5">Pantau kehadiran & status peserta sesi ujian</p>
                    </div>
                </a>

                <a href="{{ route('curriculum.index') }}" 
                    class="bento-card p-4 sm:p-5 text-slate-800 flex items-center gap-3.5 hover:border-brand-600">
                    <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-800 border border-teal-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-slate-900">Supervisi Kurikulum</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau RPP, LMS & penetapan Koordinator</p>
                    </div>
                </a>

                <a href="{{ route('import_export') }}" 
                    class="bento-card p-4 sm:p-5 text-slate-800 flex items-center gap-3.5 hover:border-brand-600">
                    <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-800 border border-brand-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-slate-900">Import / Export Data</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Sinkronisasi data siswa, guru, kelas & Dapodik</p>
                    </div>
                </a>

                <a href="{{ route('item-analysis.index') }}" 
                    class="bento-card p-4 sm:p-5 text-slate-800 flex items-center gap-3.5 hover:border-gold-500">
                    <div class="w-10 h-10 rounded-lg bg-gold-50 text-gold-700 border border-gold-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-slate-900">Analisis Butir Soal</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tingkat kesukaran, daya beda & distribusi</p>
                    </div>
                </a>
            </div>

            <!-- Recent Sessions Table Card -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-wrap justify-between items-center gap-3 bg-slate-50">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Sesi Ujian Terbaru</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Jadwal pelaksanaan sesi ujian yang terdaftar</p>
                    </div>
                    <a href="{{ route('sessions.index') }}" class="text-xs font-bold text-brand-800 hover:text-brand-950 flex items-center gap-1 py-1">
                        <span>Lihat Semua Sesi</span>
                        <span>→</span>
                    </a>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-bold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Nama Sesi</th>
                                <th class="px-6 py-3.5 text-left">Paket Ujian</th>
                                <th class="px-6 py-3.5 text-left">Jadwal Mulai</th>
                                <th class="px-6 py-3.5 text-left">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($recentSessions as $session)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3.5 font-bold text-slate-900">{{ $session->name }}</td>
                                <td class="px-6 py-3.5 text-slate-600 font-medium">{{ $session->exam->name ?? '-' }}</td>
                                <td class="px-6 py-3.5 font-mono text-xs text-slate-600 tabular-nums">{{ $session->start_at->format('d/m/Y H:i') }} WIB</td>
                                <td class="px-6 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-md border
                                        @switch($session->status)
                                            @case('open') @case('in_progress') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                            @case('completed') bg-slate-100 text-slate-800 border-slate-200 @break
                                            @case('cancelled') bg-rose-50 text-rose-800 border-rose-200 @break
                                            @default bg-amber-50 text-amber-800 border-amber-200
                                        @endswitch
                                    ">
                                        {{ $session->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a href="{{ route('sessions.show', $session) }}" class="px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-900 font-bold text-xs border border-brand-200 transition">
                                        Kelola Sesi
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-400 text-sm">Belum ada sesi ujian yang terdaftar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card List View -->
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse($recentSessions as $session)
                    <div class="p-4 space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm leading-snug">{{ $session->name }}</h3>
                                <p class="text-xs text-slate-600 mt-0.5">{{ $session->exam->name ?? '-' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-bold rounded-md border shrink-0
                                @switch($session->status)
                                    @case('open') @case('in_progress') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                    @case('completed') bg-slate-100 text-slate-800 border-slate-200 @break
                                    @case('cancelled') bg-rose-50 text-rose-800 border-rose-200 @break
                                    @default bg-amber-50 text-amber-800 border-amber-200
                                @endswitch
                            ">
                                {{ $session->status }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs font-mono text-slate-500 flex items-center gap-1 tabular-nums">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $session->start_at->format('d/m/Y H:i') }}
                            </span>
                            <a href="{{ route('sessions.show', $session) }}" class="min-h-[36px] px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-900 font-bold text-xs border border-brand-200 inline-flex items-center">
                                Kelola Sesi →
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400 text-sm">Belum ada sesi ujian yang terdaftar.</div>
                    @endforelse
                </div>
            </div>
            @break

        {{-- ================= KOORDINATOR KURIKULUM (WAKAKUR) ================= --}}
        @case('koordinator_kurikulum')
            <!-- Banner Supervisi Kurikulum -->
            <div class="bg-brand-950 text-white rounded-xl border border-brand-800 p-5 sm:p-6 mb-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-brand-900 border border-brand-700 text-gold-300 text-[11px] font-bold uppercase tracking-wider mb-2">
                        <span>Portal Supervisi Waka Kurikulum</span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black text-white tracking-tight">
                        Pemantauan Perencanaan (RPP), Pelaksanaan (LMS) & Evaluasi (CBT)
                    </h2>
                    <p class="text-xs sm:text-sm text-brand-200 mt-1 max-w-2xl">
                        Pantau progres {{ $totalTeachers }} guru pengampu mata pelajaran, validasi Modul Ajar/RPP, dan tinjau keterisian materi pada {{ $totalCourses }} kelas pembelajaran.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    <a href="{{ route('curriculum.index') }}" class="px-4 py-2.5 rounded-lg bg-gold-400 hover:bg-gold-500 text-brand-950 font-black text-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Buka Matriks Supervisi 30 Guru →</span>
                    </a>
                    <a href="{{ route('rpps.index') }}" class="px-4 py-2.5 rounded-lg bg-brand-800 hover:bg-brand-700 text-white font-bold text-xs border border-brand-700 transition">
                        Daftar RPP Sekolah
                    </a>
                </div>
            </div>

            <!-- 4 Kartu KPI Supervisi Kurikulum -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Guru Pengampu</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $totalTeachers }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">{{ $totalCourses }} kelas pembelajaran aktif</p>
                </div>
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Modul Ajar / RPP</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $totalRpps }}</p>
                    <p class="text-[11px] text-emerald-700 font-semibold mt-1">
                        {{ $approvedRpps }} disetujui • {{ $pendingReviewRpps }} menunggu review
                    </p>
                </div>
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Keaktifan Kelas LMS</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-brand-900 font-mono tabular-nums mt-1.5">{{ $lmsActivityRate }}%</p>
                    <p class="text-[11px] text-slate-500 mt-1">{{ $coursesWithMaterials }}/{{ $totalCourses }} kelas berisi materi ({{ $totalMaterials }} modul)</p>
                </div>
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Bank Soal & CBT</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $totalQuestions }}</p>
                    <p class="text-[11px] text-slate-500 mt-1">{{ $totalExams }} paket ujian terjadwal</p>
                </div>
            </div>

            <!-- Tabel RPP Terbaru untuk Di-supervisi -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Antrean Supervisi RPP & Modul Ajar Terbaru</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tinjau kesesuaian Tujuan Pembelajaran (TP), materi LMS, dan asesmen CBT guru</p>
                    </div>
                    <a href="{{ route('curriculum.index') }}" class="text-xs font-bold text-brand-800 hover:text-brand-950">
                        Lihat Seluruh Guru & RPP →
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-bold">
                            <tr>
                                <th class="px-5 py-3 text-left">Topik RPP</th>
                                <th class="px-5 py-3 text-left">Guru Penyusun</th>
                                <th class="px-5 py-3 text-left">Mapel & Kelas</th>
                                <th class="px-5 py-3 text-left">Status Supervisi</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentRpps as $rppItem)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3.5 font-bold text-slate-900">{{ $rppItem->topic }}</td>
                                <td class="px-5 py-3.5 text-slate-700 font-medium">{{ $rppItem->teacher->name ?? '-' }}</td>
                                <td class="px-5 py-3.5 text-xs text-slate-600">
                                    <span class="font-bold text-brand-900">{{ $rppItem->subject->name ?? '-' }}</span>
                                    • Kelas {{ $rppItem->classGroup->name ?? '-' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($rppItem->review_status === 'approved')
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200">Disetujui</span>
                                    @elseif($rppItem->review_status === 'revision_needed')
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-md bg-rose-50 text-rose-800 border border-rose-200">Perlu Revisi</span>
                                    @else
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-md bg-amber-50 text-amber-800 border border-amber-200">Menunggu Review</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('rpps.show', $rppItem) }}" class="text-xs font-bold text-brand-800 hover:text-brand-950">Tinjau RPP →</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-center text-slate-400 text-xs">Belum ada dokumen RPP yang diunggah guru.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Panel Tugas Mengajar Pribadi (Opsi A: Koordinator juga Mengajar) -->
            <div class="mb-3">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Aktivitas Mengajar Pribadi Anda</h2>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Bank Soal Saya</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $myQuestions }}</p>
                    <a href="{{ route('questions.index') }}" class="text-xs text-brand-700 font-semibold mt-1 inline-block">Lihat Bank Soal →</a>
                </div>
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Paket Ujian Saya</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $myExams }}</p>
                    <a href="{{ route('exams.index') }}" class="text-xs text-brand-700 font-semibold mt-1 inline-block">Kelola Ujian →</a>
                </div>
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sesi Ujian Saya</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $mySessions->count() }}</p>
                    <span class="text-[11px] text-slate-500 mt-1 block">Pelaksanaan terjadwal</span>
                </div>
                <div class="bg-amber-50 p-4 sm:p-5 rounded-xl border border-amber-200">
                    <span class="text-[11px] font-bold text-amber-900 uppercase tracking-wider">Antrean Koreksi</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-900 font-mono tabular-nums mt-1.5">
                        <a href="{{ route('grading.index') }}" class="hover:underline">{{ $pendingGrading }}</a>
                    </p>
                    <a href="{{ route('grading.index') }}" class="text-xs text-amber-800 font-bold mt-1 inline-block">Koreksi Sekarang →</a>
                </div>
            </div>
            @break

        {{-- ================= GURU ================= --}}
        @case('guru')
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Bank Soal Saya</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $myQuestions }}</p>
                    <a href="{{ route('questions.index') }}" class="text-xs text-brand-700 font-semibold mt-1 inline-block">Lihat Bank Soal →</a>
                </div>
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Paket Ujian Saya</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $myExams }}</p>
                    <a href="{{ route('exams.index') }}" class="text-xs text-brand-700 font-semibold mt-1 inline-block">Kelola Ujian →</a>
                </div>
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sesi Ujian Saya</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $mySessions->count() }}</p>
                    <span class="text-[11px] text-slate-500 mt-1 block">Pelaksanaan terjadwal</span>
                </div>
                <div class="bg-amber-50 p-4 sm:p-5 rounded-xl border border-amber-200">
                    <span class="text-[11px] font-bold text-amber-900 uppercase tracking-wider">Antrean Koreksi</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-900 font-mono tabular-nums mt-1.5">
                        <a href="{{ route('grading.index') }}" class="hover:underline">{{ $pendingGrading }}</a>
                    </p>
                    <a href="{{ route('grading.index') }}" class="text-xs text-amber-800 font-bold mt-1 inline-block">Koreksi Sekarang →</a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <a href="{{ route('questions.create') }}" class="p-4 bg-brand-900 hover:bg-brand-950 text-white rounded-xl transition flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-brand-800 text-white flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-white">Buat Soal Baru</h2>
                        <p class="text-xs text-brand-200 mt-0.5">PG, Menjodohkan, Esai</p>
                    </div>
                </a>
                <a href="{{ route('lms.courses.index') }}" class="bento-card p-4 hover:border-amber-400 transition flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-slate-900">LMS Kelas & Materi</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Unggah modul & buat tugas</p>
                    </div>
                </a>
                <a href="{{ route('item-analysis.index') }}" class="bento-card p-4 hover:border-brand-600 transition flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-gold-50 text-gold-700 border border-gold-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-slate-900">Analisis Butir Soal</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daya beda & kesukaran</p>
                    </div>
                </a>
                <a href="{{ route('import_export') }}" class="bento-card p-4 hover:border-brand-600 transition flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75A1.75 1.75 0 015.5 5h4l2 2h7A1.75 1.75 0 0120.25 8.75v8.5A1.75 1.75 0 0118.5 19h-13a1.75 1.75 0 01-1.75-1.75v-10.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-sm text-slate-900">Import Soal Massal</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Unggah bank format CSV</p>
                    </div>
                </a>
            </div>

            <!-- Sessions Card Presentation -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Sesi Ujian Terkait</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar jadwal ujian yang Anda ampu</p>
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama Sesi</th>
                                <th class="px-6 py-3 text-left">Ujian</th>
                                <th class="px-6 py-3 text-left">Tanggal</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($mySessions as $session)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $session->name }}</td>
                                <td class="px-6 py-3.5 text-slate-600">{{ $session->exam->name ?? '-' }}</td>
                                <td class="px-6 py-3.5 font-mono text-xs tabular-nums">{{ $session->start_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="px-2.5 py-0.5 text-xs font-bold rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        {{ $session->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right">
                                    <a href="{{ route('sessions.show', $session) }}" class="text-brand-700 hover:text-brand-900 font-semibold text-xs">Detail →</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-6 text-center text-slate-400 text-xs">Belum ada sesi ujian terkait.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card List View -->
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse($mySessions as $session)
                    <div class="p-4 space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm leading-snug">{{ $session->name }}</h3>
                                <p class="text-xs text-slate-600 mt-0.5">{{ $session->exam->name ?? '-' }}</p>
                            </div>
                            <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                                {{ $session->status }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs font-mono text-slate-500 tabular-nums">{{ $session->start_at->format('d/m/Y H:i') }}</span>
                            <a href="{{ route('sessions.show', $session) }}" class="min-h-[36px] px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-900 font-bold text-xs border border-brand-200 inline-flex items-center">
                                Detail Sesi →
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400 text-xs">Belum ada sesi ujian terkait.</div>
                    @endforelse
                </div>
            </div>
            @break

        {{-- ================= SISWA ================= --}}
        @case('siswa')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Assessment Entry Card -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-lg bg-gold-50 text-gold-700 border border-gold-200 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Siap Melaksanakan Ujian?</h2>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Masukkan kode token 6 karakter dari pengawas ruang/kelas untuk memulai mengerjakan sesi ujian yang sedang berlangsung.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('exam.token') }}" 
                        class="w-full sm:w-auto self-start min-h-[40px] px-5 py-2.5 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition flex items-center justify-center gap-2">
                        <span>Masukkan Token Ujian</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- LMS Learning Card -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-800 border border-brand-200 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Materi Pelajaran & Tugas (LMS)</h2>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Akses modul materi pelajaran, bahan bacaan, dan kumpulkan tugas kelas Anda secara terstruktur.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('lms.courses.index') }}" 
                        class="w-full sm:w-auto self-start min-h-[40px] px-5 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-300 font-bold text-xs transition flex items-center justify-center gap-2">
                        <span>Buka LMS Belajar</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <!-- Ujian Tersedia Hari Ini -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-2">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Ujian Tersedia</h2>
                        <p class="text-xs text-slate-500">Sesi ujian yang sedang terbuka untuk kelas Anda</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200">
                        {{ $availableSessions->count() }} Sesi Terbuka
                    </span>
                </div>
                @if($availableSessions->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Tidak ada ujian tersedia saat ini. Silakan periksa kembali saat jadwal sesi Anda dimulai.
                    </div>
                @else
                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3.5 text-left">Paket Ujian</th>
                                    <th class="px-6 py-3.5 text-left">Mata Pelajaran</th>
                                    <th class="px-6 py-3.5 text-left">Sesi & Ruang / Kelas</th>
                                    <th class="px-6 py-3.5 text-left">Waktu Pelaksanaan</th>
                                    <th class="px-6 py-3.5 text-left">Durasi</th>
                                    <th class="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($availableSessions as $session)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-3.5 font-bold text-slate-900">{{ $session->exam->name }}</td>
                                    <td class="px-6 py-3.5 text-slate-600">{{ $session->exam->subject->name ?? '-' }}</td>
                                    <td class="px-6 py-3.5 font-medium text-brand-800">{{ $session->name }}</td>
                                    <td class="px-6 py-3.5 font-mono text-xs tabular-nums">{{ $session->start_at->format('H:i') }} - {{ $session->end_at->format('H:i') }} WIB</td>
                                    <td class="px-6 py-3.5 text-slate-600">{{ $session->exam->duration_minutes }} menit</td>
                                    <td class="px-6 py-3.5 text-right">
                                        <a href="{{ route('exam.token') }}" class="px-3.5 py-1.5 rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs transition">
                                            Masukkan Token →
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card List View -->
                    <div class="block md:hidden divide-y divide-slate-100">
                        @foreach($availableSessions as $session)
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-brand-50 text-brand-800 border border-brand-200 mb-1">
                                        {{ $session->exam->subject->name ?? 'Mata Pelajaran' }}
                                    </span>
                                    <h3 class="font-bold text-slate-900 text-sm leading-snug">{{ $session->exam->name }}</h3>
                                    <p class="text-xs text-brand-800 font-medium mt-0.5">{{ $session->name }}</p>
                                </div>
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                                    {{ $session->exam->duration_minutes }} mnt
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-500 font-mono tabular-nums">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $session->start_at->format('H:i') }} - {{ $session->end_at->format('H:i') }} WIB
                                </span>
                            </div>
                            <a href="{{ route('exam.token') }}" class="w-full min-h-[40px] rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs transition flex items-center justify-center gap-2">
                                <span>Masukkan Token Ujian</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Riwayat Ujian Saya -->
            <div id="riwayat" class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Riwayat Ujian Saya</h2>
                    <p class="text-xs text-slate-500">Daftar evaluasi dan nilai ujian yang telah selesai</p>
                </div>
                @if($myAttempts->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">Belum ada riwayat pengerjaan ujian.</div>
                @else
                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3.5 text-left">Ujian</th>
                                    <th class="px-6 py-3.5 text-left">Waktu Selesai</th>
                                    <th class="px-6 py-3.5 text-left">Status</th>
                                    <th class="px-6 py-3.5 text-left">Nilai</th>
                                    <th class="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($myAttempts as $attempt)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $attempt->session->exam->name }}</td>
                                    <td class="px-6 py-3.5 font-mono text-xs text-slate-500 tabular-nums">{{ $attempt->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-md border
                                            @switch($attempt->status)
                                                @case('submitted') @case('auto_submitted') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                                @case('expired') bg-rose-50 text-rose-800 border-rose-200 @break
                                                @default bg-amber-50 text-amber-800 border-amber-200
                                            @endswitch
                                        ">
                                            {{ $attempt->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 font-mono font-bold text-slate-900 tabular-nums">
                                        @if($attempt->result)
                                            <span class="text-base text-brand-800">{{ $attempt->result->percentage }}%</span>
                                        @else
                                            <span class="text-slate-400 text-xs font-normal">Menunggu koreksi</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        @if($attempt->result)
                                            <a href="{{ route('exam.result', $attempt) }}" class="text-brand-700 hover:text-brand-900 font-semibold text-xs">Lihat Hasil →</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card List View -->
                    <div class="block md:hidden divide-y divide-slate-100">
                        @foreach($myAttempts as $attempt)
                        <div class="p-4 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm leading-snug">{{ $attempt->session->exam->name }}</h3>
                                    <p class="text-xs font-mono text-slate-500 mt-0.5 tabular-nums">{{ $attempt->started_at?->format('d/m/Y H:i') ?? '-' }}</p>
                                </div>
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-md border shrink-0
                                    @switch($attempt->status)
                                        @case('submitted') @case('auto_submitted') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                        @case('expired') bg-rose-50 text-rose-800 border-rose-200 @break
                                        @default bg-amber-50 text-amber-800 border-amber-200
                                    @endswitch
                                ">
                                    {{ $attempt->status }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <div class="font-mono font-bold text-slate-900 tabular-nums">
                                    @if($attempt->result)
                                        <span class="text-base text-brand-800">{{ $attempt->result->percentage }}%</span>
                                    @else
                                        <span class="text-slate-400 text-xs font-normal">Menunggu koreksi</span>
                                    @endif
                                </div>
                                @if($attempt->result)
                                    <a href="{{ route('exam.result', $attempt) }}" class="min-h-[36px] px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-brand-900 font-bold text-xs inline-flex items-center gap-1 transition">
                                        <span>Lihat Hasil</span>
                                        <span>→</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @break

        {{-- ================= PROKTOR ================= --}}
        @case('proktor')
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-3">
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Sesi Ujian Hari Ini</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar sesi ujian yang memerlukan pengawasan</p>
                    </div>
                    <a href="{{ route('monitoring.index') }}" class="min-h-[38px] px-4 py-2 rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Buka Monitoring Ujian
                    </a>
                </div>
                @if($todaySessions->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">Tidak ada sesi ujian dijadwalkan untuk hari ini.</div>
                @else
                    <!-- Desktop Table View -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3.5 text-left">Nama Ujian</th>
                                    <th class="px-6 py-3.5 text-left">Sesi</th>
                                    <th class="px-6 py-3.5 text-left">Ruang / Kelas</th>
                                    <th class="px-6 py-3.5 text-left">Waktu Sesi</th>
                                    <th class="px-6 py-3.5 text-left">Status</th>
                                    <th class="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($todaySessions as $session)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-3.5 font-bold text-slate-900">{{ $session->exam->name }}</td>
                                    <td class="px-6 py-3.5 text-slate-600">{{ $session->name }}</td>
                                    <td class="px-6 py-3.5 font-mono text-xs">{{ $session->room ?? 'Ruang Ujian' }}</td>
                                    <td class="px-6 py-3.5 font-mono text-xs tabular-nums">{{ $session->start_at->format('H:i') }} - {{ $session->end_at->format('H:i') }} WIB</td>
                                    <td class="px-6 py-3.5">
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-md border
                                            @switch($session->status)
                                                @case('in_progress') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                                @case('open') bg-amber-50 text-amber-800 border-amber-200 @break
                                                @case('completed') bg-slate-100 text-slate-800 border-slate-200 @break
                                                @default bg-rose-50 text-rose-800 border-rose-200
                                            @endswitch
                                        ">
                                            {{ $session->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-right">
                                        <a href="{{ route('monitoring.session', $session) }}" class="px-3 py-1.5 rounded-lg bg-gold-500 hover:bg-gold-600 text-white font-bold text-xs transition">
                                            Pantau Sesi →
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card List View -->
                    <div class="block md:hidden divide-y divide-slate-100">
                        @foreach($todaySessions as $session)
                        <div class="p-4 space-y-2.5">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm leading-snug">{{ $session->exam->name }}</h3>
                                    <p class="text-xs text-brand-800 font-medium mt-0.5">{{ $session->name }} • {{ $session->room ?? 'Ruang Ujian' }}</p>
                                </div>
                                <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-md border shrink-0
                                    @switch($session->status)
                                        @case('in_progress') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                        @case('open') bg-amber-50 text-amber-800 border-amber-200 @break
                                        @case('completed') bg-slate-100 text-slate-800 border-slate-200 @break
                                        @default bg-rose-50 text-rose-800 border-rose-200
                                    @endswitch
                                ">
                                    {{ $session->status }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <span class="text-xs font-mono text-slate-500 tabular-nums">{{ $session->start_at->format('H:i') }} - {{ $session->end_at->format('H:i') }} WIB</span>
                                <a href="{{ route('monitoring.session', $session) }}" class="min-h-[36px] px-3 py-1.5 rounded-lg bg-gold-500 hover:bg-gold-600 text-white font-bold text-xs inline-flex items-center gap-1">
                                    Pantau Sesi →
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @break

        {{-- ================= KEPALA SEKOLAH ================= --}}
        @case('kepala_sekolah')
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Ujian</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $totalExams }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $totalSessions }} Sesi Ujian</p>
                </div>
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Siswa Terdaftar</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $totalStudents }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $totalClasses }} Rombel</p>
                </div>
                <div class="bento-card p-4 sm:p-5">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Partisipasi</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-brand-800 font-mono tabular-nums mt-1.5">{{ $participants }}</p>
                    <p class="text-xs text-slate-500 mt-1">Aktivitas pengerjaan</p>
                </div>
                <div class="bg-emerald-50 p-4 sm:p-5 rounded-xl border border-emerald-200">
                    <span class="text-[11px] font-bold text-emerald-900 uppercase tracking-wider">Rata-rata Nilai</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-800 font-mono tabular-nums mt-1.5">{{ number_format((float) $avgPercentage, 1) }}%</p>
                    <p class="text-xs text-emerald-700 font-semibold mt-1">Seluruh ujian</p>
                </div>
            </div>

            <!-- Grade Distribution & Session Status Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 mb-3.5">Distribusi Nilai (Band)</h2>
                    <div class="space-y-2">
                        @foreach(['A' => '≥ 90 (Sangat Baik)', 'B' => '80-89 (Baik)', 'C' => '70-79 (Cukup)', 'D' => '60-69 (Kurang)', 'E' => '< 60 (Perlu Remedial)'] as $band => $label)
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-200/70">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-md font-bold text-xs flex items-center justify-center shrink-0
                                    @if($band === 'A') bg-emerald-100 text-emerald-800
                                    @elseif($band === 'B') bg-blue-100 text-blue-800
                                    @elseif($band === 'C') bg-amber-100 text-amber-800
                                    @else bg-rose-100 text-rose-800
                                    @endif
                                ">{{ $band }}</span>
                                <span class="text-xs font-medium text-slate-700">{{ $label }}</span>
                            </div>
                            <span class="text-xs font-bold text-slate-900 font-mono tabular-nums shrink-0">{{ $gradeDistribution[$band] ?? 0 }} siswa</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 mb-3.5">Status Sesi Ujian</h2>
                    <div class="space-y-2">
                        @foreach(['open' => 'Sesi Terbuka', 'in_progress' => 'Sedang Berlangsung', 'completed' => 'Tuntas / Selesai', 'scheduled' => 'Terjadwal', 'cancelled' => 'Dibatalkan'] as $status => $statusLabel)
                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 border border-slate-200/70">
                            <span class="text-xs font-medium text-slate-700">{{ $statusLabel }}</span>
                            <span class="text-xs font-bold text-slate-900 font-mono tabular-nums">{{ $sessionStatuses[$status] ?? 0 }} sesi</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($studentScoreStats->isNotEmpty())
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900">Statistik Nilai per Kelas</h2>
                </div>
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Nama Kelas / Rombel</th>
                                <th class="px-6 py-3.5 text-right">Rata-rata Nilai Capaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($studentScoreStats as $classId => $avg)
                            <tr>
                                <td class="px-6 py-3.5 font-semibold text-slate-900">{{ \App\Models\StudentClass::find($classId)?->name ?? 'Tanpa Kelas' }}</td>
                                <td class="px-6 py-3.5 font-mono font-bold text-brand-800 text-right tabular-nums">{{ $avg }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="block md:hidden divide-y divide-slate-100">
                    @foreach($studentScoreStats as $classId => $avg)
                    <div class="p-4 flex items-center justify-between">
                        <span class="font-semibold text-slate-900 text-sm">{{ \App\Models\StudentClass::find($classId)?->name ?? 'Tanpa Kelas' }}</span>
                        <span class="font-mono font-bold text-brand-800 text-base tabular-nums">{{ $avg }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @break

        {{-- ================= WALI KELAS ================= --}}
        @case('wali_kelas')
            @if($myClass)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                    <div class="bento-card p-4 sm:p-5">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kelas Binaan</span>
                        <p class="text-2xl sm:text-3xl font-extrabold text-brand-800 font-mono mt-1.5">{{ $myClass->name }}</p>
                        <p class="text-xs text-slate-500 mt-1">Tingkat {{ $myClass->grade }}</p>
                    </div>
                    <div class="bento-card p-4 sm:p-5">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Jumlah Siswa</span>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $classStudents->count() }}</p>
                        <p class="text-xs text-slate-500 mt-1">Siswa aktif di kelas</p>
                    </div>
                    <div class="bg-emerald-50 p-4 sm:p-5 rounded-xl border border-emerald-200">
                        <span class="text-[11px] font-bold text-emerald-900 uppercase tracking-wider">Rata-rata Nilai Kelas</span>
                        <p class="text-2xl sm:text-3xl font-extrabold text-emerald-800 font-mono tabular-nums mt-1.5">{{ $classAverage !== null ? $classAverage . '%' : '-' }}</p>
                        <p class="text-xs text-emerald-700 font-semibold mt-1">Dari {{ $classParticipants }} pengerjaan</p>
                    </div>
                </div>

                @if($classAttempts->filter(fn($a) => $a->result)->isNotEmpty())
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
                    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900">Statistik Ujian Kelas</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Nilai hasil ujian siswa kelas binaan</p>
                    </div>
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3.5 text-left">Nama Siswa</th>
                                    <th class="px-6 py-3.5 text-left">Ujian</th>
                                    <th class="px-6 py-3.5 text-left">Status</th>
                                    <th class="px-6 py-3.5 text-right">Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($classAttempts->filter(fn($a) => $a->result) as $attempt)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-6 py-3.5 font-semibold text-slate-900">{{ $attempt->user->name }}</td>
                                    <td class="px-6 py-3.5 text-slate-600">{{ $attempt->session->exam->name ?? '-' }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="px-2 py-0.5 text-xs font-bold rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            {{ $attempt->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-right font-mono font-bold text-brand-800 text-base tabular-nums">
                                        {{ $attempt->result->percentage }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="block md:hidden divide-y divide-slate-100">
                        @foreach($classAttempts->filter(fn($a) => $a->result) as $attempt)
                        <div class="p-4 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-sm">{{ $attempt->user->name }}</h3>
                                    <p class="text-xs text-slate-600 mt-0.5">{{ $attempt->session->exam->name ?? '-' }}</p>
                                </div>
                                <span class="font-mono font-bold text-brand-800 text-base tabular-nums shrink-0">
                                    {{ $attempt->result->percentage }}%
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif
            @break
    @endswitch
</x-layouts.app>
