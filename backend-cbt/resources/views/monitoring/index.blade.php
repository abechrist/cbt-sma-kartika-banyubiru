<x-layouts.app :title="'Pusat Monitoring Ujian'">
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                Pengawasan Lab Komputer Real-Time
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Monitoring Ujian Berjalan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pantau aktivitas, konektivitas, dan progress pengerjaan seluruh peserta ujian.</p>
        </div>
    </div>

    @if($sessions->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-xs">
            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Tidak Ada Sesi yang Sedang Berlangsung</h3>
            <p class="text-xs text-slate-500 mt-1">Saat ini belum ada jadwal sesi ujian dengan status terbuka atau sedang berjalan.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($sessions as $session)
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden hover:border-brand-600 transition">
                <!-- Session Header Banner -->
                <div class="p-5 sm:p-6 border-b border-slate-200 bg-slate-50/70 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-brand-800 text-gold-400 flex items-center justify-center shrink-0 font-bold text-lg shadow-xs">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="font-bold text-lg text-slate-900 tracking-tight">{{ $session->name }}</h2>
                                <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    AKTIF
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 mt-0.5">
                                {{ $session->exam->name }} • Ruang: <span class="font-semibold text-slate-800">{{ $session->room ?? 'Lab Komputer' }}</span> • Token: <span class="font-mono font-bold text-gold-700">{{ $session->tokens->first()?->token ?? '-' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 sm:gap-6">
                        <div class="text-center">
                            <p class="text-2xl font-extrabold text-emerald-600 font-mono leading-none">{{ $session->attempts->where('status', 'in_progress')->count() }}</p>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mt-1">Mengerjakan</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-extrabold text-brand-800 font-mono leading-none">{{ $session->attempts->whereIn('status', ['submitted', 'auto_submitted'])->count() }}</p>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mt-1">Selesai</p>
                        </div>
                        <a href="{{ route('monitoring.session', $session) }}" 
                            class="px-4 py-2.5 rounded-xl bg-gold-500 hover:bg-gold-600 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                            Monitor Real-Time →
                        </a>
                    </div>
                </div>

                <!-- Participants Preview Table -->
                <div class="p-2 sm:p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                <tr>
                                    <th class="px-4 py-3 text-left">No</th>
                                    <th class="px-4 py-3 text-left">Nama Peserta</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Progress</th>
                                    <th class="px-4 py-3 text-left">Mulai Pengerjaan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($session->attempts as $attempt)
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-4 py-3 text-slate-400 font-mono text-xs">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $attempt->user->name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border
                                            @switch($attempt->status)
                                                @case('in_progress') bg-emerald-100 text-emerald-800 border-emerald-200 @break
                                                @case('submitted') @case('auto_submitted') bg-brand-100 text-brand-800 border-brand-200 @break
                                                @default bg-slate-100 text-slate-700 border-slate-200
                                            @endswitch
                                        ">{{ $attempt->status }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-28 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                                <div class="bg-brand-700 h-full rounded-full" style="width: {{ $attempt->getProgressPercentage() }}%"></div>
                                            </div>
                                            <span class="text-xs font-mono font-bold text-slate-600">{{ round($attempt->getProgressPercentage()) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $attempt->started_at?->format('H:i:s') ?? '-' }}</td>
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