<x-layouts.app :title="'Buat Paket Ujian Baru'">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('exams.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Daftar Paket Ujian
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Buat Paket Ujian Baru</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Atur durasi, aturan pengacakan soal, dan pilih butir soal untuk diujikan.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <form action="{{ route('exams.store') }}" method="POST">
                @csrf
                <div class="p-6 sm:p-8 space-y-8">
                    <!-- General Info -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 pb-3 border-b border-slate-200 mb-5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-800 flex items-center justify-center text-xs font-bold">1</span>
                            Informasi & Parameter Ujian
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Paket Ujian *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Penilaian Akhir Semester (PAS) Ganjil Matematika Kelas X"
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mata Pelajaran</label>
                                <select name="subject_id" id="exam-subject"
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                                    <option value="">Pilih Mata Pelajaran</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alokasi Durasi (Menit) *</label>
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="1" max="300" required
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 font-bold focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Petunjuk / Deskripsi Ujian</label>
                                <textarea name="description" rows="3" placeholder="Tuliskan petunjuk umum pengerjaan ujian untuk peserta..."
                                    class="w-full p-3.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Security and Exam Rules -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 pb-3 border-b border-slate-200 mb-5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-800 flex items-center justify-center text-xs font-bold">2</span>
                            Konfigurasi Keamanan & Tampilan Soal
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex items-center p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                                <input type="checkbox" name="randomize_questions" value="1" class="h-4 w-4 text-brand-700 focus:ring-brand-500 rounded">
                                <span class="ml-3 text-xs font-semibold text-slate-800">Acak Urutan Butir Soal</span>
                            </label>
                            <label class="flex items-center p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                                <input type="checkbox" name="randomize_options" value="1" class="h-4 w-4 text-brand-700 focus:ring-brand-500 rounded">
                                <span class="ml-3 text-xs font-semibold text-slate-800">Acak Urutan Pilihan Opsi</span>
                            </label>
                            <label class="flex items-center p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                                <input type="checkbox" name="allow_back" value="1" checked class="h-4 w-4 text-brand-700 focus:ring-brand-500 rounded">
                                <span class="ml-3 text-xs font-semibold text-slate-800">Boleh Navigasi Bolak-balik</span>
                            </label>
                            <label class="flex items-center p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                                <input type="checkbox" name="show_result_after" value="1" class="h-4 w-4 text-brand-700 focus:ring-brand-500 rounded">
                                <span class="ml-3 text-xs font-semibold text-slate-800">Rilis Nilai Otomatis ke Siswa</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question Picker -->
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 pb-3 border-b border-slate-200 mb-5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-brand-50 text-brand-800 flex items-center justify-center text-xs font-bold">3</span>
                            Pilih Butir Soal (Dapat Ditambahkan Nanti)
                        </h2>
                        <div class="max-h-96 overflow-y-auto border border-slate-200 rounded-xl divide-y divide-slate-100">
                            @foreach($questions->groupBy('subject.name') as $subjectName => $subjectQuestions)
                            <div class="p-3 bg-slate-100 font-bold text-xs uppercase tracking-wider text-slate-700 sticky top-0">
                                {{ $subjectName ?? 'Mata Pelajaran Umum' }} ({{ $subjectQuestions->count() }} soal)
                            </div>
                            @foreach($subjectQuestions as $question)
                            <label class="p-3.5 flex items-center hover:bg-slate-50 transition cursor-pointer">
                                <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" class="h-4 w-4 text-brand-700 focus:ring-brand-500 rounded">
                                <div class="ml-3 flex-1 text-xs text-slate-800 line-clamp-1 font-medium">
                                    {{ Str::limit(strip_tags($question->question_text), 120) }}
                                </div>
                                <span class="ml-2 px-2 py-0.5 text-[10px] font-bold rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $question->type }}
                                </span>
                                <span class="ml-2 px-2 py-0.5 text-[10px] font-bold rounded-md border
                                    @switch($question->difficulty)
                                        @case('easy') bg-emerald-50 text-emerald-700 border-emerald-200 @break
                                        @case('medium') bg-amber-50 text-amber-700 border-amber-200 @break
                                        @case('hard') bg-rose-50 text-rose-700 border-rose-200 @break
                                        @default bg-slate-50 text-slate-700 border-slate-200
                                    @endswitch
                                ">
                                    {{ ucfirst($question->difficulty) }}
                                </span>
                            </label>
                            @endforeach
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('exams.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-sm shadow-xs transition">
                            Simpan Paket Ujian
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>