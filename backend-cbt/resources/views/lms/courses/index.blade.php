<x-layouts.app :title="'LMS Pembelajaran - SMA Kartika III-1 Banyubiru'">
    <div class="max-w-7xl mx-auto py-6 sm:py-8 px-4 sm:px-6 lg:px-8">
        <!-- Hero Header -->
        <div class="rounded-3xl p-6 sm:p-8 mb-8 shadow-lg border border-brand-800 relative overflow-hidden text-white" style="background: linear-gradient(135deg, #061d13 0%, #0b3120 50%, #061d13 100%);">
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-brand-900 text-gold-300 border border-brand-700 mb-3 shadow-xs">
                        <svg class="w-3.5 h-3.5 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span>Portal Learning Management System (LMS)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Kelas Pembelajaran Daring</h1>
                    <p class="text-sm text-brand-100 mt-1 max-w-2xl font-medium leading-relaxed">
                        Akses modul materi kurikulum merdeka, materi video interaktif, dan pengumpulan tugas mandiri terintegrasi SMA Kartika III-1 Banyubiru.
                    </p>
                </div>

                @if(in_array(auth()->user()->role?->name, ['super_admin', 'admin', 'guru']))
                <button onclick="document.getElementById('modalNewCourse').classList.remove('hidden')" 
                    class="px-5 py-2.5 rounded-xl bg-gold-400 hover:bg-gold-500 text-brand-950 font-black text-sm tracking-wide transition shadow-lg flex items-center gap-2 shrink-0 border border-gold-300 hover:scale-[1.02] active:scale-[0.98]">
                    <svg class="w-4 h-4 text-brand-950" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Buka Kelas Ajar Baru</span>
                </button>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Courses Grid -->
        @if($courses->isEmpty())
            <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Kelas Pembelajaran</h3>
                <p class="text-sm text-slate-500 mt-1">Saat ini belum ada kelas aktif yang terdaftar untuk akun Anda.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md transition duration-200 overflow-hidden flex flex-col group">
                    <!-- Card Top Header with Solid Deep Kartika Green Background -->
                    <div class="p-6 text-white relative border-b border-brand-800/40" style="background: linear-gradient(135deg, #0b3120 0%, #12472e 100%);">
                        <div class="flex justify-between items-center mb-3">
                            <span class="px-3 py-1 rounded-full text-xs font-black bg-gold-400 text-brand-950 shadow-xs border border-gold-300">
                                {{ $course->studentClass->name ?? 'Semua Kelas' }}
                            </span>
                            <span class="text-xs text-gold-200 font-mono font-bold">T.A. {{ $course->academic_year }}</span>
                        </div>
                        <h2 class="text-xl font-black text-white tracking-tight group-hover:text-gold-300 transition leading-snug">
                            {{ $course->subject->name }}
                        </h2>
                        <p class="text-xs text-brand-100 mt-2 flex items-center gap-1.5 font-medium">
                            <svg class="w-3.5 h-3.5 text-gold-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Guru Pengampu: <strong class="text-white font-bold">{{ $course->teacher->name }}</strong></span>
                        </p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 flex-1 flex flex-col justify-between">
                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed mb-5">
                            {{ $course->description ?? 'Tidak ada deskripsi pengantar untuk mata pelajaran ini.' }}
                        </p>

                        <div class="grid grid-cols-2 gap-3 mb-5 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                                    {{ $course->materials->count() }}
                                </span>
                                <span class="font-medium text-slate-700">Materi Ajar</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-900 flex items-center justify-center font-bold">
                                    {{ $course->assignments->count() }}
                                </span>
                                <span class="font-medium text-slate-700">Tugas Harian</span>
                            </div>
                        </div>

                        <a href="{{ route('lms.courses.show', $course->id) }}" 
                            class="w-full py-2.5 rounded-xl bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs tracking-wide text-center transition shadow-xs flex items-center justify-center gap-1.5">
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
    <div id="modalNewCourse" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="bg-brand-950 text-white p-5 flex justify-between items-center">
                <h3 class="font-bold text-base">Buka Kelas Pembelajaran Baru</h3>
                <button onclick="document.getElementById('modalNewCourse').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <form action="{{ route('lms.courses.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Mata Pelajaran</label>
                    <select name="subject_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-700 outline-none">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code ?? 'Umum' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Rombongan Belajar (Kelas)</label>
                    <select name="class_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-brand-700 outline-none">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tahun Ajaran</label>
                        <input type="text" name="academic_year" value="2026/2027" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Semester</label>
                        <select name="semester" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none">
                            <option value="ganjil">Ganjil</option>
                            <option value="genap">Genap</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Deskripsi Ringkas</label>
                    <textarea name="description" rows="2" placeholder="Capaian pembelajaran atau info umum kelas..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalNewCourse').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white text-sm font-bold shadow-xs">Simpan & Buka Kelas</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</x-layouts.app>
