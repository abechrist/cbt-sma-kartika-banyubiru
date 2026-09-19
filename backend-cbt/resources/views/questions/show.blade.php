<x-layouts.app :title="'Detail Butir Soal #' . $question->id">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('questions.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Bank Soal
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Butir Soal #{{ $question->id }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pratinjau tampilan soal dan kunci jawaban yang terdaftar di bank soal.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('questions.edit', $question) }}" class="px-4 py-2 rounded-xl bg-gold-500 hover:bg-gold-600 text-white font-bold text-xs shadow-xs transition">
                    Edit Soal
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <!-- Badges bar -->
            <div class="p-5 border-b border-slate-200 bg-slate-50/80 flex flex-wrap items-center gap-2.5">
                <span class="px-3 py-1 text-xs font-bold rounded-lg bg-blue-50 text-blue-800 border border-blue-200">
                    {{ $types[$question->type] ?? $question->type }}
                </span>
                <span class="px-3 py-1 text-xs font-bold rounded-lg border
                    @switch($question->difficulty)
                        @case('easy') bg-emerald-100 text-emerald-800 border-emerald-200 @break
                        @case('medium') bg-amber-100 text-amber-800 border-amber-200 @break
                        @case('hard') bg-rose-100 text-rose-800 border-rose-200 @break
                        @default bg-slate-100 text-slate-700 border-slate-200
                    @endswitch
                ">
                    Tingkat: {{ ucfirst($question->difficulty) }}
                </span>
                <span class="px-3 py-1 text-xs font-mono font-bold rounded-lg bg-slate-100 text-slate-800 border border-slate-200">
                    Bobot: {{ $question->score }} poin
                </span>
                <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $question->is_active ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                    {{ $question->is_active ? 'Status: Aktif' : 'Status: Nonaktif' }}
                </span>
            </div>

            <div class="p-6 sm:p-8 space-y-8">
                <!-- Question Text -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Teks Narasi Soal</h3>
                    <div class="prose max-w-none text-slate-900 text-base leading-relaxed p-5 bg-slate-50 border border-slate-200 rounded-xl">
                        {!! $question->question_text !!}
                    </div>

                    @if($question->image_path)
                        <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-xl inline-block">
                            <p class="text-[11px] font-bold text-slate-500 uppercase mb-2">Lampiran Gambar:</p>
                            <img src="{{ asset('storage/' . $question->image_path) }}" alt="Gambar Soal" class="max-w-md h-auto rounded-lg shadow-xs">
                        </div>
                    @endif
                    @if($question->audio_path)
                        <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <p class="text-[11px] font-bold text-slate-500 uppercase mb-2">Lampiran Audio:</p>
                            <audio controls src="{{ asset('storage/' . $question->audio_path) }}" class="w-full"></audio>
                        </div>
                    @endif
                    @if($question->video_path)
                        <div class="mt-4 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <p class="text-[11px] font-bold text-slate-500 uppercase mb-2">Lampiran Video:</p>
                            <video controls src="{{ asset('storage/' . $question->video_path) }}" class="w-full max-h-80 rounded"></video>
                        </div>
                    @endif
                </div>

                <!-- Options -->
                @if($question->options->count() > 0)
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Opsi Jawaban & Kunci</h3>
                    <div class="space-y-3">
                        @foreach($question->options->sortBy('sort_order') as $option)
                        <div class="p-4 rounded-xl border transition
                            @if($option->is_correct) bg-emerald-50/70 border-emerald-300 ring-1 ring-emerald-300/60 @else bg-slate-50 border-slate-200 @endif
                        ">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center font-mono font-bold text-sm
                                    @if($option->is_correct) bg-emerald-600 text-white @else bg-white border text-slate-700 @endif
                                ">
                                    {{ $option->label }}
                                </span>
                                <span class="flex-1 text-sm font-medium text-slate-900">{{ $option->option_text }}</span>
                                @if($option->is_correct)
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Kunci Jawaban Benar
                                    </span>
                                @endif
                                @if($option->correct_match)
                                    <span class="text-xs font-mono font-bold px-2 py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg">
                                        Pasangan: {{ $option->correct_match }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Metadata -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-slate-200 text-xs">
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-slate-500 uppercase font-bold tracking-wider block mb-1">Mata Pelajaran</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $question->subject->name ?? '-' }}</span>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-slate-500 uppercase font-bold tracking-wider block mb-1">Kelas Target</span>
                        <span class="font-bold text-slate-900 text-sm">{{ $question->class->name ?? 'Semua Kelas' }}</span>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                        <span class="text-slate-500 uppercase font-bold tracking-wider block mb-1">Kompetensi (KD/CP)</span>
                        <span class="font-bold text-slate-900 text-sm font-mono">{{ $question->competency_code ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>