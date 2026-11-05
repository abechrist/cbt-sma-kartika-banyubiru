<x-layouts.app :title="'Tautan Mode Fokus Peserta'">
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('monitoring.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                ← Kembali ke Monitoring Ujian
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Tautan Mode Fokus Ujian Peserta</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ $attempt->user?->name }} • {{ $attempt->session->name }}</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-7 space-y-5">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    URL Lembar Ujian (Mode Fokus &amp; Layar Penuh Aktif)
                </label>
                <p class="text-xs text-slate-500 mb-3">
                    Gunakan tautan ini untuk membuka kembali lembar pengerjaan peserta dengan proteksi Mode Layar Penuh dan Deteksi Pindah Tab.
                </p>
                <input type="text" readonly value="{{ $takeUrl }}"
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-xs font-mono bg-slate-50 text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none"
                       onfocus="this.select()">
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ $takeUrl }}" target="_blank" class="px-4 py-2.5 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition">
                    Buka Lembar Ujian
                </a>
                <button type="button" onclick="navigator.clipboard.writeText('{{ $takeUrl }}'); alert('Tautan Mode Fokus berhasil disalin!');"
                        class="px-4 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 font-bold text-xs transition">
                    Salin URL
                </button>
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-600 leading-relaxed">
                <strong>Informasi Pengawas:</strong> Tautan di atas menyertakan parameter <code class="bg-white border border-slate-200 px-1.5 py-0.5 rounded font-mono font-bold text-brand-800">?kiosk=1</code> untuk mengaktifkan Mode Layar Penuh dan pencatatan perpindahan tab secara otomatis. Lihat rincian di halaman <a href="{{ route('kiosk.launch') }}" class="text-brand-800 underline font-semibold">Mode Fokus &amp; Keamanan Ujian</a>.
            </div>
        </div>
    </div>
</x-layouts.app>
