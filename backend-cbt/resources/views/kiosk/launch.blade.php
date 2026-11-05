<x-layouts.app :title="'Mode Fokus & Keamanan Ujian'">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Mode Fokus &amp; Keamanan Ujian</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Panduan fitur proteksi Mode Layar Penuh dan Deteksi Pindah Tab selama sesi asesmen berlangsung.</p>
        </div>
        <a href="{{ route('monitoring.index') }}" class="px-4 py-2 rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs transition flex items-center gap-1.5">
            Buka Monitoring Ujian →
        </a>
    </div>

    <div class="grid lg:grid-cols-2 gap-5 mb-6">
        <!-- Step 1: Web Focus Mode -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-md bg-brand-800 text-white text-xs font-bold flex items-center justify-center">1</span>
                    <h2 class="font-bold text-slate-900 text-sm">Mekanisme Mode Fokus Ujian</h2>
                </div>
            </div>
            <div class="p-5 sm:p-6 space-y-4 text-xs text-slate-700">
                <p class="text-slate-600 leading-relaxed">
                    Saat peserta didik menekan tombol <strong>Mulai Ujian</strong> setelah memasukkan token sesi, sistem secara otomatis mengaktifkan parameter proteksi <code class="bg-slate-100 text-brand-800 px-1.5 py-0.5 rounded font-mono font-bold">?kiosk=1</code> pada halaman pengerjaan ujian.
                </p>

                <div>
                    <span class="font-bold text-slate-800 block mb-1">Format Tautan Mode Fokus</span>
                    <pre class="bg-slate-900 text-emerald-400 rounded-lg p-3.5 overflow-x-auto text-xs font-mono select-all">{{ $takeBaseUrl }}?kiosk=1</pre>
                </div>

                <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-lg text-amber-900 text-xs leading-relaxed">
                    <strong>Catatan Pengawas:</strong> Fitur ini berjalan langsung di peramban (browser) perangkat peserta tanpa memerlukan instalasi aplikasi tambahan, memastikan ujian tetap kondusif di setiap ruang/kelas.
                </div>
            </div>
        </div>

        <!-- Step 2: Active Protection Features -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-md bg-brand-800 text-white text-xs font-bold flex items-center justify-center">2</span>
                    <h2 class="font-bold text-slate-900 text-sm">Fitur Proteksi yang Aktif</h2>
                </div>
            </div>
            <div class="p-5 sm:p-6">
                <ul class="space-y-3 text-xs text-slate-700">
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Mode Layar Penuh (Fullscreen):</strong> Lembar ujian ditampilkan memenuhi layar perangkat. Keluar dari layar penuh dicatat secara otomatis pada log pengawas.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Deteksi Pindah Tab &amp; Kehilangan Fokus:</strong> Setiap aktivitas berpindah tab atau keluar dari jendela ujian langsung tercatat pada dashboard monitoring pengawas.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Anti-Salin &amp; Klik Kanan:</strong> Menu klik kanan, seleksi teks soal, serta pintasan salin/tempel dinonaktifkan selama ujian berlangsung.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Kunci Navigasi Riwayat:</strong> Tombol kembali (Back) pada peramban dikunci agar peserta tidak keluar dari lembar soal secara tidak sengaja.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Active Sessions List -->
    @if($sessions->isNotEmpty())
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50">
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-md bg-brand-800 text-white text-xs font-bold flex items-center justify-center">3</span>
                    <h2 class="font-bold text-slate-900 text-sm">Pemantauan Sesi Ujian Berjalan</h2>
                </div>
            </div>
            <div class="p-5 sm:p-6">
                <p class="text-xs text-slate-600 mb-4">
                    Pilih sesi ujian di bawah untuk memantau kehadiran dan indikator fokus peserta secara langsung.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($sessions as $session)
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-xs">{{ $session->name }}</h3>
                            <p class="text-[11px] text-slate-500">{{ $session->exam->name }} • {{ $session->room ?? 'Ruang Ujian' }}</p>
                        </div>
                        <a href="{{ route('monitoring.session', $session) }}" class="px-3 py-1.5 rounded-lg bg-brand-800 text-white font-bold text-xs hover:bg-brand-900 transition">
                            Pantau Sesi →
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-slate-400 text-xs">
            Belum ada sesi ujian yang dijadwalkan atau sedang berlangsung saat ini.
        </div>
    @endif
</x-layouts.app>
