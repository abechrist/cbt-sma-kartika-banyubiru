<x-layouts.app :title="'Monitoring - ' . $session->name">
    <!-- Top Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('monitoring.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar Monitoring
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Monitoring: {{ $session->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                {{ $session->exam->name }} • Ruang: <span class="font-semibold text-slate-800">{{ $session->room ?? 'Lab Komputer' }}</span> • Token: <span class="font-mono font-bold text-gold-700">{{ $session->tokens->first()?->token ?? '-' }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span id="liveBadge" class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1.5 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                PENGAWASAN LIVE
            </span>
        </div>
    </div>

    <!-- React Real-Time Container -->
    <div id="monitorRoot"></div>

    <script>
        window.__MONITOR_DATA__ = @json($participants);
        window.__SESSION_ID__ = {{ $session->id }};
        window.__EXAM_QUESTIONS_COUNT__ = {{ $session->exam->questions()->count() }};
        window.__CSRF__ = @json(csrf_token());
        window.__REVERB__ = @json($reverbConfig);
        window.__PUSHER_APP_KEY__ = @json($pusherKey ?? null);
        window.__PUSHER_APP_CLUSTER__ = @json(config('broadcasting.connections.pusher.options.cluster', 'ap1'));
    </script>

    @vite(['resources/js/monitoring.jsx'])

    <!-- Fallback if JS Disabled -->
    <noscript>
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center gap-6">
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-slate-900 font-mono">{{ $participants->count() }}</p>
                        <p class="text-xs text-slate-500">Total Peserta</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-emerald-600 font-mono">{{ $participants->where('status', 'in_progress')->count() }}</p>
                        <p class="text-xs text-slate-500">Mengerjakan</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-extrabold text-brand-800 font-mono">{{ $participants->whereIn('status', ['submitted', 'auto_submitted'])->count() }}</p>
                        <p class="text-xs text-slate-500">Selesai</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-3.5 text-left">No</th>
                            <th class="px-6 py-3.5 text-left">Nama Siswa</th>
                            <th class="px-6 py-3.5 text-left">Status</th>
                            <th class="px-6 py-3.5 text-left">Progress</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($participants as $participant)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-6 py-4 font-mono text-xs">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                {{ $participant['student_name'] }}
                                @if(($participant['suspicious_flags'] ?? 0) > 0)
                                    <span class="ml-2 px-2 py-0.5 text-xs bg-rose-100 text-rose-800 border border-rose-200 rounded-full font-bold">
                                        ⚠ {{ $participant['suspicious_flags'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-slate-100 text-slate-800">
                                    {{ $participant['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-28 bg-slate-100 rounded-full h-2 overflow-hidden border">
                                    <div class="bg-brand-700 h-full rounded-full" style="width: {{ $participant['progress'] }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('kiosk.show', $participant['attempt_id']) }}" target="_blank" class="text-gold-700 hover:text-gold-900 font-semibold text-xs">Kiosk</a>
                                    @if($participant['status'] !== 'not_started')
                                    <form action="{{ route('monitoring.reset', $participant['attempt_id']) }}" method="POST" onsubmit="return confirm('Reset percobaan ini?')">
                                        @csrf
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-xs">Reset</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400 text-xs">Belum ada peserta.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </noscript>

    <!-- Activity and Incident Logs Section -->
    <div class="mt-8 bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50/70 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-sm tracking-tight">Log Kejadian &amp; Aktivitas Peserta</h3>
                <p class="text-xs text-slate-500">Pencatatan pelanggaran tab, blur jendela, dan re-koneksi jaringan</p>
            </div>
            <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 border border-slate-200">
                {{ $activityLogs->count() }} log terakhir
            </span>
        </div>
        <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto text-sm">
            @forelse($activityLogs as $log)
            <div class="px-5 py-3 flex items-start gap-3 hover:bg-slate-50/60 transition">
                <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $log->action === 'suspicious_activity' ? 'bg-rose-500 ring-2 ring-rose-300' : 'bg-brand-600' }}"></span>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-900 text-xs">{{ $log->attempt?->user?->name ?? 'Sistem' }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md uppercase font-mono tracking-wider @if($log->action === 'suspicious_activity') bg-rose-100 text-rose-800 @else bg-slate-100 text-slate-700 @endif">
                            {{ $log->action }}
                        </span>
                        <span class="text-[11px] font-mono text-slate-400 ml-auto">{{ $log->created_at?->format('H:i:s') }} WIB</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1">{{ $log->description }}</p>
                </div>
            </div>
            @empty
            <p class="px-5 py-8 text-center text-slate-400 text-xs">Belum ada kejadian mencurigakan yang tercatat.</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>