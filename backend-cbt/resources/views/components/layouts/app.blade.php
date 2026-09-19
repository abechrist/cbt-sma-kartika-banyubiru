@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f5f7f4]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#061d13">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="CBT Kartika">
    <title>{{ !empty($title) ? $title . ' - ' : '' }}{{ config('app.name', 'CBT SMA Kartika III-1 Banyubiru') }}</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="icon" type="image/png" href="{{ asset('images/pwa-icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans text-slate-900 antialiased selection:bg-gold-400 selection:text-brand-950">
    <!-- Top Institutional Utility Bar -->
    <div class="bg-brand-950 text-white text-[11px] py-2 px-4 border-b border-white/10 no-print">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 text-brand-300 font-medium">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    LAN Lab Komputer Aktif
                </span>
                <span class="text-brand-700 hidden sm:inline">/</span>
                <span class="text-brand-300/80 hidden sm:inline">Yayasan Kartika Jaya Cabang III · Banyubiru</span>
            </div>
            <div class="flex items-center gap-3 text-brand-200">
                <span class="bg-white/10 px-2.5 py-1 rounded-full text-[10px] text-brand-200 font-semibold tracking-wide">T.A. 2026/2027</span>
                <span id="systemClock" class="font-mono text-brand-300 tracking-wider"></span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header (Modern Glassmorphic Shell) -->
    <header class="glass-header border-b border-slate-200/70 sticky top-0 z-30 no-print transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-[4.5rem]">
                <!-- Brand & Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-900 to-brand-950 p-1 flex items-center justify-center shrink-0 shadow-xs group-hover:scale-105 group-hover:shadow-md transition-all duration-200">
                            <img src="{{ asset('images/logo-kartika.png') }}" alt="Logo SMA Kartika III-1 Banyubiru" width="32" height="32" class="w-8 h-8 object-contain">
                        </div>
                        <div>
                            <div class="text-sm sm:text-base font-black text-brand-950 tracking-[-0.02em] leading-none group-hover:text-brand-800 transition">
                                CBT SMA KARTIKA III-1
                            </div>
                            <div class="text-[10px] sm:text-[11px] font-bold text-gold-700 tracking-wider uppercase mt-0.5">
                                Banyubiru • Sistem Ujian Digital
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Desktop Role Navigation (Categorized & Refined) -->
                @auth
                @php $role = auth()->user()->role?->name; @endphp
                <nav class="hidden xl:flex items-center gap-1">
                    <!-- Dashboard Link -->
                    <a href="{{ route('dashboard') }}" 
                        class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 text-xs font-semibold {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-950 border border-brand-200 shadow-2xs' : 'text-slate-600 hover:text-brand-950 hover:bg-slate-100/80' }}">
                        <svg class="w-4 h-4 text-brand-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>

                    <!-- LMS Belajar Link -->
                    <a href="{{ route('lms.courses.index') }}" 
                        class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 text-xs font-semibold {{ request()->routeIs('lms.*') ? 'bg-brand-50 text-brand-950 border border-brand-200 shadow-2xs' : 'text-slate-600 hover:text-brand-950 hover:bg-slate-100/80' }}">
                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        LMS Belajar
                    </a>

                    @if(in_array($role, ['super_admin', 'admin', 'guru']))
                    <!-- Ujian & Asesmen Dropdown -->
                    <div class="relative group">
                        <button type="button" aria-haspopup="true"
                            class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-700/50 {{ request()->routeIs(['questions.*', 'exams.*', 'sessions.*', 'grading.*', 'item-analysis.*', 'rpps.*']) ? 'bg-brand-50 text-brand-950 border border-brand-200' : 'text-slate-600 hover:text-brand-950 hover:bg-slate-100/80' }}">
                            <svg class="w-4 h-4 text-brand-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Asesmen & Ujian</span>
                            <svg class="w-3 h-3 text-slate-400 group-hover:rotate-180 transition duration-150" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute left-0 mt-1 w-56 bg-white/95 backdrop-blur-xl border border-slate-200/90 rounded-2xl shadow-xl py-2 hidden group-hover:block group-focus-within:block z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                            <div class="px-3.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Kurikulum & Bank Soal</div>
                            <a href="{{ route('rpps.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-950 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                Modul Ajar & RPP
                            </a>
                            <a href="{{ route('questions.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-950 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                                Bank Soal Ujian
                            </a>
                            <a href="{{ route('exams.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-950 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                                Paket Ujian Terjadwal
                            </a>
                            @if(in_array($role, ['super_admin', 'admin']))
                            <a href="{{ route('sessions.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-950 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                                Sesi & Gelombang Lab
                            </a>
                            @endif
                            <div class="border-t border-slate-100 my-1"></div>
                            <div class="px-3.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Evaluasi & Koreksi</div>
                            <a href="{{ route('grading.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-950 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Koreksi Jawaban Esai
                            </a>
                            <a href="{{ route('item-analysis.index') }}" class="flex items-center gap-2 px-3.5 py-2 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-950 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Analisis Butir Soal (Psikometri)
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Monitoring Link for Proctor / Admin -->
                    @if(in_array($role, ['super_admin', 'admin', 'proktor']))
                    <a href="{{ route('monitoring.index') }}" 
                        class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 text-xs font-semibold {{ request()->routeIs('monitoring.*') ? 'bg-emerald-50 text-emerald-950 border border-emerald-300 shadow-xs' : 'text-emerald-900 hover:text-emerald-950 hover:bg-emerald-50/70' }}">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Monitoring Lab
                    </a>
                    @endif

                    @if(in_array($role, ['super_admin', 'admin', 'guru', 'kepala_sekolah', 'wali_kelas']))
                    <a href="{{ route('results.index') }}" 
                        class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 text-xs font-semibold {{ request()->routeIs('results.*') ? 'bg-brand-50 text-brand-950 border border-brand-200' : 'text-slate-600 hover:text-brand-950 hover:bg-slate-100/80' }}">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Hasil & Nilai
                    </a>
                    @endif

                    @if($role === 'siswa')
                    <a href="{{ route('exam.token') }}" 
                        class="px-4 py-1.5 rounded-xl bg-gold-400 hover:bg-gold-500 text-brand-950 font-bold text-xs transition flex items-center gap-1.5 btn-glow-gold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Mulai Ujian
                    </a>
                    @endif

                    @if(in_array($role, ['super_admin', 'admin']))
                    <div class="relative group">
                        <button type="button" aria-haspopup="true"
                            class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-brand-700/50 text-slate-600 hover:text-brand-950 hover:bg-slate-100/80">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span>Master Data</span>
                            <svg class="w-3 h-3 text-slate-400 group-hover:rotate-180 transition duration-150" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute right-0 mt-1 w-52 bg-white/95 backdrop-blur-xl border border-slate-200/90 rounded-2xl shadow-xl py-2 hidden group-hover:block group-focus-within:block z-50 animate-in fade-in slide-in-from-top-1 duration-150">
                            <div class="px-3.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Akademik & Rombel</div>
                            <a href="{{ route('users.index') }}" class="block px-3.5 py-2 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-950 font-medium">Manajemen Pengguna</a>
                            <a href="{{ route('classes.index') }}" class="block px-3.5 py-2 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-950 font-medium">Kelas & Rombel</a>
                            <a href="{{ route('subjects.index') }}" class="block px-3.5 py-2 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-950 font-medium">Mata Pelajaran</a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <a href="{{ route('import_export') }}" class="block px-3.5 py-2 text-xs font-semibold text-brand-800 hover:bg-brand-50">Sinkronisasi Dapodik</a>
                        </div>
                    </div>
                    @elseif(in_array($role, ['guru']))
                    <a href="{{ route('import_export') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-brand-950 hover:bg-slate-100/80 transition">
                        Import Soal
                    </a>
                    @endif
                </nav>
                @endauth

                <!-- User Profile & Action Bar -->
                <div class="flex items-center gap-3">
                    @auth
                    <!-- Profile Card Pill -->
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-200/80">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-800 to-brand-950 text-gold-300 font-bold flex items-center justify-center text-xs shadow-xs ring-2 ring-brand-700/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <div class="text-xs font-bold text-slate-900 leading-tight truncate max-w-[140px]">
                                {{ auth()->user()->name }}
                            </div>
                            <div class="mt-0.5">
                                @php
                                    $role = auth()->user()->role?->name;
                                    $badgeStyle = match($role) {
                                        'super_admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'admin' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'guru' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'siswa' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'proktor' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'kepala_sekolah' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        'wali_kelas' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                    $roleDisplay = match($role) {
                                        'super_admin' => 'Super Admin',
                                        'admin' => 'Operator Ujian',
                                        'guru' => 'Guru Pengampu',
                                        'siswa' => 'Peserta Ujian',
                                        'proktor' => 'Proktor Ruang',
                                        'kepala_sekolah' => 'Kepala Sekolah',
                                        'wali_kelas' => 'Wali Kelas',
                                        default => ucfirst(str_replace('_', ' ', $role ?? 'Pengguna')),
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeStyle }}">
                                    {{ $roleDisplay }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Keluar dari sistem" aria-label="Keluar dari sistem"
                            class="p-2 text-rose-900/70 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition-all duration-150 border border-transparent hover:border-rose-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>

                    <!-- Mobile Drawer Toggle -->
                    <button type="button" id="mobileMenuBtn" aria-label="Buka navigasi seluler" class="xl:hidden p-2 text-slate-600 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition shadow-xs btn-glow-brand">
                        Masuk Sistem
                    </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        @auth
        <div id="mobileMenu" class="hidden xl:hidden border-t border-slate-200 bg-white/95 backdrop-blur-xl px-4 pt-3 pb-5 space-y-1.5 animate-in slide-in-from-top-2 duration-150">
            <div class="py-2.5 px-3 rounded-xl bg-slate-50 border border-slate-200/80 mb-3 flex items-center justify-between">
                <div>
                    <div class="font-bold text-xs text-slate-900">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-slate-500 font-mono">{{ auth()->user()->email ?? auth()->user()->nisn }}</div>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border {{ $badgeStyle }}">{{ $roleDisplay }}</span>
            </div>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-brand-50 hover:text-brand-950">
                <span>Dashboard Utama</span>
            </a>
            <a href="{{ route('lms.courses.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-gold-950 bg-gold-50 border border-gold-300">
                <span>LMS Belajar (Materi & Tugas)</span>
            </a>
            @if(in_array($role, ['super_admin', 'admin', 'guru']))
            <a href="{{ route('rpps.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold text-brand-900 bg-brand-50/70 border border-brand-200">Modul Ajar & RPP</a>
            <a href="{{ route('questions.index') }}" class="block px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-brand-50">Bank Soal</a>
            <a href="{{ route('exams.index') }}" class="block px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-brand-50">Paket Ujian</a>
            @endif
            @if(in_array($role, ['super_admin', 'admin']))
            <a href="{{ route('sessions.index') }}" class="block px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-brand-50">Sesi Ujian</a>
            @endif
            @if(in_array($role, ['super_admin', 'admin', 'proktor']))
            <a href="{{ route('monitoring.index') }}" class="block px-3 py-2 rounded-xl text-xs font-semibold text-emerald-800 bg-emerald-50">Monitoring Real-time</a>
            @endif
            @if(in_array($role, ['super_admin', 'admin', 'guru']))
            <a href="{{ route('grading.index') }}" class="block px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-brand-50">Koreksi Esai</a>
            <a href="{{ route('item-analysis.index') }}" class="block px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-brand-50">Analisis Butir Soal</a>
            @endif
            @if(in_array($role, ['super_admin', 'admin', 'guru', 'kepala_sekolah', 'wali_kelas']))
            <a href="{{ route('results.index') }}" class="block px-3 py-2 rounded-xl text-xs font-medium text-slate-700 hover:bg-brand-50">Hasil & Rekap Nilai</a>
            @endif
            @if($role === 'siswa')
            <a href="{{ route('exam.token') }}" class="block px-3 py-2 rounded-xl text-xs font-bold bg-gold-400 text-brand-950">Mulai Ujian (Masukkan Token)</a>
            @endif
            @if(in_array($role, ['super_admin', 'admin']))
            <div class="border-t border-slate-100 pt-2 mt-2">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-1">Master Data</div>
                <a href="{{ route('users.index') }}" class="block px-3 py-1.5 rounded-lg text-xs text-slate-600 hover:bg-brand-50">Pengguna</a>
                <a href="{{ route('classes.index') }}" class="block px-3 py-1.5 rounded-lg text-xs text-slate-600 hover:bg-brand-50">Kelas & Rombel</a>
                <a href="{{ route('subjects.index') }}" class="block px-3 py-1.5 rounded-lg text-xs text-slate-600 hover:bg-brand-50">Mata Pelajaran</a>
                <a href="{{ route('import_export') }}" class="block px-3 py-1.5 rounded-lg text-xs font-semibold text-brand-800 hover:bg-brand-50">Import / Export Dapodik</a>
            </div>
            @endif
        </div>
        @endauth
    </header>

    <!-- Main Body Content Area -->
    <main class="page-enter flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <!-- Toast & Feedback Notifications -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-emerald-50/90 border border-emerald-200/80 text-emerald-950 rounded-2xl flex items-start gap-3.5 shadow-sm backdrop-blur-md animate-in fade-in slide-in-from-top-2 duration-200">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="pt-1 text-sm font-semibold">{{ session('success') }}</div>
            </div>
        @endif
        
        @if (session('warning'))
            <div class="mb-6 p-4 bg-amber-50/90 border border-amber-200/80 text-amber-950 rounded-2xl flex items-start gap-3.5 shadow-sm backdrop-blur-md animate-in fade-in slide-in-from-top-2 duration-200">
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="pt-1 text-sm font-semibold">{{ session('warning') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-rose-50/90 border border-rose-200/80 text-rose-950 rounded-2xl flex items-start gap-3.5 shadow-sm backdrop-blur-md animate-in fade-in slide-in-from-top-2 duration-200">
                <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="pt-1 text-sm font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-5 bg-rose-50/95 border border-rose-200/90 text-rose-950 rounded-2xl shadow-sm backdrop-blur-md">
                <div class="flex items-center gap-2.5 font-bold text-sm mb-2 text-rose-900">
                    <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span>Periksa kembali data yang dimasukkan:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs sm:text-sm text-rose-800 pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- PWA Install Banner for Mobile -->
    <div id="pwaInstallBanner" class="hidden fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md z-50 bg-brand-950 text-white p-4 rounded-2xl shadow-2xl border border-brand-700/80 flex items-center justify-between gap-4 transition-all duration-300">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/pwa-icon-192.png') }}" alt="Logo App" class="w-10 h-10 rounded-xl bg-white p-1 shrink-0 shadow-xs">
            <div>
                <h4 class="text-xs font-bold text-white leading-tight">Pasang CBT & LMS Kartika</h4>
                <p class="text-[11px] text-brand-200 mt-0.5">Akses cepat & layar penuh tanpa address bar browser.</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button id="pwaInstallBtn" type="button" class="px-3.5 py-1.5 rounded-xl bg-gold-400 hover:bg-gold-500 text-brand-950 font-black text-xs transition shadow-xs">
                Pasang
            </button>
            <button id="pwaDismissBtn" type="button" class="p-1 rounded-lg text-brand-400 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <!-- Institutional Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2">
                <span class="font-semibold text-brand-950">SMA KARTIKA III-1 BANYUBIRU</span>
                <span>•</span>
                <span>Yayasan Kartika Jaya Cabang III</span>
            </div>
            <div class="text-center md:text-right text-slate-400">
                Sistem Terpadu CBT & LMS v2.6 Pro &bull; Hak Cipta &copy; {{ date('Y') }} SMA Kartika III-1 Banyubiru.
            </div>
        </div>
    </footer>

    <!-- Simple Vanilla JS for Clock, Mobile Drawer & PWA -->
    <script>
        // Live Header Clock
        function updateClock() {
            const clockEl = document.getElementById('systemClock');
            if (!clockEl) return;
            const now = new Date();
            clockEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Mobile Menu Toggle
        const menuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('PWA Service Worker registered:', reg.scope))
                    .catch((err) => console.error('PWA Service Worker registration failed:', err));
            });
        }

        // PWA Install Prompt Logic
        let deferredPrompt;
        const installBanner = document.getElementById('pwaInstallBanner');
        const installBtn = document.getElementById('pwaInstallBtn');
        const dismissBtn = document.getElementById('pwaDismissBtn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            // Tampilkan banner hanya jika belum di-dismiss oleh user sebelumnya
            if (!localStorage.getItem('pwa_dismissed')) {
                installBanner?.classList.remove('hidden');
            }
        });

        installBtn?.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log(`User response to install prompt: ${outcome}`);
            deferredPrompt = null;
            installBanner?.classList.add('hidden');
        });

        dismissBtn?.addEventListener('click', () => {
            installBanner?.classList.add('hidden');
            localStorage.setItem('pwa_dismissed', '1');
        });
    </script>
</body>
</html>