<x-layouts.app :title="'Detail RPP - ' . $rpp->topic">
    @php
        $className = str_starts_with(strtolower($rpp->classGroup->name ?? ''), 'kelas') 
            ? $rpp->classGroup->name 
            : 'Kelas ' . ($rpp->classGroup->name ?? '-');
    @endphp

    <!-- Back Navigation & Actions -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('rpps.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition mb-3">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                <span>Kembali ke Daftar RPP</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                {{ $rpp->topic }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                {{ $rpp->subject->name ?? '-' }} • <span class="font-bold text-brand-900">{{ $className }}</span>
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="px-3.5 py-1.5 text-xs font-black rounded-full border shadow-2xs
                @if($rpp->status === 'draft') bg-amber-50 text-amber-800 border-amber-200
                @elseif($rpp->status === 'published') bg-emerald-50 text-emerald-800 border-emerald-200
                @elseif($rpp->status === 'integrated') bg-blue-50 text-blue-800 border-blue-200
                @else bg-slate-100 text-slate-700 border-slate-200
                @endif">
                {{ ucfirst($rpp->status) }}
            </span>
            <a href="{{ route('rpps.edit', $rpp) }}" class="px-4 py-2 rounded-xl bg-gold-50 hover:bg-gold-100 text-gold-900 font-bold text-xs border border-gold-200 transition">
                Edit RPP
            </a>
            <form action="{{ route('rpps.destroy', $rpp) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus RPP ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Container with Generous Padding -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-6 sm:p-8">
        <!-- Modern Segmented Tabs Bar -->
        <div class="bg-slate-100/90 p-1.5 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row gap-1.5 sm:gap-2 mb-8">
            <button type="button" onclick="switchRppTab('overview')" id="tab-btn-overview"
                class="tab-btn flex-1 py-2.5 px-4 rounded-xl text-xs font-bold transition-all duration-150 flex items-center justify-center gap-2 bg-white text-slate-900 shadow-sm border border-slate-200/90">
                <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Overview</span>
            </button>
            <button type="button" onclick="switchRppTab('lms')" id="tab-btn-lms"
                class="tab-btn flex-1 py-2.5 px-4 rounded-xl text-xs font-bold transition-all duration-150 flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900 hover:bg-white/60">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>LMS Integration</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">
                    {{ $rpp->materials->count() }}
                </span>
            </button>
            <button type="button" onclick="switchRppTab('cbt')" id="tab-btn-cbt"
                class="tab-btn flex-1 py-2.5 px-4 rounded-xl text-xs font-bold transition-all duration-150 flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900 hover:bg-white/60">
                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>CBT Integration</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                    {{ $rpp->assessments->count() }}
                </span>
            </button>
        </div>

        <!-- Tab Panels Container -->
        <div>
            <!-- TAB 1: OVERVIEW -->
            <div id="panel-overview" class="tab-panel space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Bento Box: Informasi RPP -->
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6">
                        <h2 class="text-base font-black text-slate-900 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-brand-600"></span>
                            <span>Informasi RPP</span>
                        </h2>
                        <dl class="space-y-3.5 text-xs">
                            <div class="flex items-center justify-between py-2 border-b border-slate-200/60">
                                <dt class="font-extrabold text-slate-500 uppercase tracking-wider">Mata Pelajaran:</dt>
                                <dd class="font-bold text-slate-900">{{ $rpp->subject->name ?? '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-slate-200/60">
                                <dt class="font-extrabold text-slate-500 uppercase tracking-wider">Kelas:</dt>
                                <dd class="font-bold text-slate-900">{{ $className }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-slate-200/60">
                                <dt class="font-extrabold text-slate-500 uppercase tracking-wider">Topik Utama:</dt>
                                <dd class="font-bold text-slate-900">{{ $rpp->topic }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-slate-200/60">
                                <dt class="font-extrabold text-slate-500 uppercase tracking-wider">Alokasi Waktu:</dt>
                                <dd class="font-bold text-slate-900">{{ $rpp->time_allocation }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-slate-200/60">
                                <dt class="font-extrabold text-slate-500 uppercase tracking-wider">Tahun Akademik:</dt>
                                <dd class="font-bold text-slate-900 font-mono">{{ $rpp->academic_year }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <dt class="font-extrabold text-slate-500 uppercase tracking-wider">Semester:</dt>
                                <dd class="font-bold text-slate-900">{{ $rpp->semester }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Bento Box: Integrasi Status -->
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6 flex flex-col justify-between">
                        <div>
                            <h2 class="text-base font-black text-slate-900 mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                <span>Integrasi Status</span>
                            </h2>
                            <div class="space-y-4">
                                <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between gap-3">
                                    <div>
                                        <span class="text-xs font-extrabold text-slate-700 block">LMS Integration:</span>
                                        <span class="text-[11px] text-slate-500">Ruang materi belajar daring</span>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-bold rounded-full border
                                        {{ $rpp->materials->isEmpty() ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200' }}">
                                        {{ $rpp->materials->isEmpty() ? 'Belum terintegrasi' : 'Terintegrasi (' . $rpp->materials->count() . ' materi)' }}
                                    </span>
                                </div>

                                <div class="p-4 bg-white rounded-xl border border-slate-200/80 shadow-2xs flex items-center justify-between gap-3">
                                    <div>
                                        <span class="text-xs font-extrabold text-slate-700 block">CBT Integration:</span>
                                        <span class="text-[11px] text-slate-500">Pemetaan bank soal asesmen</span>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-bold rounded-full border
                                        {{ $rpp->assessments->isEmpty() ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200' }}">
                                        {{ $rpp->assessments->isEmpty() ? 'Belum terintegrasi' : 'Terintegrasi (' . $rpp->assessments->count() . ' asesmen)' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($rpp->integrated_at)
                            <div class="mt-6 pt-4 border-t border-slate-200/70 text-[11px] text-slate-500 flex items-center justify-between">
                                <span>Waktu Sinkronisasi Terakhir:</span>
                                <span class="font-mono font-bold text-slate-700">{{ $rpp->integrated_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TAB 2: LMS INTEGRATION -->
            <div id="panel-lms" class="tab-panel hidden space-y-6">
                <div>
                    <h2 class="text-lg font-black text-slate-900 tracking-tight leading-snug">Integrasi ke LMS (Learning Materials)</h2>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Daftar materi pembelajaran dan aktivitas penugasan mandiri siswa yang diselaraskan dari silabus RPP.</p>
                </div>
                
                @if($rpp->materials->isEmpty())
                    <div class="text-center py-12 p-8 bg-slate-50/70 border border-slate-200/80 rounded-2xl">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Belum Ada Materi LMS yang Terintegrasi</p>
                        <p class="text-xs text-slate-500 mt-1">Materi pembelajaran akan otomatis terbentuk ketika dokumen RPP disinkronkan ke ruang kelas.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($rpp->materials as $material)
                            <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6 shadow-2xs hover:shadow-xs transition space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/70 pb-3">
                                    <h3 class="text-sm sm:text-base font-black text-slate-900">
                                        {{ $material->judul_topik }}
                                    </h3>
                                    <span class="px-3 py-1 text-xs font-bold bg-blue-100/80 text-blue-800 border border-blue-200/80 rounded-xl shrink-0 self-start sm:self-auto">
                                        Pertemuan {{ $material->pertemuan_ke }}
                                    </span>
                                </div>
                                <div class="p-4 bg-white rounded-xl border border-slate-200/80">
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Rencana Aktivitas Pembelajaran</span>
                                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">{{ $material->deskripsi_aktivitas }}</p>
                                </div>
                                
                                @if($material->learningMaterial)
                                    <div class="p-4 bg-emerald-50/70 border border-emerald-200/80 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                        <div>
                                            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Tautan Materi Aktif</span>
                                            <p class="font-bold text-emerald-950 mt-0.5">{{ $material->learningMaterial->title }}</p>
                                        </div>
                                        <div class="font-mono text-emerald-700 text-[11px] shrink-0 font-bold">
                                            ID: #{{ $material->learningMaterial->id }}
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 bg-amber-50 text-amber-800 border border-amber-200/80 rounded-xl text-xs font-semibold flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Learning material digital belum di-generate ke database modul LMS.</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- TAB 3: CBT INTEGRATION -->
            <div id="panel-cbt" class="tab-panel hidden space-y-6">
                <div>
                    <h2 class="text-lg font-black text-slate-900 tracking-tight leading-snug">Integrasi ke CBT (Assessments)</h2>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Pemetaan Tujuan Pembelajaran (TP), indikator soal, tingkat kesulitan, dan paket ujian terhubung.</p>
                </div>
                
                @if($rpp->assessments->isEmpty())
                    <div class="text-center py-12 p-8 bg-slate-50/70 border border-slate-200/80 rounded-2xl">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700">Belum Ada Asesmen CBT yang Terintegrasi</p>
                        <p class="text-xs text-slate-500 mt-1">Paket soal evaluasi terstandar akan terhubung secara otomatis setelah pemetaan TP.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($rpp->assessments as $assessment)
                            <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-6 shadow-2xs hover:shadow-xs transition space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/70 pb-3">
                                    <div class="flex-1">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block font-mono">{{ $assessment->tp_id }}</span>
                                        <h3 class="text-sm sm:text-base font-black text-slate-900 mt-0.5">
                                            {{ $assessment->deskripsi_tp }}
                                        </h3>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-extrabold rounded-full border shrink-0 self-start sm:self-auto
                                        @if($assessment->tingkat_kesulitan === 'Mudah') bg-emerald-50 text-emerald-800 border-emerald-200
                                        @elseif($assessment->tingkat_kesulitan === 'Sedang') bg-amber-50 text-amber-800 border-amber-200
                                        @elseif($assessment->tingkat_kesulitan === 'Sukar') bg-rose-50 text-rose-800 border-rose-200
                                        @else bg-slate-100 text-slate-700 border-slate-200
                                        @endif">
                                        {{ $assessment->tingkat_kesulitan }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-4 bg-white rounded-xl border border-slate-200/80 text-xs">
                                    <div>
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Format Butir Soal</span>
                                        <span class="font-bold text-slate-800">{{ $assessment->tipe_soal }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Alokasi Rekomendasi Soal</span>
                                        <span class="font-bold text-slate-800 font-mono">{{ $assessment->jumlah_soal_direkomendasikan }} Butir</span>
                                    </div>
                                    @if($assessment->kata_kunci_indokator_soal)
                                        <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Kata Kunci Indikator</span>
                                            <span class="font-medium text-slate-700">{{ $assessment->kata_kunci_indokator_soal }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($assessment->exam)
                                    <div class="p-4 bg-blue-50/70 border border-blue-200/80 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                        <div>
                                            <span class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block">Paket Ujian CBT Terkait</span>
                                            <p class="font-bold text-blue-950 mt-0.5">{{ $assessment->exam->name }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full border
                                                @if($assessment->exam->status === \App\Models\Exam::STATUS_DRAFT) bg-amber-100 text-amber-800 border-amber-200
                                                @elseif($assessment->exam->status === \App\Models\Exam::STATUS_PUBLISHED) bg-emerald-100 text-emerald-800 border-emerald-200
                                                @elseif($assessment->exam->status === \App\Models\Exam::STATUS_ACTIVE) bg-blue-100 text-blue-800 border-blue-200
                                                @else bg-slate-100 text-slate-700 border-slate-200
                                                @endif">
                                                {{ ucfirst($assessment->exam->status) }}
                                            </span>
                                            <a href="{{ route('exams.show', $assessment->exam) }}" class="px-3 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition">
                                                Lihat Ujian →
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 bg-slate-100 text-slate-600 border border-slate-200 rounded-xl text-xs font-semibold">
                                        Paket ujian terstandar belum dikaitkan dengan tujuan pembelajaran ini.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Script Tab Navigation (Vanilla JS) -->
    <script>
        function switchRppTab(tabName) {
            // Sembunyikan semua panel
            const panels = document.querySelectorAll('.tab-panel');
            panels.forEach(p => p.classList.add('hidden'));

            // Tampilkan panel yang dipilih
            const activePanel = document.getElementById('panel-' + tabName);
            if (activePanel) {
                activePanel.classList.remove('hidden');
            }

            // Atur gaya tombol tab
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => {
                btn.className = 'tab-btn flex-1 py-2.5 px-4 rounded-xl text-xs font-bold transition-all duration-150 flex items-center justify-center gap-2 text-slate-600 hover:text-slate-900 hover:bg-white/60';
            });

            const activeBtn = document.getElementById('tab-btn-' + tabName);
            if (activeBtn) {
                activeBtn.className = 'tab-btn flex-1 py-2.5 px-4 rounded-xl text-xs font-bold transition-all duration-150 flex items-center justify-center gap-2 bg-white text-slate-900 shadow-sm border border-slate-200/90';
            }

            // Perbarui parameter tab di URL tanpa reload halaman
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', url);
        }

        // Jalankan saat dokumen dimuat
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = urlParams.get('tab') || 'overview';
            switchRppTab(initialTab);
        });
    </script>
</x-layouts.app>