<x-layouts.app :title="'Tautan Kiosk Peserta'">
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('monitoring.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                ← Kembali ke Monitoring Ujian
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Peluncuran Kiosk Mandiri Peserta</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ $attempt->user?->name }} • {{ $attempt->session->name }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6 sm:p-8 space-y-6">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    URL Kiosk Terproteksi (Lockdown Aktif)
                </label>
                <p class="text-xs text-slate-500 mb-3">
                    Salin tautan ini dan jalankan pada peramban komputer peserta untuk langsung memulai lembar pengerjaan dengan proteksi penuh.
                </p>
                <input type="text" readonly value="{{ $takeUrl }}"
                       class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs font-mono bg-slate-50 text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none"
                       onfocus="this.select()">
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ $takeUrl }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-gold-500 hover:bg-gold-600 text-white font-bold text-xs shadow-xs transition">
                    Buka di Jendela Kiosk Baru
                </a>
                <button onclick="navigator.clipboard.writeText('{{ $takeUrl }}'); alert('URL Kiosk berhasil disalin!');"
                        class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-xs transition">
                    Salin URL Kiosk
                </button>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 leading-relaxed">
                <strong>Tips Teknis Proktor:</strong> Untuk komputer lab dengan mode terisolasi penuh tanpa akses alt-tab, buka shortcut Google Chrome dengan argumen <code class="bg-white border px-1.5 py-0.5 rounded font-mono font-bold text-brand-800">--kiosk</code> sebagaimana dijelaskan pada panduan <a href="{{ route('kiosk.launch') }}" class="text-brand-800 underline font-semibold">Kiosk / Lockdown Mode</a>.
            </div>
        </div>
    </div>
</x-layouts.app>