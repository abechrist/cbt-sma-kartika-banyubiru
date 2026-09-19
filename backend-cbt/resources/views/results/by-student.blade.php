<x-layouts.app :title="'Riwayat Ujian - ' . $student->name">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('results.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                ← Kembali ke Rekapitulasi Nilai
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Riwayat Ujian: {{ $student->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar seluruh keikutsertaan ujian dan rekam jejak capaian akademis peserta didik.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3.5 text-left">No</th>
                        <th class="px-6 py-3.5 text-left">Paket Ujian</th>
                        <th class="px-6 py-3.5 text-left">Mata Pelajaran</th>
                        <th class="px-6 py-3.5 text-left">Tanggal</th>
                        <th class="px-6 py-3.5 text-left">Nilai</th>
                        <th class="px-6 py-3.5 text-left">Status</th>
                        <th class="px-6 py-3.5 text-right">Rincian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attempts as $attempt)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $attempt->session->exam->name }}</td>
                        <td class="px-6 py-4 text-xs font-semibold text-slate-700">{{ $attempt->session->exam->subject->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $attempt->started_at?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-6 py-4 font-mono font-bold
                            @if($attempt->result?->percentage >= 75) text-emerald-700
                            @elseif($attempt->result?->percentage >= 50) text-amber-600
                            @else text-rose-600
                            @endif
                        ">{{ $attempt->result ? number_format($attempt->result->percentage, 1) . '%' : '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full border
                                @switch($attempt->status)
                                    @case('submitted') @case('auto_submitted') bg-emerald-100 text-emerald-800 border-emerald-200 @break
                                    @case('in_progress') bg-amber-100 text-amber-800 border-amber-200 @break
                                    @default bg-slate-100 text-slate-700 border-slate-200
                                @endswitch
                            ">{{ $attempt->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($attempt->result)
                                <a href="{{ route('results.show', $attempt->result) }}" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                                    Lembar Hasil →
                                </a>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada riwayat ujian untuk siswa ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>