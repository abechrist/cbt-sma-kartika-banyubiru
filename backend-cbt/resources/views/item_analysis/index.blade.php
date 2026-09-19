<x-layouts.app :title="'Analisis Butir Soal'">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Analisis Butir Soal (Psikometri)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Evaluasi parameter daya beda, tingkat kesukaran, dan efektivitas butir soal ujian.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3.5 text-left">Paket Ujian</th>
                        <th class="px-6 py-3.5 text-left">Mata Pelajaran</th>
                        <th class="px-6 py-3.5 text-left">Jumlah Soal</th>
                        <th class="px-6 py-3.5 text-left">Sesi Dilaksanakan</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($exams as $exam)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $exam->name }}</td>
                        <td class="px-6 py-4 text-slate-600 font-medium">{{ $exam->subject->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-mono font-bold text-slate-800">{{ $exam->questions_count ?? $exam->questions->count() }} butir</td>
                        <td class="px-6 py-4 font-mono text-xs text-slate-600">{{ $exam->sessions->count() }} sesi</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('item-analysis.show', $exam) }}" class="px-3.5 py-1.5 rounded-xl bg-gold-50 hover:bg-gold-100 text-gold-800 font-bold text-xs border border-gold-200 shadow-xs transition">
                                Buka Analisis Soal →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400 text-sm">Belum ada paket ujian untuk dianalisis.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>