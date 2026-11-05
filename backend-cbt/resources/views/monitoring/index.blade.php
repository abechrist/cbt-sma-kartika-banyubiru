<x-layouts.app :title="'Pusat Monitoring Ujian'">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Monitoring Ujian Berjalan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pantau kehadiran, konektivitas, dan progres pengerjaan seluruh peserta ujian di setiap ruang/kelas.</p>
        </div>
    </div>

    @if($sessions->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-10 text-center">
            <div class="w-11 h-11 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h2 class="text-base font-bold text-slate-800">Tidak Ada Sesi yang Sedang Berlangsung</h2>
            <p class="text-xs text-slate-500 mt-1">Saat ini belum ada jadwal sesi ujian dengan status terbuka atau sedang berjalan.</p>
        </div>
    @else
        <div class="space-y-5">
            @foreach($sessions as $session)
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <!-- Session Header Banner -->
                <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-lg bg-brand-900 text-gold-400 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="font-bold text-base sm:text-lg text-slate-900 tracking-tight">{{ $session->name }}</h2>
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    AKTIF
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 mt-0.5">
                                {{ $session->exam->name }} • Ruang / Kelas: <span class="font-semibold text-slate-800">{{ $session->room ?? 'Ruang Ujian' }}</span> • Token: <span class="font-mono font-bold text-gold-700">{{ $session->tokens->first()?->token ?? '-' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="w-full sm:w-auto flex flex-col sm:flex-row items-stretch sm:items-center justify-between sm:justify-end gap-3 sm:gap-5 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-200">
                        <div class="flex items-center justify-around sm:justify-start gap-5">
                            <div class="text-center">
                                <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 font-mono tabular-nums leading-none">{{ $session->attempts->where('status', 'in_progress')->count() }}</p>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mt-1">Mengerjakan</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xl sm:text-2xl font-extrabold text-brand-800 font-mono tabular-nums leading-none">{{ $session->attempts->whereIn('status', ['submitted', 'auto_submitted'])->count() }}</p>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mt-1">Selesai</p>
                            </div>
                        </div>
                        <a href="{{ route('monitoring.session', $session) }}" 
                            class="w-full sm:w-auto min-h-[40px] px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition flex items-center justify-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            Monitor Sesi →
                        </a>
                    </div>
                </div>

                <!-- Mobile Participants Cards -->
                <div class="block md:hidden divide-y divide-slate-100 p-3">
                    @forelse($session->attempts as $attempt)
                    <div class="p-3.5 rounded-lg hover:bg-slate-50 transition">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-mono font-bold text-slate-400">#{{ $loop->iteration }}</span>
                                    <span class="font-bold text-sm text-slate-900">{{ $attempt->user->name }}</span>
                                </div>
                                <span class="text-[11px] text-slate-500 font-mono tabular-nums">Mulai: {{ $attempt->started_at?->format('H:i:s') ?? '-' }} WIB</span>
                            </div>
                            <span @class([
                                'px-2 py-0.5 text-xs font-bold rounded-md border shrink-0',
                                'bg-emerald-50 text-emerald-800 border-emerald-200' => $attempt->status === 'in_progress',
                                'bg-brand-50 text-brand-800 border-brand-200' => in_array($attempt->status, ['submitted', 'auto_submitted']),
                                'bg-slate-100 text-slate-700 border-slate-200' => !in_array($attempt->status, ['in_progress', 'submitted', 'auto_submitted']),
                            ])>
                                {{ $attempt->status === 'in_progress' ? 'Mengerjakan' : ($attempt->status === 'submitted' ? 'Selesai' : $attempt->status) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 mt-2 pt-2 border-t border-slate-100">
                            <span class="text-xs font-medium text-slate-500">Progres:</span>
                            <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                <div class="bg-brand-700 h-full rounded-full" style="width: {{ $attempt->getProgressPercentage() }}%"></div>
                            </div>
                            <span class="text-xs font-mono font-bold text-slate-700 tabular-nums">{{ round($attempt->getProgressPercentage()) }}%</span>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-slate-400 text-xs">Belum ada peserta yang memulai sesi ini.</div>
                    @endforelse
                </div>

                <!-- Desktop Participants Table -->
                <div class="hidden md:block p-2 sm:p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-4 py-3 text-left">No</th>
                                    <th class="px-4 py-3 text-left">Nama Peserta</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Progres</th>
                                    <th class="px-4 py-3 text-left">Mulai Pengerjaan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($session->attempts as $attempt)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-slate-400 font-mono text-xs tabular-nums">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $attempt->user->name }}</td>
                                    <td class="px-4 py-3">
                                        <span @class([
                                            'px-2.5 py-0.5 text-xs font-bold rounded-md border',
                                            'bg-emerald-50 text-emerald-800 border-emerald-200' => $attempt->status === 'in_progress',
                                            'bg-brand-50 text-brand-800 border-brand-200' => in_array($attempt->status, ['submitted', 'auto_submitted']),
                                            'bg-slate-100 text-slate-700 border-slate-200' => !in_array($attempt->status, ['in_progress', 'submitted', 'auto_submitted']),
                                        ])>{{ $attempt->status }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-28 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                                <div class="bg-brand-700 h-full rounded-full" style="width: {{ $attempt->getProgressPercentage() }}%"></div>
                                            </div>
                                            <span class="text-xs font-mono font-bold text-slate-600 tabular-nums">{{ round($attempt->getProgressPercentage()) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-600 tabular-nums">{{ $attempt->started_at?->format('H:i:s') ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-slate-400 text-xs">Belum ada peserta yang memulai sesi ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</x-layouts.app>
