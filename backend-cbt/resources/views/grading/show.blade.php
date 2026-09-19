<x-layouts.app :title="'Detail Hasil Koreksi Siswa'">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('grading.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Antrean Koreksi
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Hasil Koreksi Nilai Siswa</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Rincian lembar jawaban esai dan skor akhir pengerjaan ujian.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('grading.grade', $result) }}" 
                    class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs transition {{ $result->grading_status === 'completed' ? 'opacity-60' : '' }}">
                    {{ $result->grading_status === 'completed' ? 'Edit Koreksi' : 'Koreksi Esai' }}
                </a>
                <form action="{{ route('grading.regrade', $result) }}" method="POST" onsubmit="return confirm('Hitung ulang seluruh nilai otomatis dan bobot esai?')">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs border border-slate-200 transition">
                        Hitung Ulang
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Student Meta Header -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-6">
            <div class="p-6 bg-slate-50/80 border-b border-slate-200">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-1">Peserta Didik</span>
                        <p class="font-bold text-sm text-slate-900">{{ $result->attempt->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Paket Ujian</span>
                        <p class="font-semibold text-slate-800">{{ $result->attempt->session->exam->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Nilai Akhir Capaian</span>
                        <p class="font-mono font-extrabold text-base text-brand-800">{{ number_format((float) $result->percentage, 1) }}%</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Status Penilaian</span>
                        @if($result->grading_status === 'completed')
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 inline-block">Selesai</span>
                        @else
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200 inline-block">Perlu Koreksi</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Answers List -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                <h3 class="font-bold text-slate-900 text-sm tracking-tight">Evaluasi Jawaban Esai</h3>
            </div>
            @php($esai = $result->attempt->answers->filter(fn($a) => $a->question?->type === 'esai'))
            @if($esai->isEmpty())
                <p class="p-8 text-slate-400 text-center text-sm">Tidak ada jawaban esai pada ujian ini.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($esai as $answer)
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-slate-100 text-slate-700">Soal #{{ $answer->question_id }}</span>
                        </div>
                        <p class="text-slate-900 text-sm font-semibold mb-3 leading-relaxed">{{ $answer->question->question_text }}</p>
                        
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 mb-4">
                            <span class="text-[11px] font-bold uppercase text-slate-400 block mb-1">Jawaban Siswa:</span>
                            <p class="whitespace-pre-wrap text-sm text-slate-800">{{ $answer->answer_text ?: '(tidak ada jawaban)' }}</p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 bg-emerald-50/60 rounded-xl border border-emerald-200 text-xs">
                            <div>
                                <span class="text-slate-600">Perolehan Nilai:</span>
                                <span class="font-mono font-bold text-slate-900 text-sm ml-1">
                                    {{ $answer->score !== null ? $answer->score . ' / ' . $answer->question->score . ' pts' : 'Belum dinilai' }}
                                </span>
                                @if($answer->notes)
                                    <span class="text-slate-600 italic ml-2">— Catatan: "{{ $answer->notes }}"</span>
                                @endif
                            </div>
                            @if($answer->gradedBy)
                                <span class="text-[11px] text-slate-400 font-mono">
                                    Oleh {{ $answer->gradedBy->name }} ({{ $answer->graded_at?->format('d/m/Y H:i') }})
                                </span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>