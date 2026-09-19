<x-layouts.app :title="'Dashboard Utama'">
    <!-- Top Greeting Banner (Modern Mesh Radial Card) -->
    <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 mb-8 text-white shadow-xl border border-brand-800/80" style="background: radial-gradient(circle at 75% 20%, #12472e 0%, #082618 45%, #04140c 100%);">
        <!-- Subtle Glow Orbs -->
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-gold-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-brand-800/90 text-gold-300 border border-brand-700/80 mb-3 shadow-2xs backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Portal Terpadu CBT & LMS • T.A. 2026/2027</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-white leading-tight">
                    Selamat Datang, {{ auth()->user()->name }}!
                </h1>
                <p class="text-xs sm:text-sm text-brand-100/90 mt-2 max-w-2xl leading-relaxed">
                    Sistem Evaluasi Digital SMA Kartika III-1 Banyubiru. Kelola modul asesmen, monitoring peserta lab komputer, dan pantau rekapitulasi nilai akademik.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-3 bg-white/10 backdrop-blur-md px-4 py-3 rounded-2xl border border-white/15 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-gold-400/20 text-gold-300 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="text-right">
                    <div class="text-[10px] uppercase font-bold text-brand-200 tracking-wider">Tanggal Server</div>
                    <div class="text-sm font-mono font-bold text-gold-300">{{ now()->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    @switch(auth()->user()->role?->name)
        {{-- ================= SUPER ADMIN & ADMIN ================= --}}
        @case('super_admin')
        @case('admin')
            <!-- Bento Stat Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <div class="bento-card p-5 sm:p-6 hover:border-amber-400/80 group">
                    <div class="flex items-center justify-between text-slate-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Total Siswa</span>
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $totalStudents }}</p>
                    <p class="text-[11px] font-medium text-slate-500 mt-1.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Dari total {{ $totalUsers }} pengguna
                    </p>
                </div>

                <div class="bento-card p-5 sm:p-6 hover:border-blue-400/80 group">
                    <div class="flex items-center justify-between text-slate-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Guru & Pendidik</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $totalTeachers }}</p>
                    <p class="text-[11px] font-medium text-slate-500 mt-1.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        {{ $totalSubjects }} Mata Pelajaran
                    </p>
                </div>

                <div class="bento-card p-5 sm:p-6 hover:border-emerald-400/80 group">
                    <div class="flex items-center justify-between text-slate-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Bank Soal</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-mono tracking-tight">{{ $totalQuestions }}</p>
                    <p class="text-[11px] font-medium text-slate-500 mt-1.5 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Dalam {{ $totalExams }} paket ujian
                    </p>
                </div>

                <div class="bento-card p-5 sm:p-6 hover:border-brand-600/80 group">
                    <div class="flex items-center justify-between text-slate-500 mb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Sesi Aktif</span>
                        <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center group-hover:scale-105 transition-transform duration-150">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="text-3xl sm:text-4xl font-extrabold text-brand-800 font-mono tracking-tight">{{ $activeSessions }}</p>
                    <p class="text-[11px] font-bold text-emerald-600 mt-1.5 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        Lab Komputer Berjalan
                    </p>
                </div>
            </div>

            <!-- Quick Action Hub (Modern Cards with Glow) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <a href="{{ route('monitoring.index') }}" 
                    class="p-5 sm:p-6 bg-gradient-to-br from-brand-900 via-brand-950 to-black text-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-200 flex items-center gap-4 group border border-brand-800/80 hover:-translate-y-0.5">
                    <div class="w-12 h-12 rounded-2xl bg-brand-800 text-gold-400 flex items-center justify-center shrink-0 group-hover:scale-105 transition shadow-xs relative">
                        <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping absolute"></span>
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-base text-white">Monitoring Real-Time</h2>
                        <p class="text-xs text-brand-200 mt-0.5">Pantau status peserta ujian di laboratorium</p>
                    </div>
                </a>

                <a href="{{ route('import_export') }}" 
                    class="bento-card p-5 sm:p-6 text-slate-800 flex items-center gap-4 group hover:border-brand-600">
                    <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-800 flex items-center justify-center shrink-0 group-hover:scale-105 transition shadow-2xs">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-base text-slate-900 group-hover:text-brand-900 transition">Import / Export Data</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Sinkronisasi data siswa, guru, kelas & Dapodik</p>
                    </div>
                </a>

                <a href="{{ route('item-analysis.index') }}" 
                    class="bento-card p-5 sm:p-6 text-slate-800 flex items-center gap-4 group hover:border-gold-500">
                    <div class="w-12 h-12 rounded-2xl bg-gold-50 text-gold-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition shadow-2xs">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-base text-slate-900 group-hover:text-gold-900 transition">Analisis Butir Soal</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Tingkat kesukaran, daya beda & distribusi</p>
                    </div>
                </a>
            </div>

            <!-- Recent Sessions Table (Modern Card Presentation) -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden mb-8">
                <div class="p-6 border-b border-slate-100 flex flex-wrap justify-between items-center gap-3 bg-gradient-to-r from-slate-50/80 to-white">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Sesi Ujian Terbaru</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Jadwal pelaksanaan gelombang ujian terdaftar di laboratorium</p>
                    </div>
                    <a href="{{ route('sessions.index') }}" class="text-xs font-bold text-brand-800 hover:text-brand-950 flex items-center gap-1 group">
                        <span>Lihat Semua Sesi</span>
                        <span class="group-hover:translate-x-0.5 transition-transform duration-150">→</span>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/70 text-slate-600 text-xs uppercase tracking-wider font-bold">
                            <tr>
                                <th class="px-6 py-4 text-left">Nama Sesi</th>
                                <th class="px-6 py-4 text-left">Paket Ujian</th>
                                <th class="px-6 py-4 text-left">Jadwal Mulai</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($recentSessions as $session)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $session->name }}</td>
                                <td class="px-6 py-4 text-slate-600 font-medium">{{ $session->exam->name ?? '-' }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ $session->start_at->format('d/m/Y H:i') }} WIB</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-full border
                                        @switch($session->status)
                                            @case('open') @case('in_progress') bg-emerald-100 text-emerald-800 border-emerald-200 @break
                                            @case('completed') bg-slate-100 text-slate-800 border-slate-200 @break
                                            @case('cancelled') bg-rose-100 text-rose-800 border-rose-200 @break
                                            @default bg-amber-100 text-amber-800 border-amber-200
                                        @endswitch
                                    ">
                                        {{ $session->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('sessions.show', $session) }}" class="px-3.5 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-900 font-bold text-xs border border-brand-200/80 transition-all duration-150">
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
            </div>
            @break

        {{-- ================= GURU ================= --}}
        @case('guru')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Bank Soal Saya</span>
                    <p class="text-3xl font-extrabold text-slate-900 font-mono mt-2">{{ $myQuestions }}</p>
                    <a href="{{ route('questions.index') }}" class="text-xs text-brand-700 font-semibold mt-1 inline-block">Lihat Bank Soal →</a>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Paket Ujian Saya</span>
                    <p class="text-3xl font-extrabold text-slate-900 font-mono mt-2">{{ $myExams }}</p>
                    <a href="{{ route('exams.index') }}" class="text-xs text-brand-700 font-semibold mt-1 inline-block">Kelola Ujian →</a>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sesi Ujian Saya</span>
                    <p class="text-3xl font-extrabold text-slate-900 font-mono mt-2">{{ $mySessions->count() }}</p>
                    <span class="text-xs text-slate-400 mt-1 block">Pelaksanaan terjadwal</span>
                </div>
                <div class="bg-amber-50/80 p-5 rounded-2xl border border-amber-200 shadow-xs">
                    <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">Antrean Koreksi Esai</span>
                    <p class="text-3xl font-extrabold text-amber-900 font-mono mt-2">
                        <a href="{{ route('grading.index') }}" class="hover:underline">{{ $pendingGrading }}</a>
                    </p>
                    <a href="{{ route('grading.index') }}" class="text-xs text-amber-800 font-bold mt-1 inline-block">Koreksi Sekarang →</a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <a href="{{ route('questions.create') }}" class="p-5 bg-brand-800 hover:bg-brand-900 text-white rounded-2xl shadow-xs transition flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-brand-700 text-white flex items-center justify-center shrink-0 font-bold text-lg">+</div>
                    <div>
                        <h2 class="font-bold text-sm text-white">Buat Soal Baru</h2>
                        <p class="text-xs text-brand-200 mt-0.5">PG, Menjodohkan, Esai</p>
                    </div>
                </a>
                <a href="{{ route('lms.courses.index') }}" class="p-5 bg-gold-500 hover:bg-gold-600 text-brand-950 rounded-2xl shadow-xs transition flex items-center gap-4 border border-gold-400">
                    <div class="w-10 h-10 rounded-xl bg-brand-950 text-gold-400 flex items-center justify-center shrink-0 font-bold">📚</div>
                    <div>
                        <h2 class="font-bold text-sm text-brand-950">LMS Kelas & Materi</h2>
                        <p class="text-xs text-brand-900/80 mt-0.5">Unggah modul & buat tugas</p>
                    </div>
                </a>
                <a href="{{ route('item-analysis.index') }}" class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-brand-600 shadow-xs transition flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-gold-50 text-gold-700 flex items-center justify-center shrink-0 font-bold">📊</div>
                    <div>
                        <h2 class="font-bold text-sm text-slate-900">Analisis Butir Soal</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daya beda & kesukaran</p>
                    </div>
                </a>
                <a href="{{ route('import_export') }}" class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-brand-600 shadow-xs transition flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
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

            <!-- Sessions Table -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                    <h2 class="text-base font-bold text-slate-900">Sesi Ujian Terkait</h2>
                </div>
                <div class="overflow-x-auto">
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
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $session->name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $session->exam->name ?? '-' }}</td>
                                <td class="px-6 py-4 font-mono text-xs">{{ $session->start_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        {{ $session->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('sessions.show', $session) }}" class="text-brand-700 hover:text-brand-900 font-semibold text-xs">Detail →</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-6 text-center text-slate-400 text-xs">Belum ada sesi ujian terkait.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @break

        {{-- ================= SISWA ================= --}}
        @case('siswa')
            <!-- Student Action Hero (Vibrant Gradient Bento) -->
            <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 mb-8 shadow-xl text-brand-950 border border-gold-400/80" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);">
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/15 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black bg-brand-950/15 text-brand-950 border border-brand-950/20 mb-2.5 backdrop-blur-xs">
                            <span class="w-2 h-2 rounded-full bg-brand-950 animate-pulse"></span>
                            <span>Pintu Masuk Ruang Ujian</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-brand-950">Siap Melaksanakan Ujian?</h2>
                        <p class="text-xs sm:text-sm font-semibold text-brand-950/80 mt-1 max-w-xl leading-relaxed">
                            Pastikan Anda telah menerima kode Token Ujian dari proktor ruang sebelum menekan tombol di bawah.
                        </p>
                    </div>
                    <a href="{{ route('exam.token') }}" 
                        class="px-8 py-3.5 rounded-2xl bg-brand-950 hover:bg-black text-white font-black text-sm tracking-wide transition-all duration-200 shadow-xl btn-glow-brand shrink-0 flex items-center gap-2.5 transform hover:scale-[1.02]">
                        <svg class="w-5 h-5 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        <span>Masukkan Token Ujian</span>
                    </a>
                </div>
            </div>

            <!-- LMS Belajar Hub for Siswa -->
            <div class="relative overflow-hidden rounded-3xl p-6 sm:p-7 mb-8 shadow-md border border-brand-800/80 flex flex-col sm:flex-row items-center justify-between gap-6" style="background: radial-gradient(circle at 80% 20%, #12472e 0%, #061d13 70%);">
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-gold-400 to-amber-500 text-brand-950 flex items-center justify-center shrink-0 font-black shadow-md shadow-amber-500/20">
                        <svg class="w-7 h-7 text-brand-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-black bg-brand-900/90 text-gold-300 border border-brand-700 mb-1">
                            LMS Pembelajaran Mandiri
                        </div>
                        <h3 class="text-lg font-black text-white">Materi Pelajaran & Tugas Mandiri</h3>
                        <p class="text-xs text-brand-100/80 mt-0.5 font-medium leading-relaxed">Akses modul materi pelajaran, slide presentasi, dan kumpulkan tugas kelas Anda secara tertib.</p>
                    </div>
                </div>
                <a href="{{ route('lms.courses.index') }}" 
                    class="px-6 py-3 rounded-2xl bg-gold-400 hover:bg-gold-500 text-brand-950 font-black text-xs shadow-lg transition-all duration-200 shrink-0 flex items-center gap-2 border border-gold-300 relative z-10 btn-glow-gold">
                    <span>Buka LMS Belajar</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <!-- Ujian Tersedia Hari Ini -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-8">
                <div class="p-5 border-b border-slate-200 bg-slate-50/70 flex justify-between items-center">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Ujian Tersedia</h2>
                        <p class="text-xs text-slate-500">Sesi ujian yang sedang terbuka untuk kelas Anda</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                        {{ $availableSessions->count() }} Sesi Terbuka
                    </span>
                </div>
                @if($availableSessions->isEmpty())
                    <div class="p-10 text-center text-slate-400 text-sm">
                        Tidak ada ujian tersedia saat ini. Silakan periksa kembali saat jadwal sesi Anda dimulai.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3.5 text-left">Paket Ujian</th>
                                    <th class="px-6 py-3.5 text-left">Mata Pelajaran</th>
                                    <th class="px-6 py-3.5 text-left">Sesi & Ruang</th>
                                    <th class="px-6 py-3.5 text-left">Waktu Pelaksanaan</th>
                                    <th class="px-6 py-3.5 text-left">Durasi</th>
                                    <th class="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($availableSessions as $session)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $session->exam->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $session->exam->subject->name ?? '-' }}</td>
                                    <td class="px-6 py-4 font-medium text-brand-800">{{ $session->name }}</td>
                                    <td class="px-6 py-4 font-mono text-xs">{{ $session->start_at->format('H:i') }} - {{ $session->end_at->format('H:i') }} WIB</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $session->exam->duration_minutes }} menit</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('exam.token') }}" class="px-3.5 py-1.5 rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs transition">
                                            Masukkan Token →
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Riwayat Ujian Saya -->
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                    <h2 class="text-base font-bold text-slate-900">Riwayat Ujian Saya</h2>
                    <p class="text-xs text-slate-500">Daftar evaluasi dan nilai ujian yang telah selesai</p>
                </div>
                @if($myAttempts->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">Belum ada riwayat pengerjaan ujian.</div>
                @else
                    <div class="overflow-x-auto">
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
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $attempt->session->exam->name }}</td>
                                    <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $attempt->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full
                                            @switch($attempt->status)
                                                @case('submitted') @case('auto_submitted') bg-emerald-100 text-emerald-800 border border-emerald-200 @break
                                                @case('expired') bg-rose-100 text-rose-800 border border-rose-200 @break
                                                @default bg-amber-100 text-amber-800 border border-amber-200
                                            @endswitch
                                        ">
                                            {{ $attempt->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-mono font-bold text-slate-900">
                                        @if($attempt->result)
                                            <span class="text-base text-brand-800">{{ $attempt->result->percentage }}%</span>
                                        @else
                                            <span class="text-slate-400 text-xs font-normal">Menunggu koreksi</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($attempt->result)
                                            <a href="{{ route('exam.result', $attempt) }}" class="text-brand-700 hover:text-brand-900 font-semibold text-xs">Lihat Hasil →</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @break

        {{-- ================= PROKTOR ================= --}}
        @case('proktor')
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-8">
                <div class="p-5 border-b border-slate-200 bg-slate-50/70 flex justify-between items-center">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Sesi Ujian Hari Ini</h2>
                        <p class="text-xs text-slate-500">Daftar sesi laboratorium komputer yang memerlukan pengawasan</p>
                    </div>
                    <a href="{{ route('monitoring.index') }}" class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs flex items-center gap-1.5 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Buka Monitoring Real-Time
                    </a>
                </div>
                @if($todaySessions->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">Tidak ada sesi ujian dijadwalkan untuk hari ini.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3.5 text-left">Nama Ujian</th>
                                    <th class="px-6 py-3.5 text-left">Sesi</th>
                                    <th class="px-6 py-3.5 text-left">Ruang Lab</th>
                                    <th class="px-6 py-3.5 text-left">Waktu Sesi</th>
                                    <th class="px-6 py-3.5 text-left">Status</th>
                                    <th class="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($todaySessions as $session)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-6 py-4 font-bold text-slate-900">{{ $session->exam->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $session->name }}</td>
                                    <td class="px-6 py-4 font-mono text-xs">{{ $session->room ?? 'Lab Komputer' }}</td>
                                    <td class="px-6 py-4 font-mono text-xs">{{ $session->start_at->format('H:i') }} - {{ $session->end_at->format('H:i') }} WIB</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border
                                            @switch($session->status)
                                                @case('in_progress') bg-emerald-100 text-emerald-800 border-emerald-200 @break
                                                @case('open') bg-amber-100 text-amber-800 border-amber-200 @break
                                                @case('completed') bg-slate-100 text-slate-800 border-slate-200 @break
                                                @default bg-rose-100 text-rose-800 border-rose-200
                                            @endswitch
                                        ">
                                            {{ $session->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('monitoring.session', $session) }}" class="px-3 py-1.5 rounded-lg bg-gold-500 hover:bg-gold-600 text-white font-bold text-xs shadow-xs transition">
                                            Pantau Sesi →
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            @break

        {{-- ================= KEPALA SEKOLAH ================= --}}
        @case('kepala_sekolah')
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Ujian</span>
                    <p class="text-3xl font-extrabold text-slate-900 font-mono mt-2">{{ $totalExams }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $totalSessions }} Sesi Ujian</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Siswa Terdaftar</span>
                    <p class="text-3xl font-extrabold text-slate-900 font-mono mt-2">{{ $totalStudents }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $totalClasses }} Kelas / Rombel</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Partisipasi</span>
                    <p class="text-3xl font-extrabold text-brand-800 font-mono mt-2">{{ $participants }}</p>
                    <p class="text-xs text-slate-400 mt-1">Aktivitas pengerjaan</p>
                </div>
                <div class="bg-emerald-50/80 p-5 rounded-2xl border border-emerald-200 shadow-xs">
                    <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Rata-rata Nilai</span>
                    <p class="text-3xl font-extrabold text-emerald-800 font-mono mt-2">{{ number_format((float) $avgPercentage, 1) }}%</p>
                    <p class="text-xs text-emerald-700 font-semibold mt-1">Kalkulasi seluruh ujian</p>
                </div>
            </div>

            <!-- Grade Distribution & Session Status Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                    <h2 class="text-base font-bold text-slate-900 mb-4 tracking-tight">Distribusi Nilai (Band)</h2>
                    <div class="space-y-3">
                        @foreach(['A' => '≥ 90 (Sangat Baik)', 'B' => '80-89 (Baik)', 'C' => '70-79 (Cukup)', 'D' => '60-69 (Kurang)', 'E' => '< 60 (Perlu Remedial)'] as $band => $label)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-md font-bold text-xs flex items-center justify-center
                                    @if($band === 'A') bg-emerald-100 text-emerald-800
                                    @elseif($band === 'B') bg-blue-100 text-blue-800
                                    @elseif($band === 'C') bg-amber-100 text-amber-800
                                    @else bg-rose-100 text-rose-800
                                    @endif
                                ">{{ $band }}</span>
                                <span class="text-xs font-medium text-slate-700">Nilai {{ $band }} ({{ $label }})</span>
                            </div>
                            <span class="text-xs font-bold text-slate-900 font-mono">{{ $gradeDistribution[$band] ?? 0 }} siswa</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                    <h2 class="text-base font-bold text-slate-900 mb-4 tracking-tight">Status Sesi Ujian</h2>
                    <div class="space-y-3">
                        @foreach(['open' => 'Sesi Terbuka', 'in_progress' => 'Sedang Berlangsung', 'completed' => 'Tuntas / Selesai', 'scheduled' => 'Terjadwal', 'cancelled' => 'Dibatalkan'] as $status => $statusLabel)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-xs font-medium text-slate-700">{{ $statusLabel }}</span>
                            <span class="text-xs font-bold text-slate-900 font-mono">{{ $sessionStatuses[$status] ?? 0 }} sesi</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($studentScoreStats->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-8">
                <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                    <h2 class="text-base font-bold text-slate-900">Statistik Nilai per Kelas</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Nama Kelas / Rombel</th>
                                <th class="px-6 py-3.5 text-left">Rata-rata Nilai Capaian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($studentScoreStats as $classId => $avg)
                            <tr>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ \App\Models\StudentClass::find($classId)?->name ?? 'Tanpa Kelas' }}</td>
                                <td class="px-6 py-4 font-mono font-bold text-brand-800">{{ $avg }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            @break

        {{-- ================= WALI KELAS ================= --}}
        @case('wali_kelas')
            @if($myClass)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-8">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas Binaan</span>
                        <p class="text-3xl font-extrabold text-brand-800 font-mono mt-2">{{ $myClass->name }}</p>
                        <p class="text-xs text-slate-400 mt-1">Tingkat {{ $myClass->grade }}</p>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jumlah Siswa</span>
                        <p class="text-3xl font-extrabold text-slate-900 font-mono mt-2">{{ $classStudents->count() }}</p>
                        <p class="text-xs text-slate-400 mt-1">Siswa aktif di kelas</p>
                    </div>
                    <div class="bg-emerald-50/80 p-5 rounded-2xl border border-emerald-200 shadow-xs">
                        <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Rata-rata Nilai Kelas</span>
                        <p class="text-3xl font-extrabold text-emerald-800 font-mono mt-2">{{ $classAverage !== null ? $classAverage . '%' : '-' }}</p>
                        <p class="text-xs text-emerald-700 font-semibold mt-1">Dari {{ $classParticipants }} pengerjaan</p>
                    </div>
                </div>

                @if($classAttempts->filter(fn($a) => $a->result)->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-8">
                    <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                        <h2 class="text-base font-bold text-slate-900">Statistik Ujian Kelas</h2>
                        <p class="text-xs text-slate-500">Nilai hasil ujian siswa kelas binaan</p>
                    </div>
                    <div class="overflow-x-auto">
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
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-6 py-4 font-semibold text-slate-900">{{ $attempt->user->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $attempt->session->exam->name ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">
                                            {{ $attempt->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono font-bold text-brand-800 text-base">
                                        {{ $attempt->result->percentage }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endif
            @break
    @endswitch
</x-layouts.app>