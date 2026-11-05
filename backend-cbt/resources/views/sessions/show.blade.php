<x-layouts.app :title="'Detail Sesi - ' . $session->name">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('sessions.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                ← Kembali ke Daftar Sesi
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">{{ $session->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ $session->exam->name }} • Ruang / Kelas: <span class="font-semibold text-slate-800">{{ $session->room ?? 'Ruang Ujian' }}</span></p>
        </div>
        <div class="flex items-center gap-2">
            @if($session->status === 'open' || $session->status === 'in_progress')
                <a href="{{ route('monitoring.session', $session) }}" class="px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    Monitoring Sesi
                </a>
            @endif
            <a href="{{ route('sessions.edit', $session) }}" class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs border border-slate-200">
                Edit Sesi
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Column: Info & Participants -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Session Info Card -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6">
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-200">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Informasi Pelaksanaan Sesi</h2>
                    <div class="flex items-center gap-2">
                        @if($session->status === 'scheduled' || $session->status === 'open')
                            <form action="{{ route('sessions.open', $session) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition">
                                    Buka Sesi
                                </button>
                            </form>
                        @endif
                        @if($session->status === 'open' || $session->status === 'in_progress')
                            <form action="{{ route('sessions.close', $session) }}" method="POST" onsubmit="return confirm('Tutup sesi ujian ini sekarang?')">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-xs border border-rose-200 transition">
                                    Tutup Sesi
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-slate-500 block mb-1">Status Sesi</span>
                        <span class="px-2 py-0.5 text-xs font-bold rounded-md inline-block border
                            @switch($session->status)
                                @case('open') @case('in_progress') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                @case('completed') bg-slate-100 text-slate-800 border-slate-200 @break
                                @default bg-amber-50 text-amber-800 border-amber-200
                            @endswitch
                        ">
                            {{ $session->status }}
                        </span>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-slate-500 block mb-1">Mulai Pelaksanaan</span>
                        <span class="font-bold text-slate-800 font-mono tabular-nums">{{ $session->start_at->format('d/m/Y H:i') }} WIB</span>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-slate-500 block mb-1">Selesai Pelaksanaan</span>
                        <span class="font-bold text-slate-800 font-mono tabular-nums">{{ $session->end_at->format('d/m/Y H:i') }} WIB</span>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-slate-500 block mb-1">Ruang / Kelas</span>
                        <span class="font-bold text-slate-800">{{ $session->room ?? '-' }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-slate-500 block mb-1">Kapasitas Maksimal</span>
                        <span class="font-bold text-slate-800 font-mono tabular-nums">{{ $session->max_participants }} siswa</span>
                    </div>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-slate-500 block mb-1">Peserta Terdaftar</span>
                        <span class="font-bold text-brand-800 font-mono tabular-nums">{{ $session->attempts->count() }} siswa</span>
                    </div>
                    @if($session->instructions)
                    <div class="col-span-2 sm:col-span-3 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-slate-500 block mb-1">Instruksi Pengawas</span>
                        <p class="text-slate-700 leading-relaxed">{{ $session->instructions }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Participants Table -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Daftar Peserta Sesi ({{ $session->attempts->count() }}/{{ $session->max_participants }})</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-500 tabular-nums">Kapasitas: {{ $session->max_participants }}</span>
                </div>
                @if($session->attempts->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-sm">
                        Belum ada peserta yang mengonfirmasi token dan memulai ujian di sesi ini.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-5 py-3 text-left">No</th>
                                    <th class="px-5 py-3 text-left">Nama Siswa</th>
                                    <th class="px-5 py-3 text-left">Status</th>
                                    <th class="px-5 py-3 text-left">Mulai</th>
                                    <th class="px-5 py-3 text-left">Selesai</th>
                                    <th class="px-5 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($session->attempts as $index => $attempt)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-5 py-3.5 font-mono text-xs text-slate-400 tabular-nums">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3.5 font-semibold text-slate-900">{{ $attempt->user->name }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-md border
                                            @switch($attempt->status)
                                                @case('in_progress') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                                @case('submitted') @case('auto_submitted') bg-brand-50 text-brand-800 border-brand-200 @break
                                                @default bg-slate-100 text-slate-700 border-slate-200
                                            @endswitch
                                        ">{{ $attempt->status }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-slate-600 tabular-nums">{{ $attempt->started_at?->format('H:i:s') ?? '-' }}</td>
                                    <td class="px-5 py-3.5 font-mono text-xs text-slate-600 tabular-nums">{{ $attempt->submitted_at?->format('H:i:s') ?? '-' }}</td>
                                    <td class="px-5 py-3.5 text-right">
                                        @if($attempt->status !== 'not_started')
                                            <form action="{{ route('monitoring.reset', $attempt) }}" method="POST" class="inline" onsubmit="return confirm('Reset pengerjaan peserta ini? Semua jawaban sementara akan dihapus!')">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-xs border border-rose-200 transition">
                                                    Reset
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar: Token Generation & Statistics -->
        <div class="space-y-6">
            <!-- Tokens Card -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 mb-4">Aktivasi Token Sesi</h2>
                
                <form action="{{ route('sessions.generate-tokens', $session) }}" method="POST" class="space-y-3 mb-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Jumlah Token</label>
                        <input type="number" name="count" value="{{ $session->max_participants }}" min="1" max="150"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-mono font-bold focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Berlaku Hingga</label>
                        <input type="datetime-local" name="expires_at" value="{{ $session->end_at->format('Y-m-d\TH:i') }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-mono focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none">
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-lg bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs transition">
                        Generate Token Baru
                    </button>
                </form>

                <div class="border-t border-slate-200 pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-700">Token Aktif: <span class="text-brand-800 font-mono tabular-nums">{{ $session->tokens->where('is_active', true)->count() }}</span></span>
                        <a href="{{ route('sessions.print-tokens', $session) }}" class="px-2.5 py-1 rounded-md bg-gold-50 hover:bg-gold-100 text-gold-800 font-bold text-xs border border-gold-200 transition inline-flex items-center gap-1.5" target="_blank">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Token</span>
                        </a>
                    </div>
                    <div class="max-h-60 overflow-y-auto space-y-1.5">
                        @forelse($session->tokens->where('is_active', true)->take(12) as $token)
                        <div class="flex items-center justify-between px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-md text-xs">
                            <code class="font-mono font-bold text-slate-900 tracking-wider">{{ $token->token }}</code>
                            <span class="text-[10px] font-mono text-slate-500 tabular-nums">Exp: {{ $token->expires_at->format('H:i') }}</span>
                        </div>
                        @empty
                        <p class="text-xs text-slate-400 text-center py-3">Belum ada token aktif.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Live Stats -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 mb-4">Statistik Sesi</h2>
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Total Partisipan</span>
                        <span class="font-mono font-bold text-slate-900 tabular-nums">{{ $session->attempts->count() }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Sedang Mengerjakan</span>
                        <span class="font-mono font-bold text-emerald-600 tabular-nums">{{ $session->attempts->where('status', 'in_progress')->count() }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Tuntas Selesai</span>
                        <span class="font-mono font-bold text-brand-800 tabular-nums">{{ $session->attempts->whereIn('status', ['submitted', 'auto_submitted'])->count() }}</span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-500">Belum Memulai</span>
                        <span class="font-mono font-bold text-slate-500 tabular-nums">{{ $session->attempts->where('status', 'not_started')->count() }}</span>
                    </div>
                </div>
                @if($session->status === 'open' || $session->status === 'in_progress')
                <div class="mt-5 pt-4 border-t border-slate-200">
                    <a href="{{ route('monitoring.session', $session) }}" class="w-full py-2.5 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition block text-center">
                        Buka Layar Monitoring →
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
