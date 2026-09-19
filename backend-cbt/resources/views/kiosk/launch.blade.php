<x-layouts.app :title="'Kiosk / Lockdown Mode'">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold bg-brand-100 text-brand-800 border border-brand-200 mb-2">
                Integritas & Keamanan Lab
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Kiosk / Lockdown Security Mode</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Panduan konfigurasi terminal laboratorium agar komputer terkunci fullscreen tanpa celah kecurangan.</p>
        </div>
        <a href="{{ route('monitoring.index') }}" class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
            Buka Monitoring Ujian →
        </a>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <!-- Step 1: Run in Kiosk Mode -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-brand-800 text-white text-xs font-bold flex items-center justify-center">1</span>
                    <h3 class="font-bold text-slate-900 text-sm">Luncurkan Browser dalam Mode Kiosk</h3>
                </div>
            </div>
            <div class="p-6 space-y-4 text-xs text-slate-700">
                <p class="text-slate-600 leading-relaxed">
                    Pada setiap PC klien di laboratorium, buat pintasan desktop (shortcut) yang menjalankan Google Chrome atau Microsoft Edge dengan parameter <code class="bg-slate-100 text-brand-800 px-1.5 py-0.5 rounded font-mono font-bold">--kiosk</code>. Lockdown diperkuat otomatis saat URL dibuka dengan <code class="bg-slate-100 text-brand-800 px-1.5 py-0.5 rounded font-mono font-bold">?kiosk=1</code>.
                </p>

                <div>
                    <span class="font-bold text-slate-800 block mb-1">Google Chrome (Windows)</span>
                    <pre class="bg-slate-900 text-emerald-400 rounded-xl p-3.5 overflow-x-auto text-xs font-mono select-all">"C:\Program Files\Google\Chrome\Application\chrome.exe" --kiosk --disable-extensions {{ $takeBaseUrl }}</pre>
                </div>

                <div>
                    <span class="font-bold text-slate-800 block mb-1">Microsoft Edge (Windows)</span>
                    <pre class="bg-slate-900 text-emerald-400 rounded-xl p-3.5 overflow-x-auto text-xs font-mono select-all">"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe" --kiosk {{ $takeBaseUrl }}?kiosk=1</pre>
                </div>

                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-[11px] leading-relaxed">
                    <strong>Catatan Proktor:</strong> Siswa login dengan akun masing-masing, kemudian saat tombol <em>Mulai Ujian</em> ditekan, sistem secara otomatis mengarahkan ke URL dengan parameter <code>?kiosk=1</code> sehingga seluruh proteksi lockdown aktif.
                </div>
            </div>
        </div>

        <!-- Step 2: What is Protected -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-brand-800 text-white text-xs font-bold flex items-center justify-center">2</span>
                    <h3 class="font-bold text-slate-900 text-sm">Fitur Proteksi Lockdown yang Aktif</h3>
                </div>
            </div>
            <div class="p-6">
                <ul class="space-y-3 text-xs text-slate-700">
                    <li class="flex items-start gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
                        <span><strong>Fullscreen Terpaksa:</strong> Layar dikunci memenuhi monitor. Keluar dari fullscreen memicu insiden mencurigakan ke proktor.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
                        <span><strong>Anti-Copy & Klik Kanan:</strong> Klik kanan menu konteks, text selection, dan drag-and-drop dinonaktifkan total (FR-4.7).</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
                        <span><strong>Blokir Tombol Pintas Keyboard:</strong> F5, F11, F12 (DevTools), PrintScreen, serta Ctrl+C/V/P/R/W/T/N/U diblokir.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
                        <span><strong>History Lock:</strong> Tombol Back dan Forward pada browser dinonaktifkan sehingga siswa tidak dapat keluar halaman tanpa sengaja.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
                        <span><strong>Deteksi Blur & Pindah Tab:</strong> Kehilangan fokus jendela otomatis mengirim event telemetri ke dasbor pengawas secara realtime.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">✓</span>
                        <span><strong>Peringatan BeforeUnload:</strong> Menutup tab atau merefresh browser memicu konfirmasi pembatalan.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Active Sessions Launcher List -->
    @if($sessions->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-brand-800 text-white text-xs font-bold flex items-center justify-center">3</span>
                    <h3 class="font-bold text-slate-900 text-sm">Tautan Kiosk Cepat per Sesi Berjalan</h3>
                </div>
            </div>
            <div class="p-6">
                <p class="text-xs text-slate-600 mb-4">
                    Buka <a href="{{ route('monitoring.index') }}" class="text-brand-800 hover:text-brand-900 font-bold underline">Monitoring Ujian</a> untuk melihat status peserta secara live. Anda juga dapat menyalin tautan kiosk per peserta yang memerlukan asistensi khusus.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($sessions as $session)
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-slate-900 text-xs">{{ $session->name }}</h4>
                            <p class="text-[11px] text-slate-500">{{ $session->exam->name }}</p>
                        </div>
                        <a href="{{ route('monitoring.session', $session) }}" class="px-3 py-1.5 rounded-lg bg-brand-800 text-white font-bold text-xs shadow-xs hover:bg-brand-900 transition">
                            Pantau Sesi →
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-8 text-center text-slate-400 text-xs">
            Belum ada sesi ujian yang dijadwalkan atau sedang berlangsung saat ini.
        </div>
    @endif
</x-layouts.app>