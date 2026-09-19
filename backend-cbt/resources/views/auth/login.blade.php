<x-layouts.app :title="'Masuk Portal Terpadu'">
    <div class="max-w-md mx-auto py-6 sm:py-12 px-2">
        <div class="bg-white rounded-[1.75rem] shadow-[0_24px_70px_-28px_rgba(6,29,19,0.35)] border border-slate-200/90 overflow-hidden transition-all duration-300">
            <!-- Institutional Card Header (Sleek Mesh Gradient) -->
            <div class="bg-brand-950 p-7 sm:p-8 text-center text-white relative overflow-hidden">
                <!-- Background Accent Glow -->
                <div class="absolute -top-12 -right-12 w-36 h-36 bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-12 -left-12 w-36 h-36 bg-gold-400/15 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="w-16 h-16 mx-auto mb-4 p-2 bg-white rounded-2xl shadow-lg border border-gold-400/40 flex items-center justify-center transform hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset('images/logo-kartika.png') }}" alt="Logo Kartika Jaya" width="48" height="48" class="w-full h-full object-contain">
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-[-0.025em] leading-snug">
                        SMA KARTIKA III-1 BANYUBIRU
                    </h1>
                    <p class="text-xs text-gold-300 font-semibold mt-1 tracking-wide">
                        Portal Terpadu RPP • LMS • CBT
                    </p>
                </div>
            </div>

            <!-- Role Selector Hints -->
            <div class="bg-[#f7f9f6] px-6 py-3.5 border-b border-slate-200/80 flex flex-wrap justify-center gap-2.5 text-xs font-semibold">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-100/90 text-amber-900 border border-amber-200/90 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Siswa: Gunakan NISN</span>
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-200/90 text-slate-800 border border-slate-300 shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Guru / Staf: Gunakan Email</span>
                </span>
            </div>

            <!-- Login Form Card -->
            <div class="p-6 sm:p-8">
                <!-- Session Alert Messages -->
                @if (session('status'))
                    <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-2xl flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-2xl space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="login" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                            Nomor Induk / Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" name="login" id="login" value="{{ old('login') }}" required autofocus
                                placeholder="NISN Siswa atau Email Pegawai"
                                class="w-full pl-10 pr-4 py-3.5 bg-slate-50 border border-slate-300/90 rounded-xl text-slate-900 text-sm focus:bg-white focus:ring-2 focus:ring-brand-700/30 focus:border-brand-700 outline-none transition-all duration-150 shadow-2xs">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">
                            Kata Sandi (Password)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" required
                                placeholder="Masukkan kata sandi"
                                class="w-full pl-10 pr-10 py-3.5 bg-slate-50 border border-slate-300/90 rounded-xl text-slate-900 text-sm focus:bg-white focus:ring-2 focus:ring-brand-700/30 focus:border-brand-700 outline-none transition-all duration-150 shadow-2xs">
                            <button type="button" onclick="togglePasswordVisibility()" aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 transition">
                                <svg id="eyeIcon" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs font-semibold text-rose-600 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="h-4 w-4 text-brand-700 focus:ring-brand-700 border-slate-300 rounded-md">
                            <span class="ml-2.5 text-xs font-medium text-slate-600">Ingat saya di komputer ini</span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full py-3.5 px-4 rounded-xl bg-brand-900 hover:bg-brand-950 text-white font-extrabold text-sm tracking-wide transition-all duration-200 shadow-md btn-glow-brand flex items-center justify-center gap-2">
                        <span>Masuk ke Portal Terpadu</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <!-- Help Notice -->
                <div class="mt-7 pt-5 border-t border-slate-200/80 text-center">
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Mengalami kendala akun atau lupa kata sandi? Silakan hubungi <strong class="text-slate-800 font-bold">Proktor Ruang</strong> atau tim IT laboratorium sekolah.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>
</x-layouts.app>