<x-layouts.app :title="'Hasil Capaian Ujian'">
    <div class="max-w-4xl mx-auto py-4 sm:py-8 px-3 sm:px-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden">
            <!-- Institutional Result Header -->
            <div class="bg-brand-950 p-6 sm:p-8 text-center text-white border-b border-brand-900">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-white tracking-tight leading-tight">
                    Hasil Penilaian Ujian
                </h1>
                <p class="text-sm sm:text-base font-bold text-gold-300 mt-1.5">{{ $attempt->session->exam->name }}</p>
                <p class="text-xs text-brand-200 mt-1 font-medium">{{ $attempt->session->name }} • {{ $attempt->user->name }} ({{ $attempt->user->nisn ?? 'Peserta' }})</p>
            </div>

            @if($attempt->result)
            <div class="p-6 sm:p-8">
                <!-- Score Showcase Card -->
                <div class="text-center py-6 px-5 bg-slate-50 rounded-xl border border-slate-200 mb-6">
                    <div class="text-5xl sm:text-7xl font-black tracking-tight font-mono tabular-nums
                        @if($attempt->result->percentage >= 75) text-emerald-600
                        @elseif($attempt->result->percentage >= 50) text-amber-600
                        @else text-rose-600
                        @endif
                    ">
                        {{ number_format($attempt->result->percentage, 1) }}%
                    </div>
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500 mt-2.5">Nilai Akhir Capaian</div>
                    <div class="text-sm font-semibold text-slate-700 mt-1">
                        Perolehan: <span class="text-slate-900 font-bold font-mono tabular-nums">{{ $attempt->result->total_score }}</span> dari maksimum <span class="text-slate-900 font-bold font-mono tabular-nums">{{ $attempt->result->max_possible_score }}</span> poin
                    </div>
                </div>

                <!-- Stats Trio -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-6">
                    <div class="text-center p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                        <p class="text-2xl sm:text-3xl font-black font-mono text-emerald-700 tabular-nums">{{ $attempt->result->correct_count }}</p>
                        <p class="text-xs font-bold text-emerald-900 mt-1">Jawaban Benar</p>
                    </div>
                    <div class="text-center p-4 bg-rose-50 border border-rose-200 rounded-xl">
                        <p class="text-2xl sm:text-3xl font-black font-mono text-rose-700 tabular-nums">{{ $attempt->result->incorrect_count }}</p>
                        <p class="text-xs font-bold text-rose-900 mt-1">Jawaban Salah</p>
                    </div>
                    <div class="text-center p-4 bg-slate-100 border border-slate-200 rounded-xl">
                        <p class="text-2xl sm:text-3xl font-black font-mono text-slate-700 tabular-nums">{{ $attempt->result->unanswered_count }}</p>
                        <p class="text-xs font-bold text-slate-800 mt-1">Tidak Dijawab</p>
                    </div>
                </div>

                <!-- Session Metadata Breakdown -->
                <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 mb-6">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3.5">Informasi Pelaksanaan Ujian</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                        <div class="flex justify-between py-2 border-b border-slate-200/70">
                            <span class="text-slate-500 font-medium">Status Pengerjaan:</span>
                            <span class="font-bold text-slate-900">{{ $attempt->status === 'auto_submitted' ? 'Otomatis (Waktu Habis)' : 'Tuntas Mandiri' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-200/70">
                            <span class="text-slate-500 font-medium">Waktu Mulai:</span>
                            <span class="font-bold text-slate-900 font-mono tabular-nums">{{ $attempt->started_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-200/70 sm:border-b-0">
                            <span class="text-slate-500 font-medium">Waktu Selesai:</span>
                            <span class="font-bold text-slate-900 font-mono tabular-nums">{{ $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i:s') : '-' }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-slate-500 font-medium">Durasi Pengerjaan:</span>
                            <span class="font-bold text-slate-900">{{ $attempt->started_at && $attempt->submitted_at ? $attempt->started_at->diffForHumans($attempt->submitted_at, true) : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Detailed Answer Breakdown if enabled -->
                @if($attempt->session->exam->show_result_after)
                <div class="border-t border-slate-200 pt-6">
                    <h2 class="font-bold text-slate-900 text-base mb-4">Rincian Evaluasi Jawaban</h2>
                    <div class="space-y-3">
                        @foreach($attempt->answers as $answer)
                        <div class="p-4 rounded-xl border transition
                            @if($answer->score > 0) bg-emerald-50/40 border-emerald-200
                            @elseif($answer->answer_text || count($answer->selected_options ?? []) > 0) bg-rose-50/40 border-rose-200
                            @else bg-slate-50 border-slate-200
                            @endif
                        ">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs px-2.5 py-0.5 rounded-md bg-white border border-slate-200 text-slate-800">
                                            Soal #{{ $answer->question_id }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">{{ Str::limit(strip_tags($answer->question->question_text), 120) }}</p>
                                </div>
                                <span class="shrink-0 px-2.5 py-1 text-xs font-bold rounded-md
                                    @if($answer->score > 0) bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @elseif($answer->answer_text || count($answer->selected_options ?? []) > 0) bg-rose-100 text-rose-800 border border-rose-200
                                    @else bg-slate-200 text-slate-700
                                    @endif
                                ">
                                    @if($answer->score > 0) Benar (+{{ $answer->score }})
                                    @elseif($answer->answer_text || count($answer->selected_options ?? []) > 0) Salah
                                    @else Tidak Dijawab
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3.5">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-base font-bold text-slate-900">Hasil Penilaian Sedang Diproses</h2>
                <p class="text-xs text-slate-500 mt-1.5 max-w-sm mx-auto leading-relaxed">Jawaban Anda telah tersimpan dengan aman di sistem. Penilaian akhir akan dirilis setelah guru menyelesaikan proses koreksi.</p>
            </div>
            @endif

            <!-- Action Bar -->
            <div class="p-5 bg-slate-50 border-t border-slate-200 flex justify-center">
                <a href="{{ route('dashboard') }}" 
                    class="w-full sm:w-auto min-h-[42px] px-6 py-2.5 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
