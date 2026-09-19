<x-layouts.app :title="$course->subject->name . ' - ' . ($course->studentClass->name ?? 'Kelas')">
    <div class="max-w-7xl mx-auto py-6 sm:py-8 px-4 sm:px-6 lg:px-8">
        <!-- Back Navigation & Breadcrumb -->
        <div class="mb-4 flex items-center gap-2 text-xs font-medium text-slate-500">
            <a href="{{ route('lms.courses.index') }}" class="hover:text-brand-800 transition flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Daftar Kelas Pembelajaran</span>
            </a>
            <span>/</span>
            <span class="text-slate-800 font-bold">{{ $course->subject->name }}</span>
        </div>

        <!-- Course Institutional Hero Banner -->
        <div class="text-white rounded-3xl p-6 sm:p-8 mb-8 shadow-lg border border-brand-800 relative" style="background: linear-gradient(135deg, #061d13 0%, #0b3120 50%, #061d13 100%);">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2.5">
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-gold-400 text-brand-950 border border-gold-300 shadow-xs">
                            Kelas {{ $course->studentClass->name ?? 'Semua' }}
                        </span>
                        <span class="text-xs text-gold-200 font-mono font-bold">T.A. {{ $course->academic_year }} (Semester {{ ucfirst($course->semester) }})</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $course->subject->name }}</h1>
                    <p class="text-xs text-brand-100 mt-1.5 flex items-center gap-2 font-medium">
                        <span>Guru Pengampu: <strong class="text-white font-bold">{{ $course->teacher->name }}</strong></span>
                    </p>
                    @if($course->description)
                        <p class="text-xs text-brand-100 mt-2.5 max-w-2xl leading-relaxed font-normal">{{ $course->description }}</p>
                    @endif
                </div>

                <!-- Quick Stats Badge -->
                <div class="flex items-center gap-3 bg-brand-900/90 p-3.5 rounded-2xl border border-brand-700 shadow-xs shrink-0">
                    <div class="text-center px-3 border-r border-brand-800">
                        <span class="text-xl font-black text-gold-300 font-mono">{{ $course->materials->count() }}</span>
                        <p class="text-[10px] text-brand-200 uppercase font-bold tracking-wider">Materi</p>
                    </div>
                    <div class="text-center px-3 border-r border-brand-800">
                        <span class="text-xl font-black text-amber-300 font-mono">{{ $course->assignments->count() }}</span>
                        <p class="text-[10px] text-brand-200 uppercase font-bold tracking-wider">Tugas</p>
                    </div>
                    <div class="text-center px-3">
                        <span class="text-xl font-black text-emerald-300 font-mono">{{ $course->studentClass->students->count() ?? 0 }}</span>
                        <p class="text-[10px] text-brand-200 uppercase font-bold tracking-wider">Siswa</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- LMS Section Tabs -->
        <div class="border-b border-slate-200 mb-8 flex gap-4 sm:gap-6 text-sm font-bold overflow-x-auto pb-1 scrollbar-none">
            <button onclick="switchTab('tabMaterials')" id="btnTabMaterials" class="pb-3 border-b-2 border-brand-800 text-brand-950 flex items-center gap-2 whitespace-nowrap shrink-0">
                <svg class="w-4 h-4 text-brand-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Materi Pembelajaran ({{ $course->materials->count() }})</span>
            </button>
            <button onclick="switchTab('tabAssignments')" id="btnTabAssignments" class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2 whitespace-nowrap shrink-0">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Tugas & Portofolio ({{ $course->assignments->count() }})</span>
            </button>
            <button onclick="switchTab('tabDiscussions')" id="btnTabDiscussions" class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2 whitespace-nowrap shrink-0">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>Forum Diskusi ({{ $course->discussions->count() }})</span>
            </button>
            <button onclick="switchTab('tabStudents')" id="btnTabStudents" class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2 whitespace-nowrap shrink-0">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Anggota Kelas</span>
            </button>
        </div>

        <!-- TAB 1: MATERI PEMBELAJARAN -->
        <div id="tabMaterials" class="tab-content">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Daftar Modul & Materi</h2>
                    <p class="text-xs text-slate-500">Materi ajar kurikulum merdeka yang telah dipublikasikan guru</p>
                </div>
                @if(in_array(auth()->user()->role?->name, ['super_admin', 'admin', 'guru']))
                <button onclick="document.getElementById('modalNewMaterial').classList.remove('hidden')"
                    class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Materi Baru</span>
                </button>
                @endif
            </div>

            @if($course->materials->isEmpty())
                <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 text-slate-500 text-sm">
                    Belum ada materi pembelajaran yang diunggah untuk kelas ini.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($course->materials as $mat)
                    @php
                        $isCompleted = auth()->user()->isSiswa() && $mat->isCompletedBy(auth()->id());
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:border-brand-300 transition flex items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0
                                @if($mat->type === 'video') bg-rose-100 text-rose-700
                                @elseif($mat->type === 'file') bg-blue-100 text-blue-700
                                @else bg-emerald-100 text-emerald-800
                                @endif
                            ">
                                @if($mat->type === 'video')
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                @elseif($mat->type === 'file')
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                @endif
                            </div>

                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">{{ $mat->chapter ?? 'Umum' }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-xs px-2 py-0.2 rounded-full font-semibold
                                        @if($mat->type === 'video') bg-rose-50 text-rose-700 border border-rose-200
                                        @elseif($mat->type === 'file') bg-blue-50 text-blue-700 border border-blue-200
                                        @else bg-emerald-50 text-emerald-800 border border-emerald-200
                                        @endif
                                    ">
                                        {{ strtoupper($mat->type) }}
                                    </span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 leading-snug">
                                    <a href="{{ route('lms.materials.show', [$course->id, $mat->id]) }}" class="hover:text-brand-800 transition">
                                        {{ $mat->title }}
                                    </a>
                                </h3>
                                @if(auth()->user()->isSiswa())
                                    <div class="mt-2">
                                        @if($isCompleted)
                                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                Selesai Dipelajari
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 font-medium">Belum dibaca</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('lms.materials.show', [$course->id, $mat->id]) }}" 
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-brand-50 hover:text-brand-900 text-slate-700 font-bold text-xs transition">
                                Pelajari →
                            </a>
                            @if(in_array(auth()->user()->role?->name, ['super_admin', 'admin', 'guru']))
                            <form action="{{ route('lms.materials.destroy', [$course->id, $mat->id]) }}" method="POST" onsubmit="return confirm('Hapus materi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="Hapus materi pembelajaran" class="p-2 rounded-xl text-slate-500 hover:text-rose-700 hover:bg-rose-50 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 2: TUGAS & PORTOFOLIO -->
        <div id="tabAssignments" class="tab-content hidden">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Daftar Penugasan</h2>
                    <p class="text-xs text-slate-500">Tugas harian, PR, dan proyek mandiri kelas</p>
                </div>
                @if(in_array(auth()->user()->role?->name, ['super_admin', 'admin', 'guru']))
                <button onclick="document.getElementById('modalNewAssignment').classList.remove('hidden')"
                    class="px-4 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Buat Tugas Baru</span>
                </button>
                @endif
            </div>

            @if($course->assignments->isEmpty())
                <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 text-slate-500 text-sm">
                    Belum ada tugas yang diberikan untuk kelas ini.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($course->assignments as $assign)
                    @php
                        $sub = auth()->user()->isSiswa() ? $assign->submissionForUser(auth()->id()) : null;
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:border-brand-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-mono font-bold text-slate-500">Maksimal: {{ $assign->max_score }} Poin</span>
                                @if($assign->due_date)
                                    <span class="text-slate-300">•</span>
                                    <span class="text-xs font-semibold text-amber-700 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Batas: {{ $assign->due_date->format('d M Y, H:i') }} WIB
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-base font-bold text-slate-900 leading-snug">
                                <a href="{{ route('lms.assignments.show', [$course->id, $assign->id]) }}" class="hover:text-brand-800 transition">
                                    {{ $assign->title }}
                                </a>
                            </h3>
                            @if(auth()->user()->isSiswa())
                                <div class="mt-2">
                                    @if($sub)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold
                                            @if($sub->status === 'graded') bg-emerald-100 text-emerald-800 border border-emerald-200
                                            @else bg-blue-100 text-blue-800 border border-blue-200
                                            @endif
                                        ">
                                            @if($sub->status === 'graded')
                                                Ternilai: {{ $sub->score }}/{{ $assign->max_score }}
                                            @else
                                                Terkumpul (Menunggu Koreksi)
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            Belum Dikumpulkan
                                        </span>
                                    @endif
                                </div>
                            @else
                                <p class="text-xs text-slate-500 mt-1">
                                    Terkumpul: <strong>{{ $assign->submissions->count() }}</strong> dari {{ $course->studentClass->students->count() ?? 0 }} siswa
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('lms.assignments.show', [$course->id, $assign->id]) }}" 
                                class="px-4 py-2 rounded-xl bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs shadow-xs transition">
                                @if(auth()->user()->isSiswa())
                                    {{ $sub ? 'Lihat Tugas & Nilai →' : 'Kumpulkan Tugas →' }}
                                @else
                                    Periksa & Nilai ({{ $assign->submissions->count() }}) →
                                @endif
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 3: FORUM DISKUSI -->
        <div id="tabDiscussions" class="tab-content hidden">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Ruang Tanya Jawab & Diskusi</h2>
                    <p class="text-xs text-slate-500">Ajukan pertanyaan seputar materi pelajaran atau diskusikan soal</p>
                </div>
            </div>

            <!-- Form Buat Topik Baru -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-8 shadow-xs">
                <form action="{{ route('lms.discussions.store', $course->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <input type="text" name="title" required placeholder="Judul topik pertanyaan atau diskusi..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none">
                    </div>
                    <div>
                        <textarea name="content" rows="3" required placeholder="Tuliskan isi pertanyaan atau diskusi Anda di sini secara jelas..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs">Kirim Pertanyaan</button>
                    </div>
                </form>
            </div>

            @if($course->discussions->isEmpty())
                <div class="bg-white rounded-2xl p-8 text-center border border-slate-200 text-slate-400 text-sm">
                    Belum ada topik diskusi. Jadilah yang pertama mengajukan pertanyaan!
                </div>
            @else
                <div class="space-y-6">
                    @foreach($course->discussions as $disc)
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-full bg-brand-800 text-white font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr($disc->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $disc->user->name }}</h4>
                                <p class="text-[10px] text-slate-400">{{ $disc->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900 mb-2">{{ $disc->title }}</h3>
                        <p class="text-sm text-slate-700 leading-relaxed mb-4">{{ $disc->content }}</p>

                        <!-- Replies -->
                        @if($disc->replies->isNotEmpty())
                            <div class="mt-4 border border-slate-200/90 space-y-3 bg-slate-50/70 p-4 rounded-2xl">
                                @foreach($disc->replies as $rep)
                                <div class="text-xs">
                                    <div class="flex items-center gap-2 mb-1">
                                        <strong class="text-brand-950">{{ $rep->user->name }}</strong>
                                        <span class="text-slate-400 text-[10px]">{{ $rep->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-slate-600 leading-relaxed">{{ $rep->content }}</p>
                                </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Reply Form -->
                        <form action="{{ route('lms.discussions.reply', [$course->id, $disc->id]) }}" method="POST" class="mt-4 flex gap-2">
                            @csrf
                            <input type="text" name="content" required placeholder="Tuliskan tanggapan Anda..." class="flex-1 px-3.5 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none focus:bg-white focus:ring-1 focus:ring-brand-700">
                            <button type="submit" class="px-4 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold">Balas</button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 4: ANGGOTA KELAS & REKAP -->
        <div id="tabStudents" class="tab-content hidden">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <div class="p-5 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Peserta Didik Kelas {{ $course->studentClass->name ?? '' }}</h3>
                        <p class="text-xs text-slate-500">Daftar siswa yang berhak mengakses kelas pembelajaran ini</p>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-brand-100 text-brand-900">
                        Total: {{ $course->studentClass->students->count() ?? 0 }} Siswa
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">Nama Siswa</th>
                                <th class="px-6 py-3 text-left">NISN</th>
                                <th class="px-6 py-3 text-left">Email</th>
                                <th class="px-6 py-3 text-center">Tugas Terkumpul</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($course->studentClass->students ?? [] as $st)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-6 py-3 font-bold text-slate-900">{{ $st->name }}</td>
                                <td class="px-6 py-3 font-mono text-xs text-slate-600">{{ $st->nisn ?? '-' }}</td>
                                <td class="px-6 py-3 text-slate-500 text-xs">{{ $st->email }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                        {{ $st->assignmentSubmissions()->whereHas('assignment', fn($q) => $q->where('course_id', $course->id))->count() }} / {{ $course->assignments->count() }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada siswa di kelas ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Materi Baru (Guru) -->
    @if(in_array(auth()->user()->role?->name, ['super_admin', 'admin', 'guru']))
    <div id="modalNewMaterial" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="bg-brand-950 text-white p-5 flex justify-between items-center">
                <h3 class="font-bold text-base">Tambah Materi Pembelajaran Baru</h3>
                <button onclick="document.getElementById('modalNewMaterial').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <form action="{{ route('lms.materials.store', $course->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Judul Materi</label>
                    <input type="text" name="title" required placeholder="Contoh: Bab 1 Konsep Dasar Eksponen" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Bab / Pertemuan</label>
                        <input type="text" name="chapter" placeholder="Bab 1 / Pertemuan 1" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tipe Materi</label>
                        <select name="type" id="materialTypeSelect" onchange="toggleMaterialInputs()" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none">
                            <option value="article">Artikel / Rangkuman Bacaan</option>
                            <option value="video">Video Pembelajaran (YouTube/Link)</option>
                            <option value="file">Berkas Dokumen (PDF/PPT/Word)</option>
                        </select>
                    </div>
                </div>

                <div id="inputVideoUrl" class="hidden">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">URL Video (YouTube / Link)</label>
                    <input type="url" name="video_url" placeholder="https://www.youtube.com/watch?v=..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none">
                </div>

                <div id="inputFile" class="hidden">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Unggah Berkas (PDF, PPT, DOCX, maks 20MB)</label>
                    <input type="file" name="file" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none">
                </div>

                <div id="inputText">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Isi Ringkasan Materi</label>
                    <textarea name="content_text" rows="4" placeholder="Tuliskan isi materi atau pengantar rangkuman di sini..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalNewMaterial').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white text-sm font-bold shadow-xs">Terbitkan Materi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Tugas Baru (Guru) -->
    <div id="modalNewAssignment" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-150">
            <div class="bg-brand-950 text-white p-5 flex justify-between items-center">
                <h3 class="font-bold text-base">Buat Penugasan Siswa Baru</h3>
                <button onclick="document.getElementById('modalNewAssignment').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
            </div>
            <form action="{{ route('lms.assignments.store', $course->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Judul Tugas</label>
                    <input type="text" name="title" required placeholder="Contoh: Tugas Mandiri 1 Sifat-sifat Eksponen" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Tenggat Waktu (Deadline)</label>
                        <input type="datetime-local" name="due_date" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nilai Maksimal</label>
                        <input type="number" name="max_score" value="100" min="1" max="100" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Petunjuk & Instruksi Pengerjaan</label>
                    <textarea name="instructions" rows="4" placeholder="Tuliskan instruksi tugas secara rinci..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Lampiran Berkas Soal (Opsional)</label>
                    <input type="file" name="attachment" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none">
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalNewAssignment').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white text-sm font-bold shadow-xs">Publikasikan Tugas</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.getElementById(tabId).classList.remove('hidden');

            const tabs = ['tabMaterials', 'tabAssignments', 'tabDiscussions', 'tabStudents'];
            tabs.forEach(t => {
                const btn = document.getElementById('btn' + t.charAt(0).toUpperCase() + t.slice(1));
                if (t === tabId) {
                    btn.classList.add('border-brand-800', 'text-brand-950');
                    btn.classList.remove('border-transparent', 'text-slate-500');
                } else {
                    btn.classList.remove('border-brand-800', 'text-brand-950');
                    btn.classList.add('border-transparent', 'text-slate-500');
                }
            });
        }

        function toggleMaterialInputs() {
            const val = document.getElementById('materialTypeSelect').value;
            document.getElementById('inputVideoUrl').classList.toggle('hidden', val !== 'video');
            document.getElementById('inputFile').classList.toggle('hidden', val !== 'file');
        }
    </script>
</x-layouts.app>
