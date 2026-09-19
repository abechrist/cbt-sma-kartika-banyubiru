<x-layouts.app :title="'Koreksi Lembar Esai'">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('grading.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                ← Kembali ke Antrean Koreksi
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Koreksi Lembar Jawaban Esai</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Berikan evaluasi nilai dan umpan balik catatan untuk butir soal esai siswa.</p>
        </div>

        <!-- Student & Exam Header Info -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-6">
            <div class="p-5 border-b border-slate-200 bg-slate-50/80">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-1">Peserta Didik</span>
                        <p class="font-bold text-sm text-slate-900">{{ $result->attempt->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Kelas / Rombel</span>
                        <p class="font-semibold text-slate-800">{{ $result->attempt->user->class->name ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Paket Ujian</span>
                        <p class="font-semibold text-slate-800">{{ $result->attempt->session->exam->name }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-1">Skor Otomatis Terkalkulasi</span>
                        <p class="font-mono font-bold text-sm text-brand-800">{{ number_format((float) $result->percentage, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        @if($answers->isEmpty())
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-8 text-slate-400 text-center text-sm">
                Tidak ada jawaban esai yang perlu dikoreksi pada percobaan ini.
            </div>
        @else
            <form action="{{ route('grading.update', $result) }}" method="POST">
                @csrf
                <div class="space-y-6">
                    @foreach($answers as $answer)
                    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                        <!-- Question Header -->
                        <div class="flex items-start justify-between gap-4 mb-4 pb-3 border-b border-slate-100">
                            <div>
                                <span class="px-2 py-0.5 rounded bg-brand-50 text-brand-800 text-xs font-bold border border-brand-200">
                                    Soal #{{ $answer->question_id }}
                                </span>
                                <h3 class="font-bold text-slate-900 mt-2 text-sm leading-relaxed">
                                    {{ $answer->question->question_text }}
                                </h3>
                            </div>
                            <span class="shrink-0 text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                                Maks: {{ $answer->question->score }} pts
                            </span>
                        </div>

                        <!-- Student's Written Response -->
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl mb-5">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Lembar Jawaban Siswa:</span>
                            <div class="text-slate-900 text-sm whitespace-pre-wrap leading-relaxed font-sans bg-white p-3.5 rounded-lg border border-slate-200">
                                {{ $answer->answer_text ?: '(Siswa tidak mengisikan jawaban esai)' }}
                            </div>
                        </div>

                        <!-- Teacher Grading Input -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Nilai Diperoleh (0 - {{ $answer->question->score }}) *
                                </label>
                                <input type="number" name="scores[{{ $answer->id }}]" min="0" max="{{ $answer->question->score }}"
                                    step="0.5" value="{{ old('scores.' . $answer->id, $answer->score ?? '') }}" required
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl font-mono text-sm font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Catatan / Umpan Balik Guru (Opsional)
                                </label>
                                <input type="text" name="notes[{{ $answer->id }}]" value="{{ old('notes.' . $answer->id, $answer->notes) }}"
                                    placeholder="Contoh: Jawaban runtut dan rumus tepat..."
                                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                            </div>
                        </div>
                        @error('scores.' . $answer->id)
                            <p class="text-xs text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 flex items-center justify-between">
                    <a href="{{ route('grading.show', $result) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-sm shadow-xs transition">
                        Simpan Penilaian Esai
                    </button>
                </div>
            </form>
        @endif
    </div>
</x-layouts.app>