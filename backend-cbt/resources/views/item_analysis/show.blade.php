<x-layouts.app :title="'Analisis Butir Soal - ' . $exam->name">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('item-analysis.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                ← Kembali ke Daftar Analisis
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Analisis Butir Soal: {{ $exam->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ $exam->subject->name ?? 'Mata Pelajaran Umum' }} • Dihitung dari sampel {{ $totalAttempts }} hasil pengerjaan peserta</p>
        </div>
    </div>

    <!-- Filter Type -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5 mb-6">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-full sm:w-64">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Format Tipe Soal</label>
                <select name="question_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none">
                    <option value="">Semua Tipe Soal</option>
                    @foreach(['pg' => 'Pilihan Ganda', 'pg_kompleks' => 'PG Kompleks', 'benar_salah' => 'Benar/Salah', 'isian_singkat' => 'Isian Singkat', 'menjodohkan' => 'Menjodohkan', 'esai' => 'Esai'] as $key => $label)
                        <option value="{{ $key }}" {{ $questionFilter == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs transition">
                    Filter
                </button>
                <a href="{{ route('item-analysis.show', $exam) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Matrix Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-5 py-3.5 text-left">Konten Soal</th>
                        <th class="px-5 py-3.5 text-left">Tipe</th>
                        <th class="px-5 py-3.5 text-center text-emerald-700">Benar</th>
                        <th class="px-5 py-3.5 text-center text-rose-700">Salah</th>
                        <th class="px-5 py-3.5 text-center text-slate-500">Kosong</th>
                        <th class="px-5 py-3.5 text-left">Tingkat Kesukaran (p)</th>
                        <th class="px-5 py-3.5 text-left">Daya Beda (d)</th>
                        <th class="px-5 py-3.5 text-left">Rekomendasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($analysis as $row)
                    @php
                        $q = $row['question'];
                        $p = $row['difficulty'];
                        $d = $row['discrimination'];
                        $diffLabel = $p >= 0.70 ? 'Mudah' : ($p >= 0.30 ? 'Sedang' : 'Sukar');
                        $diffColor = $p >= 0.70 ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($p >= 0.30 ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-rose-100 text-rose-800 border-rose-200');
                        $diffDisc = $d >= 0.40 ? 'Sangat Baik' : ($d >= 0.30 ? 'Baik' : ($d >= 0.20 ? 'Cukup' : ($d >= 0.10 ? 'Kurang' : 'Buruk')));
                        $discColor = $d >= 0.30 ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : ($d >= 0.20 ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-rose-100 text-rose-800 border-rose-200');
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-4 max-w-xs font-medium text-slate-900 line-clamp-2">
                            {{ \Illuminate\Support\Str::limit(strip_tags($q->question_text), 70) }}
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $q->type }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center font-mono font-bold text-emerald-700">{{ $row['correct'] }}</td>
                        <td class="px-5 py-4 text-center font-mono font-bold text-rose-600">{{ $row['incorrect'] }}</td>
                        <td class="px-5 py-4 text-center font-mono text-slate-400">{{ $row['unanswered'] }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold text-slate-900 text-xs">{{ number_format($p * 100, 1) }}%</span>
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full border {{ $diffColor }}">{{ $diffLabel }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold text-slate-900 text-xs">{{ number_format($d, 2) }}</span>
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full border {{ $discColor }}">{{ $diffDisc }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($row['correct'] == 0 && $row['total_answers'] > 0)
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                    Perlu Perbaikan
                                </span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-5 py-10 text-center text-slate-400 text-sm">Tidak ada butir soal yang sesuai filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>