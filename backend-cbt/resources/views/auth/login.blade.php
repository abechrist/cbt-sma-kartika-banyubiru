<x-layouts.app :title="'Masuk Sistem Asesmen & Pembelajaran'">
    <div class="w-full max-w-5xl mx-auto">
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden grid grid-cols-1 lg:grid-cols-12">
            <!-- Left Panel: Institutional Identity & Assessment Gate Guidelines -->
            <div class="lg:col-span-6 bg-brand-950 text-white p-5 lg:px-7 lg:py-5 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-brand-900">
                <div>
                    <!-- School Crest & Title -->
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 p-1.5 bg-white rounded-xl border border-gold-400/40 flex items-center justify-center shrink-0">
                            <img src="{{ asset('images/logo-kartika.png') }}" alt="Logo SMA Kartika III-1 Banyubiru" width="40" height="40" class="w-full h-full object-contain">
                        </div>
                        <div class="min-w-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-brand-900 text-gold-300 border border-brand-800 text-[10px] font-bold tracking-wide">
                                Tahun Ajaran 2026/2027
                            </span>
                            <h1 class="text-base sm:text-lg font-black text-white tracking-[-0.02em] leading-snug mt-0.5">
                                SMA KARTIKA III-1 BANYUBIRU
                            </h1>
                            <p class="text-[11px] text-brand-200 font-medium">
                                Sistem Asesmen, Pembelajaran (LMS) & Modul Ajar (RPP)
                            </p>
                        </div>
                    </div>

                    <!-- Access Instructions by Role -->
                    <div class="mt-3.5 space-y-2">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-gold-300">
                            Panduan Akses Pengguna
                        </h2>

                        <div class="p-2.5 rounded-lg bg-brand-900/80 border border-brand-800 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-amber-400/15 text-gold-300 border border-gold-400/30 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="text-xs leading-snug">
                                <div class="font-bold text-white">Peserta Didik (Siswa)</div>
                                <p class="text-brand-200 mt-0.5">
                                    Masuk menggunakan <strong class="text-white font-semibold">NISN (10 digit)</strong> serta kata sandi akun ujian yang diberikan sekolah.
                                </p>
                            </div>
                        </div>

                        <div class="p-2.5 rounded-lg bg-brand-900/80 border border-brand-800 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-emerald-400/15 text-emerald-300 border border-emerald-400/30 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="text-xs leading-snug">
                                <div class="font-bold text-white">Guru, Pengawas & Staf Akademik</div>
                                <p class="text-brand-200 mt-0.5">
                                    Masuk menggunakan <strong class="text-white font-semibold">Username / NIP / Alamat Email</strong> terdaftar untuk mengelola bank soal, jadwal ujian, LMS, dan RPP.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Concise Assessment Rules -->
                    <div class="mt-3.5 pt-3 border-t border-brand-800/80">
                        <h2 class="text-[11px] font-bold uppercase tracking-wider text-brand-300 mb-1.5">
                            Ketentuan Pelaksanaan Ujian
                        </h2>
                        <ul class="space-y-1 text-xs text-brand-200 leading-snug">
                            <li class="flex items-start gap-2">
                                <svg class="w-3.5 h-3.5 text-gold-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Siapkan kode token 6 karakter dari pengawas ruang/kelas sebelum memulai sesi ujian.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-3.5 h-3.5 text-gold-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Jawaban ujian tersimpan otomatis di sistem dan dilengkapi proteksi Mode Layar Penuh & Deteksi Pindah Tab.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-3.5 pt-2.5 border-t border-brand-900 flex items-center justify-between text-[11px] text-brand-300">
                    <span>Yayasan Kartika Jaya Cabang III</span>
                    <span class="font-mono">Banyubiru, Kab. Semarang</span>
                </div>
            </div>

            <!-- Right Panel: Clean Authentication Form -->
            <div class="lg:col-span-6 p-5 lg:px-7 lg:py-5 flex flex-col justify-center bg-white">
                <div class="mb-3.5">
                    <h2 class="text-lg font-black text-slate-900 tracking-tight">
                        Autentikasi Pengguna
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Masukkan identitas akun Anda untuk mengakses dashboard sesuai peran.
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-3.5">
                    @csrf

                    <div>
                        <label for="login" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            NISN / Username / Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus
                                autocomplete="username" autocorrect="off" autocapitalize="off" spellcheck="false" enterkeyhint="next"
                                placeholder="Masukkan NISN Siswa atau Username / Email Guru"
                                class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-base sm:text-sm focus:bg-white focus:ring-2 focus:ring-brand-700/25 focus:border-brand-700 outline-none transition">
                        </div>
                        @error('login')
                            <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Kata Sandi (Password)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" required
                                autocomplete="current-password" enterkeyhint="done"
                                placeholder="Masukkan kata sandi"
                                class="w-full pl-10 pr-12 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-slate-900 text-base sm:text-sm focus:bg-white focus:ring-2 focus:ring-brand-700/25 focus:border-brand-700 outline-none transition">
                            <button type="button" onclick="togglePasswordVisibility()" aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute inset-y-0 right-0 w-11 flex items-center justify-center text-slate-400 hover:text-slate-700 transition">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer select-none py-0.5">
                            <input type="checkbox" name="remember" class="h-4 w-4 text-brand-700 focus:ring-brand-700 border-slate-300 rounded">
                            <span class="ml-2 text-xs font-medium text-slate-600">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full min-h-[42px] py-2.5 px-4 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-sm transition flex items-center justify-center gap-2">
                        <span>Masuk ke Sistem</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <!-- Institutional Help Notice -->
                <div class="mt-4 pt-3.5 border-t border-slate-200">
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Mengalami kendala akun atau lupa kata sandi? Silakan hubungi <strong class="text-slate-800 font-semibold">Pengawas Ujian</strong> atau <strong class="text-slate-800 font-semibold">Administrator Sekolah</strong>.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</x-layouts.app>
