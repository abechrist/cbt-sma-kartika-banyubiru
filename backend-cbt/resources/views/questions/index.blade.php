<x-layouts.app :title="'Bank Soal'">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">Bank Soal Ujian</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Koleksi butir soal multi-tipe (Pilihan Ganda, Benar/Salah, Menjodohkan, Esai) terstandar.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('import_export') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200/80 shadow-2xs">
                Import Soal
            </a>
            <a href="{{ route('questions.create') }}" class="px-5 py-2.5 rounded-2xl bg-brand-900 hover:bg-brand-950 text-white font-extrabold text-xs shadow-md hover:shadow-lg btn-glow-brand transition-all duration-150 flex items-center gap-2">
                <span class="text-base font-black leading-none">+</span>
                <span>Tambah Soal Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar (Modern Bento Shell) -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/90 mb-8">
        <form method="GET" class="flex flex-wrap items-end gap-3.5">
            <div class="w-full sm:w-48">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Mata Pelajaran</label>
                <select name="subject_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-brand-700 focus:bg-white outline-none transition">
                    <option value="">Semua Mapel</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-36">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Kelas</label>
                <select name="class_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-brand-700 focus:bg-white outline-none transition">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-40">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Tipe Soal</label>
                <select name="type" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-brand-700 focus:bg-white outline-none transition">
                    <option value="">Semua Tipe</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-32">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Kesulitan</label>
                <select name="difficulty" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs text-slate-800 font-semibold focus:ring-2 focus:ring-brand-700 focus:bg-white outline-none transition">
                    <option value="">Semua</option>
                    <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Mudah</option>
                    <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Sulit</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Cari Soal</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci isi soal..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-brand-900 hover:bg-brand-950 text-white font-extrabold text-xs shadow-md transition">
                    Filter
                </button>
                <a href="{{ route('questions.index') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Questions Table (Modern Card Presentation) -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/70 text-slate-600 text-xs uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4 text-left whitespace-nowrap">Konten Pertanyaan</th>
                        <th class="px-6 py-4 text-left whitespace-nowrap">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left whitespace-nowrap">Kelas</th>
                        <th class="px-6 py-4 text-left whitespace-nowrap">Tipe</th>
                        <th class="px-6 py-4 text-left whitespace-nowrap">Kesulitan</th>
                        <th class="px-6 py-4 text-left whitespace-nowrap">Bobot</th>
                        <th class="px-6 py-4 text-left whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($questions as $question)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-6 py-4 min-w-[280px] max-w-md">
                            <div class="font-bold text-slate-900 line-clamp-2 leading-relaxed">
                                {{ Str::limit(strip_tags($question->question_text), 90) }}
                            </div>
                            <div class="text-[11px] font-medium text-slate-400 mt-1">Oleh: {{ $question->creator->name ?? 'Admin' }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-semibold whitespace-nowrap">{{ $question->subject->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-600 font-semibold whitespace-nowrap">{{ $question->class->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-extrabold rounded-xl border whitespace-nowrap shadow-2xs
                                @switch($question->type)
                                    @case('multiple_choice') bg-blue-50 text-blue-800 border-blue-200 @break
                                    @case('true_false') bg-purple-50 text-purple-800 border-purple-200 @break
                                    @case('matching') bg-amber-50 text-amber-900 border-amber-200 @break
                                    @case('essay') bg-teal-50 text-teal-800 border-teal-200 @break
                                    @default bg-slate-100 text-slate-700 border-slate-200
                                @endswitch
                            ">
                                {{ $types[$question->type] ?? $question->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-extrabold rounded-xl border whitespace-nowrap shadow-2xs
                                @switch($question->difficulty)
                                    @case('easy') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                    @case('medium') bg-amber-50 text-amber-900 border-amber-200 @break
                                    @case('hard') bg-rose-50 text-rose-800 border-rose-200 @break
                                    @default bg-slate-100 text-slate-700 border-slate-200
                                @endswitch
                            ">
                                {{ ucfirst($question->difficulty) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-mono font-bold text-slate-800 whitespace-nowrap">{{ $question->score }} pts</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-bold rounded-full border whitespace-nowrap {{ $question->is_active ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('questions.show', $question) }}" class="px-3 py-2 sm:py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                    Detail
                                </a>
                                <a href="{{ route('questions.edit', $question) }}" class="px-3 py-2 sm:py-1.5 rounded-xl bg-gold-50 hover:bg-gold-100 text-gold-900 font-bold text-xs border border-gold-200 transition">
                                    Edit
                                </a>
                                <form action="{{ route('questions.destroy', $question) }}" method="POST" class="inline" onsubmit="return confirm('Hapus butir soal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-2 sm:py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-sm">
                            Tidak ada butir soal yang sesuai dengan kriteria filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/40">
            {{ $questions->links() }}
        </div>
    </div>
</x-layouts.app>