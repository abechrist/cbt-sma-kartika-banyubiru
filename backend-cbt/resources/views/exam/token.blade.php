<x-layouts.app :title="'Aktivasi Token Ujian'">
    <div class="max-w-md mx-auto py-6 sm:py-10 px-3 sm:px-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
            <!-- Header Banner -->
            <div class="bg-brand-950 p-6 sm:p-7 text-center text-white border-b border-brand-900">
                <div class="w-12 h-12 sm:w-14 sm:h-14 mx-auto rounded-xl bg-gold-400 text-brand-950 flex items-center justify-center mb-3.5">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-brand-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h1 class="text-lg sm:text-xl font-black text-white tracking-tight leading-snug">
                    Masukkan Token Ujian
                </h1>
                <p class="text-xs text-brand-200 font-medium mt-1">
                    SMA Kartika III-1 Banyubiru • Sistem Asesmen Sekolah
                </p>
            </div>

            <!-- Form Body -->
            <div class="p-6 sm:p-7">
                <p class="text-xs sm:text-sm text-slate-600 text-center mb-5 leading-relaxed">
                    Masukkan <strong class="text-slate-900 font-bold">Kode Token Sesi</strong> yang diberikan oleh Pengawas Ujian di ruang/kelas Anda untuk membuka lembar soal.
                </p>

                <form action="{{ route('exam.token.validate') }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label for="tokenInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wider text-center mb-2">
                            Kode Token Sesi
                        </label>
                        <input type="text" name="token" id="tokenInput" value="{{ old('token') }}" required autofocus
                            placeholder="CBT-XXXXXX"
                            oninput="this.value = this.value.toUpperCase()"
                            class="w-full px-4 py-3 text-center text-xl sm:text-2xl font-mono font-black tracking-widest uppercase bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:ring-2 focus:ring-brand-700/25 focus:border-brand-700 outline-none transition">
                        @error('token')
                            <p class="mt-2 text-xs font-semibold text-rose-600 text-center flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <button type="submit" 
                        class="w-full min-h-[44px] py-3 px-4 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-sm transition flex items-center justify-center gap-2">
                        <span>Mulai Ujian</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <!-- Guidelines Checklist -->
                <div class="mt-6 pt-5 border-t border-slate-200">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-3">Ketentuan Pengerjaan:</div>
                    <ul class="space-y-2 text-xs text-slate-600">
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Timer pengerjaan otomatis berjalan segera setelah lembar soal ditampilkan.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Setiap pilihan jawaban langsung tersimpan otomatis (Auto-Save) di sistem.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>Tetap berada di Mode Layar Penuh dan tidak berpindah tab browser selama sesi ujian berlangsung.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
