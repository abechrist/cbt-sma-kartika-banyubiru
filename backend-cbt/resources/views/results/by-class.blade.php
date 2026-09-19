<x-layouts.app :title="'Rekapitulasi Kelas - ' . $class->name">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('results.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                ← Kembali ke Rekapitulasi Nilai
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Rekapitulasi Nilai Kelas {{ $class->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Rangkuman capaian rata-rata seluruh peserta didik pada rombel terpilih.</p>
        </div>
    </div>

    <!-- Students Summary Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-6">
        <div class="p-5 border-b border-slate-200 bg-slate-50/70">
            <h3 class="font-bold text-slate-900 text-sm tracking-tight">Ringkasan Capaian Siswa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3.5 text-left">No</th>
                        <th class="px-6 py-3.5 text-left">Nama Siswa</th>
                        <th class="px-6 py-3.5 text-left">Total Ujian Ditempuh</th>
                        <th class="px-6 py-3.5 text-left">Rata-rata Nilai</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($students as $student)
                    @php
                        $studentResults = $results->where('attempt.user_id', $student->id);
                        $avgScore = $studentResults->count() > 0 ? $studentResults->avg(fn($r) => $r->percentage) : 0;
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $student->name }}</td>
                        <td class="px-6 py-4 font-mono font-semibold text-slate-700">{{ $studentResults->count() }} paket</td>
                        <td class="px-6 py-4 font-mono font-bold
                            @if($avgScore >= 75) text-emerald-700
                            @elseif($avgScore >= 50) text-amber-600
                            @else text-rose-600
                            @endif
                        ">{{ number_format($avgScore, 1) }}%</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('results.by-student', $student) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                                Rapor Siswa →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada siswa di kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>