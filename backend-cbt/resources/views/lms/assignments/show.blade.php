<x-layouts.app :title="$assignment->title . ' - ' . $course->subject->name">
    <div class="max-w-5xl mx-auto py-6 sm:py-8 px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <div class="mb-4 flex items-center gap-2 text-xs font-medium text-slate-500">
            <a href="{{ route('lms.courses.index') }}" class="hover:text-brand-800 transition">Kelas Belajar</a>
            <span>/</span>
            <a href="{{ route('lms.courses.show', $course->id) }}" class="hover:text-brand-800 transition">{{ $course->subject->name }}</a>
            <span>/</span>
            <span class="text-slate-800 font-bold truncate">{{ $assignment->title }}</span>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Assignment Information Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <div class="p-6 sm:p-8 bg-slate-50/80 border-b border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                            Penugasan Terjadwal
                        </span>
                        <span class="text-xs font-mono font-bold text-slate-500">Nilai Maksimal: {{ $assignment->max_score }} Poin</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        {{ $assignment->title }}
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Dibuat oleh: <strong class="text-slate-800">{{ $assignment->creator->name ?? $course->teacher->name }}</strong>
                    </p>
                </div>

                @if($assignment->due_date)
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-right shrink-0">
                    <p class="text-[10px] uppercase font-bold text-amber-800 tracking-wider">Tenggat Waktu (Deadline)</p>
                    <p class="text-sm font-extrabold text-amber-950 font-mono mt-0.5">
                        {{ $assignment->due_date->format('d M Y, H:i') }} WIB
                    </p>
                </div>
                @endif
            </div>

            <div class="p-6 sm:p-8">
                <!-- Instructions -->
                <div class="mb-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Petunjuk & Instruksi Guru</h3>
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                        {{ $assignment->instructions ?? 'Tidak ada petunjuk khusus untuk tugas ini. Kerjakan sesuai arahan guru di kelas.' }}
                    </div>
                </div>

                <!-- Attachment file if any -->
                @if($assignment->file_attachment)
                <div class="mb-6 p-4 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-brand-800" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <span class="text-xs font-bold text-slate-800">Lampiran Lembar Kerja Guru</span>
                    </div>
                    <a href="{{ asset('storage/' . $assignment->file_attachment) }}" target="_blank" download
                        class="px-4 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs">
                        Unduh Lampiran
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- SISWA VIEW: Form Pengumpulan Tugas -->
        @if(auth()->user()->isSiswa())
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-800" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Pengumpulan Tugas Anda</span>
            </h2>

            @if($mySubmission)
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-6">
                    <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
                        <span class="text-xs font-bold text-slate-500">Status Pengumpulan:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($mySubmission->status === 'graded') bg-emerald-100 text-emerald-800 border border-emerald-200
                            @elseif($mySubmission->status === 'late') bg-rose-100 text-rose-800 border border-rose-200
                            @else bg-blue-100 text-blue-800 border border-blue-200
                            @endif
                        ">
                            @if($mySubmission->status === 'graded')
                                Sudah Dinilai: {{ $mySubmission->score }} / {{ $assignment->max_score }} Poin
                            @elseif($mySubmission->status === 'late')
                                Terkumpul Terlambat (Menunggu Koreksi)
                            @else
                                Terkumpul Tepat Waktu (Menunggu Koreksi)
                            @endif
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 font-mono mb-2">Waktu Kirim: {{ $mySubmission->submitted_at->format('d M Y, H:i') }} WIB</p>
                    @if($mySubmission->notes)
                        <p class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-200/80 mb-3">
                            <strong>Catatan Anda:</strong> {{ $mySubmission->notes }}
                        </p>
                    @endif

                    @if($mySubmission->file_path)
                        <div class="flex items-center gap-2 mb-4">
                            <a href="{{ asset('storage/' . $mySubmission->file_path) }}" target="_blank" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Lihat Berkas yang Dikumpulkan</span>
                            </a>
                        </div>
                    @endif

                    <!-- Feedback Guru -->
                    @if($mySubmission->feedback)
                        <div class="p-4 rounded-xl bg-emerald-50/80 border border-emerald-200 text-xs">
                            <p class="font-bold text-emerald-950 mb-1">Catatan Evaluasi Guru ({{ $mySubmission->grader->name ?? 'Guru' }}):</p>
                            <p class="text-emerald-900 leading-relaxed">{{ $mySubmission->feedback }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Form upload / kirim ulang -->
            <form action="{{ route('lms.assignments.submit', [$course->id, $assignment->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Unggah Berkas Jawaban (PDF, DOC, Gambar, maks 20MB)</label>
                    <input type="file" name="file" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none focus:bg-white focus:ring-2 focus:ring-brand-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Tuliskan catatan pengerjaan atau keterangan tugas..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs outline-none focus:bg-white focus:ring-2 focus:ring-brand-700"></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs shadow-xs transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ $mySubmission ? 'Kirim Pembaruan Tugas' : 'Kumpulkan Tugas Sekarang' }}</span>
                    </button>
                </div>
            </form>
        </div>
        @endif

        <!-- GURU VIEW: Daftar Pengumpulan Tugas Seluruh Siswa & Form Penilaian -->
        @if(in_array(auth()->user()->role?->name, ['super_admin', 'admin', 'guru']))
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/80 flex justify-between items-center">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Pengumpulan Tugas Siswa</h2>
                    <p class="text-xs text-slate-500">Periksa lembar tugas yang dikumpulkan dan berikan nilai serta umpan balik</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-brand-100 text-brand-900">
                    {{ $assignment->submissions->count() }} Terkumpul
                </span>
            </div>

            @if($assignment->submissions->isEmpty())
                <div class="p-10 text-center text-slate-400 text-sm">
                    Belum ada siswa yang mengumpulkan tugas ini.
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($assignment->submissions as $sub)
                    <div class="p-6 hover:bg-slate-50/50 transition">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $sub->user->name }}</h4>
                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                    <span class="font-mono">NISN: {{ $sub->user->nisn ?? '-' }}</span>
                                    <span>•</span>
                                    <span class="font-mono">{{ $sub->submitted_at?->format('d/m/Y H:i') }} WIB</span>
                                    <span>•</span>
                                    <span class="px-2 py-0.2 rounded-full font-semibold
                                        @if($sub->status === 'graded') bg-emerald-100 text-emerald-800
                                        @elseif($sub->status === 'late') bg-rose-100 text-rose-800
                                        @else bg-blue-100 text-blue-800
                                        @endif
                                    ">
                                        {{ ucfirst($sub->status) }}
                                    </span>
                                </div>
                            </div>

                            @if($sub->file_path)
                            <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" download 
                                class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs flex items-center gap-1.5 transition">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Unduh Berkas Jawaban</span>
                            </a>
                            @endif
                        </div>

                        @if($sub->notes)
                            <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 mb-4">
                                <strong>Catatan Siswa:</strong> {{ $sub->notes }}
                            </p>
                        @endif

                        <!-- Grading Form -->
                        <form action="{{ route('lms.assignments.grade', [$course->id, $assignment->id, $sub->id]) }}" method="POST" class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row items-end sm:items-center gap-3">
                            @csrf
                            <div class="w-full sm:w-36">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Skor (Maks: {{ $assignment->max_score }})</label>
                                <input type="number" step="0.5" name="score" value="{{ $sub->score }}" min="0" max="{{ $assignment->max_score }}" required placeholder="0-100" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl font-mono text-sm font-bold text-slate-900 outline-none focus:ring-1 focus:ring-brand-700">
                            </div>

                            <div class="w-full sm:flex-1">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Catatan Evaluasi / Feedback</label>
                                <input type="text" name="feedback" value="{{ $sub->feedback }}" placeholder="Catatan perbaikan atau pujian untuk siswa..." class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs outline-none focus:ring-1 focus:ring-brand-700">
                            </div>

                            <button type="submit" class="px-5 py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-xs shadow-xs shrink-0">
                                Simpan Nilai
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endif
    </div>
</x-layouts.app>
