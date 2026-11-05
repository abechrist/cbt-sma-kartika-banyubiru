<x-layouts.app :title="'Supervisi & Pemantauan Kurikulum'">
    <!-- Header Supervisi Kurikulum -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 mb-6 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-brand-50 border border-brand-200 text-brand-900 text-[11px] font-bold uppercase tracking-wider mb-2">
                <span>Supervisi Akademik & Kurikulum</span>
                <span>•</span>
                <span>T.A. 2026/2027</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 leading-tight">
                Dashboard Pemantauan Koordinator Kurikulum
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-3xl leading-relaxed">
                Pantau ketuntasan perencanaan pembelajaran (<strong>Modul Ajar / RPP</strong>), pelaksanaan kelas daring (<strong>LMS</strong>), dan kesiapan evaluasi ujian (<strong>CBT</strong>) seluruh guru SMA Kartika III-1 Banyubiru.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('rpps.index') }}" class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs border border-slate-200 transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Semua Dokumen RPP</span>
            </a>
            <a href="{{ route('lms.courses.index') }}" class="px-3.5 py-2 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Direktori Kelas LMS</span>
            </a>
        </div>
    </div>

    <!-- Panel Penetapan Guru Koordinator Kurikulum (Khusus Admin / Super Admin) -->
    @if(in_array(auth()->user()->role?->name, ['super_admin', 'admin']))
    <div class="bg-brand-950 text-white rounded-xl border border-brand-800 p-4 sm:p-5 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-gold-400 text-brand-950">Pengaturan Jabatan</span>
                    <h2 class="text-sm sm:text-base font-bold text-white">Penetapan Guru Koordinator Kurikulum (Wakakur)</h2>
                </div>
                <p class="text-xs text-brand-200 leading-relaxed">
                    Pilih salah satu dari 30 guru mata pelajaran untuk diberikan hak akses <strong>Koordinator Kurikulum</strong>. Guru yang ditetapkan tetap dapat mengajar kelasnya sekaligus memantau kurikulum seluruh guru.
                </p>
                <div class="pt-1 flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-semibold text-brand-300">Pejabat Aktif:</span>
                    @forelse($coordinators as $coord)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-brand-900 border border-brand-700 text-xs font-bold text-gold-300">
                            <span>{{ $coord->name }} ({{ $coord->nip ?? $coord->email }})</span>
                            @if($coord->email !== 'wakakur@kartika.sch.id')
                                <form action="{{ route('curriculum.assign-coordinator') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="teacher_id" value="{{ $coord->id }}">
                                    <input type="hidden" name="action" value="revoke">
                                    <button type="submit" title="Kembalikan menjadi Guru Pengampu biasa" class="ml-1 text-rose-300 hover:text-rose-200 font-black">×</button>
                                </form>
                            @endif
                        </span>
                    @empty
                        <span class="text-xs text-brand-300 italic">Belum ada guru yang ditetapkan</span>
                    @endforelse
                </div>
            </div>

            <form action="{{ route('curriculum.assign-coordinator') }}" method="POST" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0">
                @csrf
                <input type="hidden" name="action" value="assign">
                <label for="assign_teacher_id" class="sr-only">Pilih Guru Koordinator Kurikulum</label>
                <select name="teacher_id" id="assign_teacher_id" required
                    class="px-3 py-2 rounded-lg bg-brand-900 border border-brand-700 text-white text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-gold-400">
                    <option value="">— Pilih Guru dari 30 Guru Pengampu —</option>
                    @foreach($allTeachersForAssign as $teacherOption)
                        <option value="{{ $teacherOption->id }}" @selected($teacherOption->role?->name === 'koordinator_kurikulum')>
                            {{ $teacherOption->name }} ({{ $teacherOption->subjects->pluck('code')->join(', ') ?: 'Guru' }})
                            {{ $teacherOption->role?->name === 'koordinator_kurikulum' ? '✓ [Koordinator]' : '' }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-lg bg-gold-400 hover:bg-gold-500 text-brand-950 font-black text-xs transition shrink-0">
                    Tetapkan Koordinator
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- 4 Kartu KPI Utama (3 Pilar Kurikulum) -->
    @php
        $compliantCount = $teachersCollection->filter(fn ($t) => $t->metrics['compliance_status'] === 'lengkap')->count();
        $partialCount = $teachersCollection->filter(fn ($t) => $t->metrics['compliance_status'] === 'sebagian')->count();
        $emptyCount = $teachersCollection->filter(fn ($t) => $t->metrics['compliance_status'] === 'belum')->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <!-- KPI 1: Perencanaan RPP -->
        <div class="bento-card p-4 sm:p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">1. Perencanaan (RPP)</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">{{ $rppTeacherRate }}% Guru</span>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums">{{ $totalRpps }} <span class="text-xs font-semibold text-slate-500 font-sans">Dokumen</span></p>
            <div class="mt-2 flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                <span class="text-emerald-700">{{ $approvedRpps }} Disetujui</span>
                <span class="text-slate-300">•</span>
                <span class="text-amber-700">{{ $pendingReviewRpps }} Antrean</span>
                @if($revisionRpps > 0)
                    <span class="text-slate-300">•</span>
                    <span class="text-rose-700">{{ $revisionRpps }} Revisi</span>
                @endif
            </div>
        </div>

        <!-- KPI 2: Pelaksanaan LMS -->
        <div class="bento-card p-4 sm:p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">2. Pelaksanaan (LMS)</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">{{ $lmsActivityRate }}% Kelas Aktif</span>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-brand-900 font-mono tabular-nums">{{ $coursesWithMaterials }}<span class="text-base text-slate-400">/{{ $totalCourses }}</span></p>
            <p class="text-[11px] font-medium text-slate-500 mt-2">
                {{ $totalMaterials }} modul materi & {{ $totalAssignments }} penugasan kelas
            </p>
        </div>

        <!-- KPI 3: Evaluasi CBT -->
        <div class="bento-card p-4 sm:p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">3. Evaluasi (CBT)</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">{{ $totalExams }} Paket Ujian</span>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums">{{ $totalQuestions }} <span class="text-xs font-semibold text-slate-500 font-sans">Butir Soal</span></p>
            <p class="text-[11px] font-medium text-slate-500 mt-2">
                {{ $pendingEssayGrading }} jawaban esai menunggu koreksi guru
            </p>
        </div>

        <!-- KPI 4: Status Kepatuhan 30 Guru -->
        <div class="bento-card p-4 sm:p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-600">Kepatuhan {{ $totalTeachers }} Guru</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">Semester Ganjil</span>
            </div>
            <div class="grid grid-cols-3 gap-2 pt-1">
                <div class="p-2 rounded-lg bg-emerald-50 border border-emerald-200 text-center">
                    <div class="text-lg font-black text-emerald-900 font-mono tabular-nums">{{ $compliantCount }}</div>
                    <div class="text-[10px] font-bold text-emerald-700">Lengkap</div>
                </div>
                <div class="p-2 rounded-lg bg-amber-50 border border-amber-200 text-center">
                    <div class="text-lg font-black text-amber-900 font-mono tabular-nums">{{ $partialCount }}</div>
                    <div class="text-[10px] font-bold text-amber-700">Sebagian</div>
                </div>
                <div class="p-2 rounded-lg bg-rose-50 border border-rose-200 text-center">
                    <div class="text-lg font-black text-rose-900 font-mono tabular-nums">{{ $emptyCount }}</div>
                    <div class="text-[10px] font-bold text-rose-700">Belum Mulai</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keterlaksanaan Kurikulum per Tingkat Kelas (Kelas X, XI, XII) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4 mb-6">
        @foreach($gradeSummaries as $gradeStat)
            <div class="bg-white rounded-xl border border-slate-200 p-4 sm:p-5">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h2 class="text-sm font-black text-slate-900">{{ $gradeStat['label'] }}</h2>
                        <p class="text-[11px] text-slate-500">{{ $gradeStat['classes_count'] }} Rombel • {{ $gradeStat['courses_count'] }} Kelas Mapel</p>
                    </div>
                    <span class="text-sm font-mono font-extrabold text-brand-900 bg-brand-50 border border-brand-200 px-2.5 py-1 rounded-lg">
                        {{ $gradeStat['coverage_pct'] }}%
                    </span>
                </div>
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden my-2.5">
                    <div class="h-full bg-brand-700 rounded-full transition-all" style="width: {{ max(4, $gradeStat['coverage_pct']) }}%"></div>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-600 pt-1">
                    <span><strong>{{ $gradeStat['rpps_count'] }}</strong> RPP Tersusun</span>
                    <span><strong>{{ $gradeStat['materials_count'] }}</strong> Materi LMS</span>
                    <span><strong>{{ $gradeStat['active_courses_count'] }}/{{ $gradeStat['courses_count'] }}</strong> Kelas Aktif</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filter & Matriks Pemantauan 30 Guru Pengampu -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-black text-slate-900">Matriks Keterlaksanaan Kurikulum Guru ({{ $teachersCollection->count() }} Guru)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Klik <strong>Supervisi Detail</strong> pada baris guru untuk memeriksa RPP, materi pertemuan LMS, dan bank soal.</p>
                </div>

                <!-- Form Filter -->
                <form method="GET" action="{{ route('curriculum.index') }}" class="flex flex-wrap items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / username guru..."
                        aria-label="Cari nama atau username guru"
                        class="px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-700/25 focus:border-brand-700">

                    <select name="subject_id" aria-label="Filter Mata Pelajaran"
                        class="px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand-700/25">
                        <option value="">Semua Mapel</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected((string) request('subject_id') === (string) $subject->id)>
                                {{ $subject->code }} — {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="grade" aria-label="Filter Tingkat Kelas"
                        class="px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand-700/25">
                        <option value="">Semua Tingkat</option>
                        <option value="10" @selected(request('grade') === '10')>Kelas X</option>
                        <option value="11" @selected(request('grade') === '11')>Kelas XI</option>
                        <option value="12" @selected(request('grade') === '12')>Kelas XII</option>
                    </select>

                    <select name="compliance" aria-label="Filter Status Kepatuhan"
                        class="px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-700 font-medium focus:outline-none focus:ring-2 focus:ring-brand-700/25">
                        <option value="">Semua Status</option>
                        <option value="lengkap" @selected(request('compliance') === 'lengkap')>Lengkap (RPP + LMS)</option>
                        <option value="sebagian" @selected(request('compliance') === 'sebagian')>Sebagian Terisi</option>
                        <option value="belum" @selected(request('compliance') === 'belum')>Belum Mengisi</option>
                    </select>

                    <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'subject_id', 'grade', 'compliance']))
                        <a href="{{ route('curriculum.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-4 py-3.5 text-left">No</th>
                        <th class="px-4 py-3.5 text-left">Nama Guru & Username</th>
                        <th class="px-4 py-3.5 text-left">Mapel & Beban Kelas</th>
                        <th class="px-4 py-3.5 text-center">1. RPP / Modul Ajar</th>
                        <th class="px-4 py-3.5 text-center">2. Pelaksanaan LMS</th>
                        <th class="px-4 py-3.5 text-center">3. Evaluasi CBT</th>
                        <th class="px-4 py-3.5 text-center">Status Kurikulum</th>
                        <th class="px-4 py-3.5 text-right">Supervisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teachersCollection as $idx => $teacher)
                        @php $m = $teacher->metrics; @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 font-mono text-xs text-slate-500 tabular-nums">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900">{{ $teacher->name }}</span>
                                    @if($teacher->role?->name === 'koordinator_kurikulum')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-900 border border-teal-300">Koordinator</span>
                                    @endif
                                </div>
                                <div class="text-xs font-mono text-slate-500 mt-0.5">
                                    {{ $teacher->nip ?? '-' }} • {{ $teacher->email }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex flex-wrap gap-1 mb-1">
                                    @foreach($teacher->subjects as $subj)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-brand-50 text-brand-900 border border-brand-200">
                                            {{ $subj->code }} ({{ $subj->name }})
                                        </span>
                                    @endforeach
                                </div>
                                <div class="text-xs text-slate-600">
                                    <strong class="text-slate-800">{{ $m['courses_count'] }} Kelas:</strong>
                                    {{ $m['class_names']->take(8)->join(', ') }}{{ $m['class_names']->count() > 8 ? ' +' . ($m['class_names']->count() - 8) . ' lainnya' : '' }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="font-mono font-bold text-slate-900 text-sm tabular-nums">{{ $m['rpps_count'] }} RPP</div>
                                @if($m['rpps_count'] > 0)
                                    <div class="text-[11px] mt-0.5">
                                        @if($m['revision_rpps_count'] > 0)
                                            <span class="text-rose-700 font-bold">{{ $m['revision_rpps_count'] }} perlu revisi</span>
                                        @elseif($m['approved_rpps_count'] > 0)
                                            <span class="text-emerald-700 font-semibold">{{ $m['approved_rpps_count'] }} disetujui</span>
                                        @else
                                            <span class="text-amber-700 font-semibold">{{ $m['pending_rpps_count'] }} menunggu review</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400">Belum unggah</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="text-xs font-bold text-slate-900 font-mono tabular-nums">
                                    {{ $m['active_courses_count'] }}/{{ $m['courses_count'] }} Kelas Aktif
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $m['materials_count'] }} Materi • {{ $m['assignments_count'] }} Tugas
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="text-xs font-bold text-slate-900 font-mono tabular-nums">
                                    {{ $m['questions_count'] }} Soal
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $m['exams_count'] }} Paket Ujian
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($m['compliance_status'] === 'lengkap')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Terkendali
                                    </span>
                                @elseif($m['compliance_status'] === 'sebagian')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Perlu Dilengkapi
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                        Belum Mengisi
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('curriculum.teachers.show', $teacher) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-900 font-bold text-xs border border-brand-200 transition">
                                    <span>Supervisi Detail</span>
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400 text-sm">
                                Tidak ada data guru yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
