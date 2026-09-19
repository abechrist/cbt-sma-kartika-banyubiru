<x-layouts.app :title="'Rekapitulasi Nilai Akhir'">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Rekapitulasi Capaian Nilai</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Laporan komprehensif hasil evaluasi dan capaian seluruh peserta ujian CBT.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('export.rekap_nilai') }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Export Nilai CSV
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-60">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Paket Ujian</label>
                <select name="exam_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none">
                    <option value="">Semua Paket Ujian</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-44">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Kelas / Rombel</label>
                <select name="class_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Cari Peserta Didik</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama siswa..."
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs transition">
                    Filter
                </button>
                <a href="{{ route('results.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3.5 text-left">No</th>
                        <th class="px-6 py-3.5 text-left">Nama Siswa</th>
                        <th class="px-6 py-3.5 text-left">Kelas</th>
                        <th class="px-6 py-3.5 text-left">Paket Ujian</th>
                        <th class="px-6 py-3.5 text-left">Nilai Akhir</th>
                        <th class="px-6 py-3.5 text-left">Benar / Salah</th>
                        <th class="px-6 py-3.5 text-left">Waktu Selesai</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($results as $result)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $result->attempt->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-xs font-semibold text-slate-700">{{ $result->attempt->user->class->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-700">{{ $result->attempt->session->exam->name ?? '-' }}</td>
                        <td class="px-6 py-4 font-mono font-extrabold text-base
                            @if($result->percentage >= 75) text-emerald-700
                            @elseif($result->percentage >= 50) text-amber-600
                            @else text-rose-600
                            @endif
                        ">
                            {{ number_format($result->percentage, 1) }}%
                        </td>
                        <td class="px-6 py-4 font-mono text-xs">
                            <span class="text-emerald-700 font-bold">{{ $result->correct_count }}B</span> /
                            <span class="text-rose-600 font-bold">{{ $result->incorrect_count }}S</span>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $result->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('results.show', $result) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                                Rincian →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-slate-400 text-sm">Belum ada data nilai yang tersimpan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $results->links() }}
        </div>
    </div>
</x-layouts.app>