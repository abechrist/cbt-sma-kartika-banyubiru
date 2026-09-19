<x-layouts.app :title="'Koreksi Esai Manual'">
    <div class="max-w-5xl mx-auto">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Koreksi Jawaban Esai</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Antrean pemeriksaan dan penilaian manual butir soal esai oleh dewan guru.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5 mb-6">
            <form method="GET" action="{{ route('grading.index') }}" class="flex flex-wrap gap-3 items-end">
                <div class="w-full sm:w-64">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Filter Paket Ujian</label>
                    <select name="exam_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none">
                        <option value="">Semua Paket Ujian</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
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
                    <a href="{{ route('grading.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            @if($results->count() === 0)
                <div class="p-12 text-center text-slate-400">
                    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <p class="font-bold text-slate-800 text-base">Antrean Bersih</p>
                    <p class="text-xs text-slate-500 mt-1">Seluruh jawaban esai telah selesai dinilai atau tidak ada ujian yang memerlukan koreksi manual.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Nama Siswa</th>
                                <th class="px-6 py-3.5 text-left">Paket Ujian & Sesi</th>
                                <th class="px-6 py-3.5 text-left">Skor Sementara</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($results as $result)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $result->attempt->user->name }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $result->attempt->user->nisn ?? 'NISN -' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800">{{ $result->attempt->session->exam->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $result->attempt->session->name }}</div>
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-amber-700">
                                    {{ number_format((float) $result->percentage, 1) }}%
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('grading.grade', $result) }}" 
                                        class="px-4 py-1.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs transition">
                                        Koreksi Esai →
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                    {{ $results->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>