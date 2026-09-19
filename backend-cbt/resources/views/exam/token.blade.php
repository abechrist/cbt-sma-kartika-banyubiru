<x-layouts.app :title="'Aktivasi Token Ujian'">
    <div class="max-w-md mx-auto py-8 sm:py-14 px-2">
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 overflow-hidden transition-all duration-300 hover:shadow-2xl">
            <!-- Header Banner (Radial Emerald Gradient) -->
            <div class="bg-gradient-to-br from-brand-950 via-brand-900 to-brand-950 p-8 text-center text-white relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-36 h-36 bg-gold-400/15 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-12 -left-12 w-36 h-36 bg-emerald-400/15 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-gold-400 to-amber-500 text-brand-950 flex items-center justify-center ring-4 ring-gold-400/30 shadow-lg shadow-amber-500/20 mb-4 transform hover:scale-105 transition-transform duration-200">
                        <svg class="w-8 h-8 text-brand-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-snug">
                        Masukkan Token Ujian
                    </h1>
                    <p class="text-xs text-gold-300 font-semibold mt-1">
                        SMA Kartika III-1 Banyubiru • Lab Komputer
                    </p>
                </div>
            </div>

            <!-- Form Body -->
            <div class="p-6 sm:p-8">
                <p class="text-xs sm:text-sm text-slate-600 text-center mb-6 leading-relaxed">
                    Ketik <strong class="text-slate-900 font-bold">Kode Token Sesi</strong> yang telah diumumkan oleh Proktor atau Pengawas Ruang untuk membuka lembar soal ujian Anda.
                </p>

                <form action="{{ route('exam.token.validate') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="tokenInput" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider text-center mb-2.5">
                            Kode Token Sesi
                        </label>
                        <input type="text" name="token" id="tokenInput" value="{{ old('token') }}" required autofocus
                            placeholder="CBT-XXXXXX"
                            oninput="this.value = this.value.toUpperCase()"
                            class="w-full px-4 py-4 text-center text-2xl font-mono font-black tracking-widest uppercase bg-slate-50 border-2 border-slate-300/90 rounded-2xl focus:bg-white focus:ring-4 focus:ring-brand-700/20 focus:border-brand-700 outline-none transition-all duration-150 shadow-inner">
                        @error('token')
                            <p class="mt-2.5 text-xs font-semibold text-rose-600 text-center flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <button type="submit" 
                        class="w-full py-4 px-4 rounded-2xl bg-brand-900 hover:bg-brand-950 text-white font-extrabold text-sm tracking-wide transition-all duration-200 shadow-md hover:shadow-xl btn-glow-brand flex items-center justify-center gap-2">
                        <span>Mulai Ujian</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <!-- Guidelines Checklist (Card Style) -->
                <div class="mt-8 pt-6 border-t border-slate-200/80">
                    <div class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider mb-3">Ketentuan Pengerjaan:</div>
                    <ul class="space-y-2.5 text-xs text-slate-600">
                        <li class="flex items-start gap-2.5">
                            <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                            <span>Timer pengerjaan akan otomatis berjalan segera setelah soal ditampilkan.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">✓</span>
                            <span>Setiap pilihan jawaban langsung tersimpan otomatis (Auto-Save) di server sekolah.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-4 h-4 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 font-bold text-[10px] mt-0.5">⚠</span>
                            <span>Dilarang berpindah tab browser atau keluar dari mode fullscreen (terekam di log pengawas).</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>