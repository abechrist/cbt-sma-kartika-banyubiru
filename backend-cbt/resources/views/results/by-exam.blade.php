<x-layouts.app :title="'Rekapitulasi per Ujian - ' . $exam->name">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('results.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                ← Kembali ke Rekapitulasi Nilai
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $exam->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Analisis hasil dan sebaran nilai peserta per sesi pelaksanaan ujian.</p>
        </div>
    </div>

    <!-- Stats Banner -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 text-center">
            <p class="text-3xl font-extrabold text-brand-800 font-mono">{{ $stats['total_participants'] }}</p>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Total Peserta</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 text-center">
            <p class="text-3xl font-extrabold text-emerald-700 font-mono">{{ number_format($stats['average_score'], 1) }}%</p>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Rata-rata Nilai</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 text-center">
            <p class="text-3xl font-extrabold text-amber-600 font-mono">{{ number_format($stats['highest_score'], 1) }}%</p>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Nilai Tertinggi</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 text-center">
            <p class="text-3xl font-extrabold text-rose-600 font-mono">{{ number_format($stats['lowest_score'], 1) }}%</p>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Nilai Terendah</p>
        </div>
    </div>

    <!-- Sessions breakdown -->
    @foreach($sessions as $session)
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 mb-6 overflow-hidden">
        <div class="p-5 border-b border-slate-200 bg-slate-50/70 flex flex-wrap justify-between items-center gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-base">{{ $session->name }}</h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $session->start_at->format('d/m/Y H:i') }} - {{ $session->end_at->format('H:i') }}</p>
            </div>
            <span class="px-3 py-1 text-xs font-bold rounded-full border
                @switch($session->status)
                    @case('completed') bg-emerald-100 text-emerald-800 border-emerald-200 @break
                    @case('open') @case('in_progress') bg-amber-100 text-amber-800 border-amber-200 @break
                    @default bg-slate-100 text-slate-700 border-slate-200
                @endswitch
            ">{{ ucfirst($session->status) }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3.5 text-left">No</th>
                        <th class="px-6 py-3.5 text-left">Nama Peserta</th>
                        <th class="px-6 py-3.5 text-left">Status Ujian</th>
                        <th class="px-6 py-3.5 text-left">Nilai</th>
                        <th class="px-6 py-3.5 text-left">Benar / Salah</th>
                        <th class="px-6 py-3.5 text-left">Waktu Submit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($session->attempts as $attempt)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $attempt->user->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full border
                                @switch($attempt->status)
                                    @case('submitted') @case('auto_submitted') bg-emerald-100 text-emerald-800 border-emerald-200 @break
                                    @case('in_progress') bg-amber-100 text-amber-800 border-amber-200 @break
                                    @default bg-slate-100 text-slate-700 border-slate-200
                                @endswitch
                            ">{{ $attempt->status }}</span>
                        </td>
                        <td class="px-6 py-4 font-mono font-bold
                            @if($attempt->result?->percentage >= 75) text-emerald-700
                            @elseif($attempt->result?->percentage >= 50) text-amber-600
                            @else text-rose-600
                            @endif
                        ">{{ $attempt->result ? number_format($attempt->result->percentage, 1) . '%' : '-' }}</td>
                        <td class="px-6 py-4 font-mono text-xs">
                            <span class="text-emerald-700 font-bold">{{ $attempt->result?->correct_count ?? 0 }}</span> /
                            <span class="text-rose-600 font-bold">{{ $attempt->result?->incorrect_count ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $attempt->submitted_at?->format('H:i') ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada peserta yang menyelesaikan sesi ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</x-layouts.app>