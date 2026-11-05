<x-layouts.app :title="'LMS Pembelajaran - SMA Kartika III-1 Banyubiru'">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">Kelas Pembelajaran Daring</h1>
                <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed">
                    Akses modul materi pelajaran, bahan bacaan, dan pengumpulan tugas mandiri terintegrasi SMA Kartika III-1 Banyubiru.
                </p>
            </div>

            @if(in_array(auth()->user()->role?->name, ['super_admin', 'admin', 'guru']))
            <button type="button" onclick="document.getElementById('modalNewCourse').classList.remove('hidden')" 
                class="w-full md:w-auto min-h-[40px] px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition flex items-center justify-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Buka Kelas Ajar Baru</span>
            </button>
            @endif
        </div>

        <!-- Courses Grid -->
        @if($courses->isEmpty())
            <div class="bg-white rounded-xl p-10 text-center border border-slate-200">
                <div class="w-12 h-12 mx-auto rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h2 class="text-base font-bold text-slate-800">Belum Ada Kelas Pembelajaran</h2>
                <p class="text-xs text-slate-500 mt-1">Saat ini belum ada kelas aktif yang terdaftar untuk akun Anda.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($courses as $course)
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col">
                    <!-- Card Header -->
                    <div class="p-5 bg-brand-950 text-white border-b border-brand-900">
                        <div class="flex justify-between items-center mb-2.5">
                            <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-gold-400 text-brand-950">
                                {{ $course->studentClass->name ?? 'Semua Kelas' }}
                            </span>
                            <span class="text-xs text-brand-200 font-mono font-semibold">T.A. {{ $course->academic_year }}</span>
                        </div>
                        <h2 class="text-lg font-bold text-white tracking-tight leading-snug">
                            {{ $course->subject->name }}
                        </h2>
                        <p class="text-xs text-brand-200 mt-1.5 flex items-center gap-1.5 font-medium">
                            <svg class="w-3.5 h-3.5 text-gold-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Guru Pengampu: <strong class="text-white font-semibold">{{ $course->teacher->name }}</strong></span>
                        </p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed mb-4">
                            {{ $course->description ?? 'Tidak ada deskripsi pengantar untuk mata pelajaran ini.' }}
                        </p>

                        <div class="grid grid-cols-2 gap-3 mb-4 p-3 rounded-lg bg-slate-50 border border-slate-200/80 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold font-mono tabular-nums">
                                    {{ $course->materials->count() }}
                                </span>
                                <span class="font-medium text-slate-700">Materi Ajar</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-amber-100 text-amber-900 flex items-center justify-center font-bold font-mono tabular-nums">
                                    {{ $course->assignments->count() }}
                                </span>
                                <span class="font-medium text-slate-700">Tugas Harian</span>
                            </div>
                        </div>

                        <a href="{{ route('lms.courses.show', $course->id) }}" 
                            class="w-full min-h-[40px] py-2 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs text-center transition flex items-center justify-center gap-1.5">
                            <span>Buka Ruang Belajar</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal Tambah Kursus Baru (Guru & Admin) -->
    @if(in_array(auth()->user()->role?->name, ['super_admin', 'admin', 'guru']))
    <div id="modalNewCourse" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 hidden">
        <div class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="bg-brand-950 text-white p-4 sm:p-5 flex justify-between items-center sticky top-0 z-10">
                <h3 class="font-bold text-sm sm:text-base">Buka Kelas Pembelajaran Baru</h3>
                <button type="button" onclick="document.getElementById('modalNewCourse').classList.add('hidden')" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition">&times;</button>
            </div>
            <form action="{{ route('lms.courses.store') }}" method="POST" class="p-5 sm:p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Mata Pelajaran</label>
                    <select name="subject_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-700/25 outline-none">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code ?? 'Umum' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Rombongan Belajar (Kelas)</label>
                    <select name="class_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-700/25 outline-none">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tahun Ajaran</label>
                        <input type="text" name="academic_year" value="2026/2027" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Semester</label>
                        <select name="semester" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm outline-none">
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Deskripsi Ringkas</label>
                    <textarea name="description" rows="2" placeholder="Capaian pembelajaran atau info umum kelas..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm outline-none"></textarea>
                </div>

                <div class="pt-2 flex flex-col-reverse sm:flex-row justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalNewCourse').classList.add('hidden')" class="w-full sm:w-auto min-h-[40px] px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center justify-center">Batal</button>
                    <button type="submit" class="w-full sm:w-auto min-h-[40px] px-5 py-2 rounded-lg bg-brand-800 hover:bg-brand-900 text-white text-xs font-bold flex items-center justify-center">Simpan &amp; Buka Kelas</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</x-layouts.app>
