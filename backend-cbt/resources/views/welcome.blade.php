<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="theme-color" content="#061d13">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="CBT Kartika">
    <title>Portal RPP, LMS & CBT - SMA Kartika III-1 Banyubiru</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="icon" type="image/png" href="{{ asset('images/pwa-icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="/manifest.json">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col font-sans text-slate-900 bg-gradient-to-b from-slate-100 via-slate-50 to-white selection:bg-brand-700 selection:text-white">
    <!-- Top Bar -->
    <header class="bg-brand-950 text-white border-b border-brand-900 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/logo-kartika.png') }}" alt="Logo SMA Kartika III-1 Banyubiru" width="44" height="44" class="w-11 h-11 object-contain drop-shadow-xs">
                </div>
                <div>
                    <h1 class="text-sm sm:text-base font-bold text-white tracking-tight leading-tight">SMA KARTIKA III-1 BANYUBIRU</h1>
                    <p class="text-[11px] sm:text-xs text-gold-300 font-medium">Sistem Terpadu RPP, LMS & CBT</p>
                </div>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-900 text-emerald-300 border border-brand-800 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    Server Lab & Cloud Siap
                </span>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-1.5 rounded-xl bg-gold-400 hover:bg-gold-500 text-brand-950 font-bold text-sm transition shadow-xs">
                        Buka Dashboard →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-xl bg-gold-400 hover:bg-gold-500 text-brand-950 font-bold text-sm transition shadow-xs border border-gold-300">
                        Masuk Sistem
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1">
        <section class="relative overflow-hidden py-16 sm:py-20 text-white" style="background: linear-gradient(135deg, #061d13 0%, #0b3120 50%, #061d13 100%);">
            <!-- Background Decorative Grid -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#d97706_1px,transparent_1px)] [background-size:24px_24px]"></div>

            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
                <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold bg-brand-900 text-gold-300 border border-brand-700 mb-6 shadow-xs">
                    <svg class="w-3.5 h-3.5 text-gold-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    Tahun Ajaran 2026/2027 • Integrasi Modul Ajar RPP • LMS E-Learning • Ujian Digital CBT
                </span>
                <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-tight mb-5">
                    Platform Perencanaan, Pembelajaran & Asesmen Digital<br>
                    <span class="text-gold-300">
                        Interaktif, Teratur, dan Terpercaya
                    </span>
                </h2>
                <p class="max-w-3xl mx-auto text-sm sm:text-base text-brand-100 leading-relaxed mb-8 font-normal">
                    Menghubungkan perencanaan kurikulum <strong>Rencana Pelaksanaan Pembelajaran (RPP/Modul Ajar)</strong> secara otomatis dengan ruang kelas daring <strong>Learning Management System (LMS)</strong> dan mesin evaluasi terstandar <strong>Computer Based Test (CBT)</strong> untuk seluruh civitas akademika SMA Kartika III-1 Banyubiru.
                </p>

                <div class="flex flex-wrap justify-center gap-3.5">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl bg-gold-400 hover:bg-gold-500 text-brand-950 font-black text-sm transition shadow-md flex items-center gap-2 border border-gold-300">
                            <span>Masuk ke Dashboard</span>
                            <svg class="w-4 h-4 text-brand-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-gold-400 hover:bg-gold-500 text-brand-950 font-black text-sm transition shadow-md flex items-center gap-2 border border-gold-300">
                            <span>Masuk Portal Belajar & Ujian</span>
                            <svg class="w-4 h-4 text-brand-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endauth
                    <a href="#ekosistem-terpadu" class="px-5 py-3 rounded-xl bg-brand-900 hover:bg-brand-800 text-brand-100 font-bold text-sm transition border border-brand-700 shadow-xs flex items-center gap-2">
                        <span>Jelajahi Ekosistem RPP, LMS & CBT</span>
                        <svg class="w-4 h-4 text-gold-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Gateway Access Cards -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20 pb-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1: Siswa -->
                <div class="bento-card p-6 sm:p-7 flex flex-col justify-between group hover:border-amber-400/80">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 text-brand-950 flex items-center justify-center mb-5 font-bold shadow-md shadow-amber-500/20 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-6 h-6 text-brand-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200/80">Peserta Ujian</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-2 mb-2 group-hover:text-amber-800 transition">Peserta Didik (Siswa)</h3>
                        <p class="text-xs text-slate-600 mb-6 leading-relaxed">
                            Akses modul materi pelajaran PDF & video, kumpulkan tugas mandiri secara daring, dan ikuti ujian terstandar dengan token sesi dari proktor.
                        </p>
                    </div>
                    <a href="{{ route('login') }}" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-brand-900 to-brand-950 hover:from-brand-950 hover:to-black text-white font-bold text-xs text-center transition-all duration-200 shadow-sm btn-glow-brand block">
                        Login Siswa (NISN) →
                    </a>
                </div>

                <!-- Card 2: Guru -->
                <div class="bento-card p-6 sm:p-7 flex flex-col justify-between group hover:border-emerald-500/80">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center mb-5 font-bold shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/80">Pendidik</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-2 mb-2 group-hover:text-emerald-800 transition">Guru Mata Pelajaran</h3>
                        <p class="text-xs text-slate-600 mb-6 leading-relaxed">
                            Kelola modul ajar RPP Kurikulum Merdeka, sinkronkan materi & penugasan ke kelas daring LMS, serta susun bank soal ujian dengan pemetaan indikator TP otomatis.
                        </p>
                    </div>
                    <a href="{{ route('login') }}" class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs text-center transition-all duration-200 shadow-sm block">
                        Portal Guru (RPP, LMS & Soal) →
                    </a>
                </div>

                <!-- Card 3: Proktor & Admin -->
                <div class="bento-card p-6 sm:p-7 flex flex-col justify-between group hover:border-blue-500/80">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center mb-5 font-bold shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-800 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200/80">Operator</span>
                        <h3 class="text-xl font-extrabold text-slate-900 mt-2 mb-2 group-hover:text-blue-800 transition">Proktor & Administrator</h3>
                        <p class="text-xs text-slate-600 mb-6 leading-relaxed">
                            Pantau status pengerjaan peserta ujian secara real-time di lab komputer, kelola rilis token sesi, manajemen akun & rombel, serta sinkronisasi Dapodik.
                        </p>
                    </div>
                    <a href="{{ route('login') }}" class="w-full py-3 px-4 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs text-center transition-all duration-200 shadow-sm block">
                        Pengawasan & Operator →
                    </a>
                </div>
            </div>
              <!-- SECTION: TIGA PILAR UTAMA (RPP, LMS & CBT) -->
        <section id="ekosistem-terpadu" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 border-t border-slate-200">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="inline-flex items-center gap-2 text-xs font-black text-brand-800 tracking-wider uppercase bg-brand-50 px-3.5 py-1.5 rounded-full border border-brand-200 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                    Ekosistem Digital Kurikulum Merdeka
                </span>
                <h3 class="text-2xl sm:text-4xl font-black text-slate-900 mt-4 tracking-tight">Tiga Layanan Utama dalam Satu Sistem Terpadu</h3>
                <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed">
                    Menyelaraskan seluruh tahapan pendidikan: dari perencanaan modul ajar (RPP), pelaksanaan pembelajaran interaktif (LMS), hingga evaluasi terstandar dengan analisis psikometri (CBT).
                </p>
            </div>

            <!-- Grid 3 Pilar Layanan -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 mb-14">
                <!-- PILAR 1: RPP (Rencana Pelaksanaan Pembelajaran & Modul Ajar) -->
                <div class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/90 shadow-xs hover:border-blue-500 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center gap-3.5 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-black shadow-md shadow-blue-500/20 shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-[11px] font-black uppercase tracking-wider text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-md border border-blue-200/80">Pilar 1</span>
                                <h4 class="text-lg sm:text-xl font-black text-slate-900 group-hover:text-blue-700 transition">Perencanaan RPP Digital</h4>
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
                            Integrasi modul ajar Kurikulum Merdeka yang secara otomatis memetakan materi pertemuan kelas daring dan kisi-kisi instrumen asesmen.
                        </p>

                        <ul class="space-y-3 text-xs sm:text-sm text-slate-700">
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Impor RPP Berstruktur:</strong> Mengimpor modul ajar JSON & dokumen lengkap dengan capaian dan alokasi waktu.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Otomasi Pertemuan LMS:</strong> Menghasilkan rancangan topik pertemuan dan aktivitas belajar kelas secara instan.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Sinkronisasi Bank Soal CBT:</strong> Memetakan Tujuan Pembelajaran (TP) dan bobot kesukaran langsung ke butir soal ujian.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Pelacakan Status Integrasi:</strong> Memastikan seluruh topik ajar memiliki instrumen evaluasi yang valid.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- PILAR 2: LMS (Learning Management System) -->
                <div class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/90 shadow-xs hover:border-amber-500 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center gap-3.5 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-yellow-600 text-white flex items-center justify-center font-black shadow-md shadow-amber-500/20 shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-[11px] font-black uppercase tracking-wider text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-md border border-amber-200/80">Pilar 2</span>
                                <h4 class="text-lg sm:text-xl font-black text-slate-900 group-hover:text-amber-700 transition">Learning Management System (LMS)</h4>
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
                            Mendukung kegiatan belajar mengajar harian, pendalaman materi kurikulum merdeka, dan pengumpulan tugas terstruktur di luar jam tatap muka.
                        </p>

                        <ul class="space-y-3 text-xs sm:text-sm text-slate-700">
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Modul Multimedia:</strong> Berkas bacaan PDF, modul PPT, artikel rangkuman, dan tautan video edukatif interaktif.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Tugas Daring Terjadwal:</strong> Siswa mengunggah hasil penugasan mandiri dengan catatan tenggat waktu otomatis.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Penilaian & Catatan Feedback:</strong> Guru memberikan skor nilai angka (0-100) serta umpan balik korektif transparan.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Ruang Diskusi Konsep:</strong> Forum interaktif per mata pelajaran untuk memperdalam pemahaman materi.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- PILAR 3: CBT (Computer Based Test) -->
                <div class="bg-white rounded-3xl p-7 sm:p-8 border border-slate-200/90 shadow-xs hover:border-emerald-600 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center gap-3.5 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-800 text-white flex items-center justify-center font-black shadow-md shadow-emerald-500/20 shrink-0">
                                <svg class="w-6 h-6 text-gold-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-[11px] font-black uppercase tracking-wider text-emerald-800 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200/80">Pilar 3</span>
                                <h4 class="text-lg sm:text-xl font-black text-slate-900 group-hover:text-emerald-700 transition">Computer Based Test (CBT)</h4>
                            </div>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 mb-6 leading-relaxed">
                            Mesin ujian terstandar dengan keamanan tinggi untuk Penilaian Sumatif, PTS, PAS, dan Simulasi Asesmen Nasional.
                        </p>

                        <ul class="space-y-3 text-xs sm:text-sm text-slate-700">
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Bank Soal Multi-Format:</strong> Pilihan Ganda, Benar/Salah, Menjodohkan (Matching), dan Esai dengan rubrik.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Anti-Cheat & Auto-Save:</strong> Deteksi perpindahan tab, mode fullscreen, dan penyimpanan jawaban realtime.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Monitoring Live Proktor:</strong> Pemantauan status pengerjaan siswa secara langsung di lab komputer.
                                </div>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                                <div>
                                    <strong class="text-slate-900 font-bold">Analisis Psikometrik:</strong> Kalkulasi otomatis daya pembeda dan tingkat kesukaran setiap butir soal.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Pipeline Alur Sinkronisasi RPP -> LMS -> CBT (Redesigned & Estetis) -->
            <div class="relative overflow-hidden rounded-3xl text-white border border-emerald-900/60 shadow-2xl p-8 sm:p-12" style="background: radial-gradient(circle at 50% 0%, #0d3822 0%, #051d13 65%, #020c08 100%);">
                <!-- Background Glow Effect -->
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-gold-400/10 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Section Header Inside Card -->
                <div class="text-center max-w-2xl mx-auto mb-10 relative z-10">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold bg-gold-400/15 text-gold-300 border border-gold-400/30 mb-3.5 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
                        Alur Sinkronisasi Otomatis Kurikulum Merdeka
                    </span>
                    <h4 class="text-2xl sm:text-3xl font-black tracking-tight text-white leading-snug">
                        Satu Dokumen RPP untuk Ruang Belajar & Ujian
                    </h4>
                    <p class="text-xs sm:text-sm text-brand-100 mt-2.5 leading-relaxed font-normal">
                        Guru cukup mengimpor modul ajar sekali. Sistem otomatis mendistribusikan materi silabus ke kelas daring LMS dan merumuskan instrumen bank soal asesmen CBT.
                    </p>
                </div>

                <!-- Three Steps Pipeline Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 relative z-10">
                    <!-- Step 1: RPP -->
                    <div class="bg-white/5 hover:bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 hover:border-blue-400/50 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-400/30">
                                    Tahap 01
                                </span>
                                <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-300 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                            </div>
                            <h5 class="text-base font-bold text-white mb-2">Perencanaan (RPP)</h5>
                            <p class="text-xs text-slate-300 leading-relaxed mb-4">
                                Unggah dokumen modul ajar Kurikulum Merdeka (JSON/Word). Sistem membaca Capaian Pembelajaran (CP) dan rincian alokasi waktu.
                            </p>
                        </div>
                        <div class="pt-3 border-t border-white/10 text-[11px] font-semibold text-blue-300 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            Format Fleksibel & AI-Assisted
                        </div>
                    </div>

                    <!-- Step 2: LMS -->
                    <div class="bg-white/5 hover:bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 hover:border-gold-400/50 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-black uppercase tracking-wider bg-gold-400/20 text-gold-300 border border-gold-400/30">
                                    Tahap 02
                                </span>
                                <div class="w-9 h-9 rounded-xl bg-gold-400/20 text-gold-300 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                            </div>
                            <h5 class="text-base font-bold text-white mb-2">Pembelajaran (LMS)</h5>
                            <p class="text-xs text-slate-300 leading-relaxed mb-4">
                                Pertemuan mingguan, modul referensi multimedia, dan wadah tugas mandiri otomatis terbit di kelas daring siswa.
                            </p>
                        </div>
                        <div class="pt-3 border-t border-white/10 text-[11px] font-semibold text-gold-300 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold-400"></span>
                            Distribusi Silabus & Tugas Instan
                        </div>
                    </div>

                    <!-- Step 3: CBT -->
                    <div class="bg-white/5 hover:bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/10 hover:border-emerald-400/50 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                                    Tahap 03
                                </span>
                                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                            <h5 class="text-base font-bold text-white mb-2">Asesmen (CBT)</h5>
                            <p class="text-xs text-slate-300 leading-relaxed mb-4">
                                Kisi-kisi soal tersinkronisasi dengan Tujuan Pembelajaran (TP). Ujian terlaksana aman di lab dengan evaluasi butir soal otomatis.
                            </p>
                        </div>
                        <div class="pt-3 border-t border-white/10 text-[11px] font-semibold text-emerald-300 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Analisis Psikometrik & Auto-Grading
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tata Tertib Section -->
        <section id="tata-tertib" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 border-t border-slate-200">
            <div class="max-w-3xl mx-auto text-center mb-10">
                <span class="text-xs font-black text-gold-700 tracking-wider uppercase">Protokol Pelaksanaan</span>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Tata Tertib Peserta Ujian Digital</h3>
                <p class="text-xs sm:text-sm text-slate-600 mt-2">Dipersiapkan demi kelancaran dan integritas penilaian di lingkungan sekolah.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <div class="flex items-start gap-4 p-5 bg-white rounded-2xl border border-slate-200 shadow-xs">
                    <div class="w-8 h-8 rounded-xl bg-brand-100 text-brand-900 font-black flex items-center justify-center shrink-0 text-sm">1</div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Kerahasiaan Akun & Token</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Login hanya dengan NISN pribadi. Token ujian hanya berlaku sesuai jadwal dan sesi ruang yang telah ditetapkan oleh proktor.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-5 bg-white rounded-2xl border border-slate-200 shadow-xs">
                    <div class="w-8 h-8 rounded-xl bg-brand-100 text-brand-900 font-black flex items-center justify-center shrink-0 text-sm">2</div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Penyimpanan Otomatis (Auto-Save)</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Sistem menyimpan setiap jawaban Anda secara instan ke server lokal. Jawaban tetap aman walau terjadi mati listrik atau kendala komputer.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-5 bg-white rounded-2xl border border-slate-200 shadow-xs">
                    <div class="w-8 h-8 rounded-xl bg-brand-100 text-brand-900 font-black flex items-center justify-center shrink-0 text-sm">3</div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Integritas & Deteksi Tab</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Sistem mendeteksi perpindahan tab, aplikasi latar belakang, dan mode layar penuh. Segala aktivitas terekam dalam log pengawasan proktor.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-5 bg-white rounded-2xl border border-slate-200 shadow-xs">
                    <div class="w-8 h-8 rounded-xl bg-brand-100 text-brand-900 font-black flex items-center justify-center shrink-0 text-sm">4</div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Bantuan Teknis Proktor</h4>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">Bila terjadi kendala browser atau logout tidak disengaja, segera angkat tangan agar proktor ruang dapat melakukan verifikasi reset sesi.</p>
                    </div>
                </div>
            </div>
        </section>
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

    <!-- Footer -->
    <footer class="bg-brand-950 text-slate-400 py-8 border-t border-brand-900 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <span class="text-white font-bold">SMA KARTIKA III-1 BANYUBIRU</span> • Yayasan Kartika Jaya Cabang III
                <p class="text-slate-500 mt-0.5">Kecamatan Banyubiru, Kabupaten Semarang, Jawa Tengah</p>
            </div>
            <div class="text-slate-500 text-center md:text-right">
                Sistem Terpadu CBT & LMS v2.6 Pro • Hak Cipta &copy; {{ date('Y') }} SMA Kartika III-1 Banyubiru.
            </div>
        </div>
    </footer>

    <!-- Service Worker & PWA Logic -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('PWA Service Worker registered:', reg.scope))
                    .catch((err) => console.error('PWA Service Worker registration failed:', err));
            });
        }

        let deferredPrompt;
        const installBanner = document.getElementById('pwaInstallBanner');
        const installBtn = document.getElementById('pwaInstallBtn');
        const dismissBtn = document.getElementById('pwaDismissBtn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (!localStorage.getItem('pwa_dismissed')) {
                installBanner?.classList.remove('hidden');
            }
        });

        installBtn?.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
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
