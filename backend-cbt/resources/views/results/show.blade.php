<x-layouts.app :title="'Detail Capaian Siswa'">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('results.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Rekapitulasi Nilai
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Hasil Ujian Siswa</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Rincian perolehan poin dan keabsahan lembar pengerjaan peserta.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-6">
            <!-- Header Meta -->
            <div class="p-6 border-b border-slate-200 bg-slate-50/80">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-1">Peserta Didik</span>
                        <p class="font-bold text-sm text-slate-900">{{ $result->attempt->user->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kelas / Rombel</span>
                        <p class="font-semibold text-slate-800">{{ $result->attempt->user->class->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Paket Ujian</span>
                        <p class="font-semibold text-slate-800">{{ $result->attempt->session->exam->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Waktu Selesai</span>
                        <p class="font-mono text-slate-700">{{ $result->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                <!-- Score showcase -->
                <div class="text-center mb-8 p-6 bg-radial from-slate-50 to-white rounded-2xl border border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Nilai Capaian</span>
                    <div class="text-6xl font-black tracking-tight my-2
                        @if($result->percentage >= 75) text-emerald-700
                        @elseif($result->percentage >= 50) text-amber-600
                        @else text-rose-600
                        @endif
                    ">
                        {{ number_format($result->percentage, 1) }}%
                    </div>
                    <p class="text-xs font-mono font-bold text-slate-500">Perolehan: {{ $result->total_score }} dari {{ $result->max_possible_score }} Poin Maksimal</p>
                </div>

                <!-- Stats Trio -->
                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="text-center p-4 bg-emerald-50/80 border border-emerald-200 rounded-xl">
                        <p class="text-3xl font-extrabold text-emerald-800">{{ $result->correct_count }}</p>
                        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider mt-1">Jawaban Benar</p>
                    </div>
                    <div class="text-center p-4 bg-rose-50/80 border border-rose-200 rounded-xl">
                        <p class="text-3xl font-extrabold text-rose-700">{{ $result->incorrect_count }}</p>
                        <p class="text-xs font-bold text-rose-700 uppercase tracking-wider mt-1">Jawaban Salah</p>
                    </div>
                    <div class="text-center p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <p class="text-3xl font-extrabold text-slate-600">{{ $result->unanswered_count }}</p>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mt-1">Tidak Dijawab</p>
                    </div>
                </div>

                <!-- Answers Breakout -->
                <div class="border-t border-slate-200 pt-6">
                    <h3 class="font-bold text-slate-900 text-sm uppercase tracking-wider mb-4">Rincian Lembar Respon Siswa</h3>
                    <div class="space-y-4">
                        @foreach($result->attempt->answers->sortBy('question_id') as $answer)
                        <div class="p-4 rounded-xl border transition
                            @if($answer->score > 0) bg-emerald-50/40 border-emerald-200
                            @elseif($answer->answer_text || count($answer->selected_options ?? []) > 0) bg-rose-50/40 border-rose-200
                            @else bg-slate-50/60 border-slate-200
                            @endif
                        ">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-bold text-xs px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-800">
                                            Soal #{{ $answer->question_id }}
                                        </span>
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full
                                            @if($answer->score > 0) bg-emerald-100 text-emerald-800 border border-emerald-200
                                            @elseif($answer->answer_text || count($answer->selected_options ?? []) > 0) bg-rose-100 text-rose-800 border border-rose-200
                                            @else bg-slate-100 text-slate-700 border border-slate-200
                                            @endif
                                        ">
                                            @if($answer->score > 0) Benar (+{{ $answer->score }} pts)
                                            @elseif($answer->answer_text || count($answer->selected_options ?? []) > 0) Jawaban Salah
                                            @else Tidak Menjawab
                                            @endif
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-800 leading-relaxed">{{ Str::limit($answer->question->question_text, 140) }}</p>
                                    @if($answer->answer_text)
                                        <div class="mt-2 p-2.5 bg-white rounded-lg border border-slate-200 text-xs text-slate-700">
                                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Jawaban Esai / Teks:</span>
                                            {{ $answer->answer_text }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>