<x-layouts.app :title="'Supervisi Kurikulum — ' . $teacher->name">
    <!-- Navigasi Kembali & Header Profil Guru -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <a href="{{ route('curriculum.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition mb-3">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span>Kembali ke Matriks 30 Guru</span>
            </a>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    {{ $teacher->name }}
                </h1>
                @if($teacher->role?->name === 'koordinator_kurikulum')
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-teal-50 text-teal-900 border border-teal-300">
                        Koordinator Kurikulum
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        Guru Pengampu
                    </span>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 font-mono">
                Username: <strong>{{ $teacher->nip ?? '-' }}</strong> • Email: {{ $teacher->email }}
            </p>
            <div class="flex flex-wrap gap-1.5 mt-2">
                @foreach($teacher->subjects as $subject)
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-brand-50 text-brand-900 border border-brand-200">
                        {{ $subject->code }} — {{ $subject->name }}
                    </span>
                @endforeach
            </div>
        </div>

        @if(in_array(auth()->user()->role?->name, ['super_admin', 'admin']) && $teacher->email !== 'wakakur@kartika.sch.id')
            <div class="shrink-0">
                <form action="{{ route('curriculum.assign-coordinator') }}" method="POST">
                    @csrf
                    <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                    @if($teacher->role?->name === 'koordinator_kurikulum')
                        <input type="hidden" name="action" value="revoke">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-xs border border-rose-200 transition">
                            Kembalikan ke Guru Pengampu Biasa
                        </button>
                    @else
                        <input type="hidden" name="action" value="assign">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-950 text-gold-300 font-bold text-xs border border-brand-800 transition">
                            Tetapkan sebagai Koordinator Kurikulum
                        </button>
                    @endif
                </form>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-bold flex items-center gap-2.5">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- 4 Kartu Ringkasan Kinerja Guru -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bento-card p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Beban Kelas LMS</span>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">
                {{ $activeCourses }}<span class="text-base text-slate-400">/{{ $teacher->teachingCourses->count() }}</span>
            </p>
            <p class="text-[11px] text-slate-500 mt-1">Kelas telah terisi materi/tugas</p>
        </div>
        <div class="bento-card p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Modul Ajar & RPP</span>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $teacher->rpps->count() }}</p>
            <p class="text-[11px] text-emerald-700 font-semibold mt-1">
                {{ $teacher->rpps->where('review_status', 'approved')->count() }} disetujui • {{ $teacher->rpps->where('review_status', 'revision_needed')->count() }} revisi
            </p>
        </div>
        <div class="bento-card p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Konten Pembelajaran</span>
            <p class="text-2xl sm:text-3xl font-extrabold text-brand-900 font-mono tabular-nums mt-1.5">{{ $totalMaterials }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Modul Materi & {{ $totalAssignments }} Tugas LMS</p>
        </div>
        <div class="bento-card p-4 sm:p-5">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Asesmen CBT</span>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-mono tabular-nums mt-1.5">{{ $questionsBySubject->sum('total') }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Butir Soal & {{ $teacher->createdExams->count() }} Paket Ujian</p>
        </div>
    </div>

    <!-- PILAR 1: Supervisi & Validasi Modul Ajar (RPP) -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="text-base font-black text-slate-900">1. Supervisi Perencanaan (Modul Ajar & RPP)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Berikan persetujuan atau catatan revisi terhadap dokumen RPP yang disusun oleh {{ $teacher->name }}.</p>
            </div>
        </div>

        <div class="divide-y divide-slate-200">
            @forelse($teacher->rpps as $rpp)
                <div class="p-5 sm:p-6">
                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                        <div class="space-y-2 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-brand-50 text-brand-900 border border-brand-200">
                                    {{ $rpp->subject->name ?? '-' }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                    Kelas {{ $rpp->classGroup->name ?? '-' }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                    Status Sistem: {{ ucfirst($rpp->status) }}
                                </span>
                                @if($rpp->review_status === 'approved')
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        ✓ Supervisi: Disetujui
                                    </span>
                                @elseif($rpp->review_status === 'revision_needed')
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                        ! Supervisi: Perlu Revisi
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Menunggu Review Koordinator
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-base sm:text-lg font-black text-slate-900">
                                <a href="{{ route('rpps.show', $rpp) }}" class="hover:text-brand-800 hover:underline">{{ $rpp->topic }}</a>
                            </h3>

                            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600">
                                <span>Alokasi Waktu: <strong>{{ $rpp->time_allocation }}</strong></span>
                                <span>•</span>
                                <span>Integrasi LMS: <strong>{{ $rpp->materials_count }} Pertemuan</strong></span>
                                <span>•</span>
                                <span>Pemetaan CBT: <strong>{{ $rpp->assessments_count }} Tujuan Pembelajaran (TP)</strong></span>
                            </div>

                            @if($rpp->review_notes)
                                <div class="mt-3 p-3 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-700">
                                    <div class="font-bold text-slate-900 mb-0.5">
                                        Catatan Supervisi Terakhir
                                        @if($rpp->reviewer)
                                            oleh {{ $rpp->reviewer->name }}
                                        @endif
                                        @if($rpp->reviewed_at)
                                            ({{ $rpp->reviewed_at->format('d/m/Y H:i') }})
                                        @endif:
                                    </div>
                                    <p class="leading-relaxed">{{ $rpp->review_notes }}</p>
                                </div>
                            @endif

                            <div class="pt-2">
                                <a href="{{ route('rpps.show', $rpp) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-800 hover:text-brand-950">
                                    <span>Inspeksi Isi Lengkap RPP, Pertemuan LMS & Kisi-kisi CBT</span>
                                    <span>→</span>
                                </a>
                            </div>
                        </div>

                        <!-- Form Keputusan Supervisi RPP (Opsi 1) -->
                        <div class="w-full lg:w-96 bg-slate-50 rounded-xl border border-slate-200 p-4 shrink-0">
                            <form action="{{ route('curriculum.rpps.review', $rpp) }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                        Keputusan Supervisi RPP
                                    </label>
                                    <select name="review_status" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-700/25">
                                        <option value="pending" @selected($rpp->review_status === 'pending')>Menunggu Review</option>
                                        <option value="approved" @selected($rpp->review_status === 'approved')>✓ Setujui RPP (Layak Dilaksanakan)</option>
                                        <option value="revision_needed" @selected($rpp->review_status === 'revision_needed')>! Minta Revisi / Perbaikan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                        Catatan Supervisi Kurikulum
                                    </label>
                                    <textarea name="review_notes" rows="2" placeholder="Tulis masukan/umpan balik untuk guru pengampu..."
                                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-700/25">{{ $rpp->review_notes }}</textarea>
                                </div>
                                <button type="submit" class="w-full py-2 px-3 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition">
                                    Simpan Hasil Supervisi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs sm:text-sm">
                    Guru ini belum mengunggah dokumen Modul Ajar / RPP pada semester berjalan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- PILAR 2: Pemantauan Pelaksanaan Kelas Pembelajaran (LMS) -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
        <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="text-base font-black text-slate-900">2. Pelaksanaan Kelas Pembelajaran Daring (LMS)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh rombongan belajar yang diampu beserta keterisian materi ajar, penugasan, dan interaksi diskusi.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-[11px] uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-5 py-3.5 text-left">Kelas / Rombel</th>
                        <th class="px-5 py-3.5 text-left">Mata Pelajaran</th>
                        <th class="px-5 py-3.5 text-center">Modul Materi</th>
                        <th class="px-5 py-3.5 text-center">Tugas & Penilaian</th>
                        <th class="px-5 py-3.5 text-center">Diskusi Kelas</th>
                        <th class="px-5 py-3.5 text-center">Status Kelas</th>
                        <th class="px-5 py-3.5 text-right">Inspeksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teacher->teachingCourses as $course)
                        @php
                            $matCount = $course->materials->count();
                            $asgCount = $course->assignments->count();
                            $discCount = $course->discussions->count();
                            $isActiveCourse = $matCount > 0 || $asgCount > 0;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                Kelas {{ $course->studentClass->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 font-medium">
                                {{ $course->subject->name ?? '-' }}
                                <span class="text-xs text-slate-400 font-mono">({{ $course->subject->code ?? '' }})</span>
                            </td>
                            <td class="px-5 py-3.5 text-center font-mono font-bold text-slate-900">
                                {{ $matCount }} Modul
                            </td>
                            <td class="px-5 py-3.5 text-center font-mono text-xs text-slate-700">
                                <strong>{{ $asgCount }}</strong> Tugas
                            </td>
                            <td class="px-5 py-3.5 text-center font-mono text-xs text-slate-600">
                                {{ $discCount }} Topik
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                @if($isActiveCourse)
                                    <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Aktif Terisi
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Belum Ada Materi
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('lms.courses.show', $course) }}" class="text-xs font-bold text-brand-800 hover:text-brand-950">
                                    Buka Ruang Kelas →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400 text-xs">
                                Belum ada kelas pembelajaran yang terdaftar untuk guru ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PILAR 3: Kesiapan Bank Soal & Evaluasi Ujian (CBT) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="text-sm font-black text-slate-900 mb-3">3A. Kontribusi Bank Soal per Mata Pelajaran</h2>
            <div class="space-y-2.5">
                @forelse($questionsBySubject as $qStat)
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-900">{{ $qStat->subject->name ?? 'Mata Pelajaran' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">Kode: {{ $qStat->subject->code ?? '-' }}</div>
                        </div>
                        <span class="px-3 py-1 rounded-lg bg-brand-50 border border-brand-200 text-brand-900 font-mono font-bold text-xs">
                            {{ $qStat->total }} Butir Soal
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada butir soal yang dibuat oleh guru ini.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="text-sm font-black text-slate-900 mb-3">3B. Paket Ujian CBT yang Disusun</h2>
            <div class="space-y-2.5">
                @forelse($teacher->createdExams as $exam)
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                        <div>
                            <div class="text-xs font-bold text-slate-900">{{ $exam->name }}</div>
                            <div class="text-[11px] text-slate-500">
                                {{ $exam->subject->name ?? '-' }} • {{ $exam->questions_count }} Soal • {{ $exam->duration_minutes }} Menit
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $exam->status === 'published' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                            <a href="{{ route('exams.show', $exam) }}" class="text-xs font-bold text-brand-800 hover:underline">Lihat →</a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada paket ujian CBT yang disusun oleh guru ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
