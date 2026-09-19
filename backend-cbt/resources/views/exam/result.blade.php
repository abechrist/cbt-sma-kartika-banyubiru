<x-layouts.app :title="'Hasil Capaian Ujian'">
    <div class="max-w-4xl mx-auto py-8 sm:py-12 px-2">
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200/90 overflow-hidden transition-all duration-300">
            <!-- Institutional Header (Modern Radial Gradient) -->
            <div class="relative overflow-hidden p-8 sm:p-10 text-center text-white" style="background: radial-gradient(circle at 50% 10%, #12472e 0%, #061d13 70%, #020b07 100%);">
                <div class="absolute -top-16 -right-16 w-48 h-48 bg-gold-400/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-black bg-brand-900/90 text-gold-300 border border-brand-700/80 mb-3 shadow-2xs backdrop-blur-md">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>Ujian Selesai Dikerjakan</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight">
                        Hasil Penilaian Ujian
                    </h1>
                    <p class="text-base font-bold text-gold-300 mt-2">{{ $attempt->session->exam->name }}</p>
                    <p class="text-xs text-brand-200/80 mt-1 font-medium">{{ $attempt->session->name }} • {{ $attempt->user->name }} ({{ $attempt->user->nisn ?? 'Peserta' }})</p>
                </div>
            </div>

            @if($attempt->result)
            <div class="p-6 sm:p-10">
                <!-- Score Showcase Card (Clean Modern Gauge Card) -->
                <div class="text-center py-8 px-6 bg-gradient-to-b from-slate-50 to-white rounded-3xl border border-slate-200/90 mb-8 shadow-xs">
                    <div class="text-6xl sm:text-8xl font-black tracking-tight font-mono tabular-nums
                        @if($attempt->result->percentage >= 75) text-emerald-600
                        @elseif($attempt->result->percentage >= 50) text-amber-600
                        @else text-rose-600
                        @endif
                    ">
                        {{ number_format($attempt->result->percentage, 1) }}%
                    </div>
                    <div class="text-xs font-black uppercase tracking-widest text-slate-500 mt-3">Nilai Akhir Capaian</div>
                    <div class="text-sm sm:text-base font-semibold text-slate-700 mt-1.5">
                        Perolehan: <span class="text-slate-900 font-bold font-mono">{{ $attempt->result->total_score }}</span> dari maksimum <span class="text-slate-900 font-bold font-mono">{{ $attempt->result->max_possible_score }}</span> poin
                    </div>
                </div>

                <!-- Stats Trio Bento -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <div class="text-center p-5 bg-emerald-50/80 border border-emerald-200/90 rounded-2xl">
                        <p class="text-3xl sm:text-4xl font-black font-mono text-emerald-700 tabular-nums">{{ $attempt->result->correct_count }}</p>
                        <p class="text-xs font-extrabold text-emerald-900 mt-1.5">Jawaban Benar</p>
                    </div>
                    <div class="text-center p-5 bg-rose-50/80 border border-rose-200/90 rounded-2xl">
                        <p class="text-3xl sm:text-4xl font-black font-mono text-rose-700 tabular-nums">{{ $attempt->result->incorrect_count }}</p>
                        <p class="text-xs font-extrabold text-rose-900 mt-1.5">Jawaban Salah</p>
                    </div>
                    <div class="text-center p-5 bg-slate-100/90 border border-slate-200 rounded-2xl">
                        <p class="text-3xl sm:text-4xl font-black font-mono text-slate-700 tabular-nums">{{ $attempt->result->unanswered_count }}</p>
                        <p class="text-xs font-extrabold text-slate-800 mt-1.5">Tidak Dijawab</p>
                    </div>
                </div>

                <!-- Session Metadata Breakdown -->
                <div class="bg-slate-50/90 rounded-2xl p-6 border border-slate-200/80 mb-8">
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-500 mb-4">Informasi Pelaksanaan Ujian</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="flex justify-between py-2 border-b border-slate-200/70">
                            <span class="text-slate-500 font-medium">Status Pengerjaan:</span>
                            <span class="font-bold text-slate-900">{{ $attempt->status === 'auto_submitted' ? 'Otomatis (Waktu Habis)' : 'Tuntas Mandiri' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-200/70">
                            <span class="text-slate-500 font-medium">Waktu Mulai:</span>
                            <span class="font-bold text-slate-900 font-mono">{{ $attempt->started_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-200/70 sm:border-b-0">
                            <span class="text-slate-500 font-medium">Waktu Selesai:</span>
                            <span class="font-bold text-slate-900 font-mono">{{ $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i:s') : '-' }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-slate-500 font-medium">Durasi Pengerjaan:</span>
                            <span class="font-bold text-slate-900">{{ $attempt->started_at && $attempt->submitted_at ? $attempt->started_at->diffForHumans($attempt->submitted_at, true) : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Detailed Answer Breakdown if enabled -->
                @if($attempt->session->exam->show_result_after)
                <div class="border-t border-slate-200/80 pt-8">
                    <h3 class="font-black text-slate-900 text-base tracking-tight mb-5">Rincian Evaluasi Jawaban</h3>
                    <div class="space-y-3.5">
                        @foreach($attempt->answers as $answer)
                        <div class="p-5 rounded-2xl border transition
                            @if($answer->score > 0) bg-emerald-50/40 border-emerald-200/90
                            @elseif($answer->answer_text || count($answer->selected_options ?? []) > 0) bg-rose-50/40 border-rose-200/90
                            @else bg-slate-50 border-slate-200
                            @endif
                        ">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs px-2.5 py-0.5 rounded-lg bg-white border border-slate-200 text-slate-800 shadow-2xs">
                                            Soal #{{ $answer->question_id }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">{{ Str::limit(strip_tags($answer->question->question_text), 120) }}</p>
                                </div>
                                <span class="shrink-0 px-3 py-1 text-xs font-bold rounded-xl
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
            <div class="p-14 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-black text-slate-900">Hasil Penilaian Sedang Diproses</h3>
                <p class="text-xs text-slate-500 mt-1.5 max-w-sm mx-auto leading-relaxed">Jawaban Anda telah tersimpan dengan aman di server lab. Penilaian akhir akan dirilis setelah guru menyelesaikan proses koreksi.</p>
            </div>
            @endif

            <!-- Action Bar -->
            <div class="p-6 bg-slate-50/90 border-t border-slate-200/80 flex justify-center">
                <a href="{{ route('dashboard') }}" 
                    class="px-7 py-3 rounded-2xl bg-brand-900 hover:bg-brand-950 text-white font-extrabold text-sm transition-all duration-200 shadow-md hover:shadow-lg btn-glow-brand flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>