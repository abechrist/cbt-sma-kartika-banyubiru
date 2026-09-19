<x-layouts.app :title="'Edit Soal - ' . $question->id">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('questions.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Bank Soal
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Butir Soal #{{ $question->id }}</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui teks soal, opsi jawaban, atau klasifikasi mata pelajaran.</p>
            </div>
            <a href="{{ route('questions.show', $question) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs border border-slate-200">
                Lihat Pratinjau
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <form action="{{ route('questions.update', $question) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="p-6 sm:p-8 space-y-8">
                    <!-- Basic Info -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 pb-3 border-b border-slate-200 mb-5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-800 flex items-center justify-center text-xs font-bold">1</span>
                            Informasi Dasar & Klasifikasi
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mata Pelajaran *</label>
                                <select name="subject_id" required
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Target Kelas / Rombel</label>
                                <select name="class_id"
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                                    <option value="">Pilih Kelas (Opsional)</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $question->class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tipe Format Soal *</label>
                                <select name="type" required id="question-type"
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 font-bold focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" {{ old('type', $question->type) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tingkat Kesulitan *</label>
                                <select name="difficulty" required
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                                    @foreach($difficulties as $key => $label)
                                        <option value="{{ $key }}" {{ old('difficulty', $question->difficulty) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bobot Nilai (Poin) *</label>
                                <input type="number" step="0.01" min="0" name="score" value="{{ old('score', $question->score) }}" required
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-800 font-bold focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Kompetensi (KD / CP)</label>
                                <input type="text" name="competency_code" value="{{ old('competency_code', $question->competency_code) }}"
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Teks Narasi / Pertanyaan Soal *</label>
                                <textarea name="question_text" rows="4" required
                                    class="w-full p-4 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none leading-relaxed transition">{{ old('question_text', $question->question_text) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Options for PG -->
                    <div id="options-container">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 pb-3 border-b border-slate-200 mb-5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-800 flex items-center justify-center text-xs font-bold">2</span>
                            Opsi Jawaban & Kunci Benar
                        </h2>
                        <div id="options-wrapper" class="space-y-3">
                            @php
                                $options = $question->options->sortBy('sort_order')->values();
                                $count = max($options->count(), 2);
                            @endphp
                            @foreach($options as $index => $option)
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 p-4 bg-slate-50 border border-slate-200 rounded-xl option-row">
                                <input type="text" name="options[{{ $index }}][label]" value="{{ $option->label }}" required
                                    class="w-full sm:w-16 px-3 py-2 bg-white border border-slate-300 rounded-lg text-center font-bold text-sm text-slate-800 focus:ring-2 focus:ring-brand-700 outline-none">
                                <input type="text" name="options[{{ $index }}][option_text]" value="{{ $option->option_text }}" required
                                    class="w-full flex-1 px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-brand-700 outline-none">
                                <label class="flex items-center is-correct-wrap px-3 py-1.5 bg-white border border-slate-300 rounded-lg cursor-pointer hover:bg-emerald-50 transition">
                                    <input type="checkbox" name="options[{{ $index }}][is_correct]" value="1" {{ $option->is_correct ? 'checked' : '' }} class="h-4 w-4 text-emerald-600 rounded">
                                    <span class="ml-2 text-xs font-bold text-slate-700">Kunci Benar</span>
                                </label>
                                <input type="text" name="options[{{ $index }}][correct_match]" value="{{ $option->correct_match }}" placeholder="Pasangan cocok"
                                    class="flex-1 px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-700 outline-none correct-match-input {{ $question->type === 'menjodohkan' ? '' : 'hidden' }}">
                                <input type="number" name="options[{{ $index }}][sort_order]" value="{{ $option->sort_order ?? $index }}" title="Urutan" class="w-16 px-3 py-2 bg-white border border-slate-300 rounded-lg text-center text-xs">
                                <button type="button" onclick="removeOption(this)" class="px-2.5 py-1 text-xs font-semibold text-rose-600 hover:text-rose-800 rounded hover:bg-rose-50 transition">Hapus</button>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <button type="button" onclick="addOption()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-brand-800 font-bold text-xs transition border border-slate-300 flex items-center gap-1.5">
                                <span>+ Tambah Opsi Baris</span>
                            </button>
                            <p class="text-xs text-slate-500">Minimal 2 opsi, maksimal 10 opsi.</p>
                        </div>
                    </div>

                    <!-- Media -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 pb-3 border-b border-slate-200 mb-5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-800 flex items-center justify-center text-xs font-bold">3</span>
                            Media Pendukung Soal
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Ganti Gambar</label>
                                @if($question->image_path)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $question->image_path) }}" alt="Preview" class="h-20 w-auto rounded object-cover border">
                                    </div>
                                @endif
                                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-800">
                            </div>
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Ganti Audio</label>
                                @if($question->audio_path)
                                    <div class="mb-2"><audio controls src="{{ asset('storage/' . $question->audio_path) }}" class="w-full h-8"></audio></div>
                                @endif
                                <input type="file" name="audio" accept="audio/*" class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-800">
                            </div>
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Ganti Video</label>
                                @if($question->video_path)
                                    <div class="mb-2"><video controls src="{{ asset('storage/' . $question->video_path) }}" class="h-20 w-auto rounded border"></video></div>
                                @endif
                                <input type="file" name="video" accept="video/*" class="w-full text-xs text-slate-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-800">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('questions.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-sm shadow-xs transition">
                            Simpan Perubahan Soal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const optionTypes = ['pg', 'pg_kompleks', 'menjodohkan'];

        function updateOptionsVisibility() {
            const type = document.getElementById('question-type').value;
            const container = document.getElementById('options-container');
            const isOptionType = optionTypes.includes(type);
            container.style.display = isOptionType ? '' : 'none';

            const isMatching = type === 'menjodohkan';
            document.querySelectorAll('.option-row').forEach(row => {
                const correctWrap = row.querySelector('.is-correct-wrap');
                const matchInput = row.querySelector('.correct-match-input');
                if (isMatching) {
                    correctWrap.style.display = 'none';
                    matchInput.classList.remove('hidden');
                    matchInput.required = true;
                } else {
                    correctWrap.style.display = '';
                    matchInput.classList.add('hidden');
                    matchInput.required = false;
                }
            });
        }
        document.getElementById('question-type').addEventListener('change', updateOptionsVisibility);
        updateOptionsVisibility();

        function addOption() {
            const wrapper = document.getElementById('options-wrapper');
            const count = wrapper.querySelectorAll('.option-row').length;
            if (count >= 10) {
                alert('Maksimal 10 opsi');
                return;
            }
            
            const div = document.createElement('div');
            div.className = 'flex flex-col sm:flex-row items-start sm:items-center gap-3 p-4 bg-slate-50 border border-slate-200 rounded-xl option-row';
            const isMatching = document.getElementById('question-type').value === 'menjodohkan';
            const labelLetter = String.fromCharCode(65 + count);
            div.innerHTML = `
                <input type="text" name="options[${count}][label]" value="${labelLetter}" placeholder="Label" required
                    class="w-full sm:w-16 px-3 py-2 bg-white border border-slate-300 rounded-lg text-center font-bold text-sm text-slate-800 focus:ring-2 focus:ring-brand-700 outline-none">
                <input type="text" name="options[${count}][option_text]" placeholder="Teks opsi..." required
                    class="w-full flex-1 px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:ring-2 focus:ring-brand-700 outline-none">
                <label class="flex items-center is-correct-wrap px-3 py-1.5 bg-white border border-slate-300 rounded-lg cursor-pointer hover:bg-emerald-50 transition">
                    <input type="checkbox" name="options[${count}][is_correct]" value="1" class="h-4 w-4 text-emerald-600 rounded">
                    <span class="ml-2 text-xs font-bold text-slate-700">Kunci Benar</span>
                </label>
                <input type="text" name="options[${count}][correct_match]" placeholder="Pasangan cocok"
                    class="flex-1 px-3 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-700 outline-none correct-match-input ${isMatching ? '' : 'hidden'}">
                <input type="number" name="options[${count}][sort_order]" value="${count}" class="w-16 px-3 py-2 bg-white border border-slate-300 rounded-lg text-center text-xs">
                <button type="button" onclick="removeOption(this)" class="px-2.5 py-1 text-xs font-semibold text-rose-600 hover:text-rose-800 rounded hover:bg-rose-50 transition">Hapus</button>
            `;
            if (isMatching) {
                const correctWrap = div.querySelector('.is-correct-wrap');
                correctWrap.style.display = 'none';
                div.querySelector('.correct-match-input').required = true;
            }
            wrapper.appendChild(div);
        }

        function removeOption(btn) {
            const rows = document.querySelectorAll('.option-row');
            if (rows.length <= 2) {
                alert('Minimal 2 opsi');
                return;
            }
            btn.closest('.option-row').remove();
        }
    </script>
</x-layouts.app>