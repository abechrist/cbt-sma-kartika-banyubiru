<x-layouts.app :title="'Detail Paket Ujian - ' . $exam->name">
    <div class="max-w-5xl mx-auto">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('exams.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Daftar Paket Ujian
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $exam->name }}</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">{{ $exam->subject->name ?? 'Mata Pelajaran Umum' }} • Dibuat oleh {{ $exam->creator->name ?? 'Administrator' }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if($exam->status === 'published' || $exam->status === 'draft')
                    <form action="{{ route('exams.publish', $exam) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition"
                            @if($exam->questionCount() === 0) disabled title="Tambahkan soal terlebih dahulu" @endif>
                            Terbitkan Ujian
                        </button>
                    </form>
                @endif
                <a href="{{ route('exams.edit', $exam) }}" class="px-4 py-2 rounded-xl bg-gold-500 hover:bg-gold-600 text-white font-bold text-xs shadow-xs transition">
                    Edit Konfigurasi
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-8">
            <!-- Badges Bar -->
            <div class="p-5 border-b border-slate-200 bg-slate-50/80 flex flex-wrap items-center gap-3">
                <span class="px-3 py-1 text-xs font-bold rounded-full border
                    @switch($exam->status)
                        @case('draft') bg-slate-100 text-slate-700 border-slate-200 @break
                        @case('published') bg-blue-100 text-blue-800 border-blue-200 @break
                        @case('active') bg-emerald-100 text-emerald-800 border-emerald-200 @break
                        @case('archived') bg-rose-100 text-rose-800 border-rose-200 @break
                        @default bg-slate-100 text-slate-700 border-slate-200
                    @endswitch
                ">
                    Status: {{ ucfirst($exam->status) }}
                </span>
                <span class="px-3 py-1 text-xs font-mono font-bold rounded-lg bg-slate-100 text-slate-800 border border-slate-200">
                    Durasi: {{ $exam->duration_minutes }} Menit
                </span>
                <span class="px-3 py-1 text-xs font-mono font-bold rounded-lg bg-brand-50 text-brand-800 border border-brand-200">
                    {{ $exam->questionCount() }} Butir Soal Terpasang
                </span>
                <span class="px-3 py-1 text-xs font-mono font-bold rounded-lg bg-amber-50 text-amber-800 border border-amber-200">
                    Total Poin: {{ $exam->totalScore() }} Pts
                </span>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                @if($exam->description)
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Petunjuk / Deskripsi Ujian</h3>
                    <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-200">{{ $exam->description }}</p>
                </div>
                @endif

                <!-- Rules Grid -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Aturan Pelaksanaan Ujian</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-slate-400 block mb-1">Acak Butir Soal</span>
                            <span class="font-bold {{ $exam->randomize_questions ? 'text-emerald-700' : 'text-slate-700' }}">{{ $exam->randomize_questions ? 'Ya (Aktif)' : 'Tidak' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-slate-400 block mb-1">Acak Pilihan Opsi</span>
                            <span class="font-bold {{ $exam->randomize_options ? 'text-emerald-700' : 'text-slate-700' }}">{{ $exam->randomize_options ? 'Ya (Aktif)' : 'Tidak' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-slate-400 block mb-1">Boleh Kembali</span>
                            <span class="font-bold {{ $exam->allow_back ? 'text-emerald-700' : 'text-slate-700' }}">{{ $exam->allow_back ? 'Bebas' : 'Linear (Ketat)' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-slate-400 block mb-1">Rilis Nilai Otomatis</span>
                            <span class="font-bold {{ $exam->show_result_after ? 'text-emerald-700' : 'text-slate-700' }}">{{ $exam->show_result_after ? 'Langsung Rilis' : 'Tertutup' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Questions Table -->
                <div class="pt-4 border-t border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">Daftar Soal Dalam Paket ({{ $exam->questions->count() }})</h3>
                        <a href="{{ route('exams.edit', $exam) }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900">+ Kelola Soal</a>
                    </div>
                    @if($exam->questions->isEmpty())
                        <div class="p-8 text-center bg-slate-50 rounded-xl border border-slate-200 text-slate-400 text-sm">
                            Belum ada butir soal yang dipasang ke paket ujian ini. Klik "Edit Konfigurasi" untuk memilih soal.
                        </div>
                    @else
                        <div class="overflow-x-auto border border-slate-200 rounded-xl">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                    <tr>
                                        <th class="px-4 py-3 text-left">No</th>
                                        <th class="px-4 py-3 text-left">Konten Pertanyaan</th>
                                        <th class="px-4 py-3 text-left">Tipe</th>
                                        <th class="px-4 py-3 text-left">Kesulitan</th>
                                        <th class="px-4 py-3 text-left">Bobot</th>
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($exam->questions as $index => $question)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="px-4 py-3 font-mono text-xs text-slate-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 max-w-sm">
                                            <div class="font-medium text-slate-900 line-clamp-1">
                                                {{ Str::limit(strip_tags($question->question_text), 85) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 text-xs rounded bg-blue-50 text-blue-800 border border-blue-200 font-semibold">{{ $question->type }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-xs">
                                            <span class="px-2 py-0.5 rounded border font-semibold
                                                @switch($question->difficulty)
                                                    @case('easy') bg-emerald-50 text-emerald-700 border-emerald-200 @break
                                                    @case('medium') bg-amber-50 text-amber-700 border-amber-200 @break
                                                    @case('hard') bg-rose-50 text-rose-700 border-rose-200 @break
                                                    @default bg-slate-50 text-slate-700 border-slate-200
                                                @endswitch
                                            ">
                                                {{ ucfirst($question->difficulty) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-mono font-bold text-slate-800">{{ $question->pivot->score ?? $question->score }} pts</td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('questions.show', $question) }}" class="text-brand-800 hover:text-brand-900 font-semibold text-xs">Pratinjau Soal →</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Sessions Table -->
                <div class="pt-4 border-t border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800">Sesi Ujian yang Terjadwal ({{ $sessions->count() }})</h3>
                        <a href="{{ route('sessions.create') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900">+ Jadwalkan Sesi</a>
                    </div>
                    @if($sessions->isEmpty())
                        <div class="p-6 text-center bg-slate-50 rounded-xl border border-slate-200 text-slate-400 text-xs">
                            Belum ada jadwal sesi yang mengujikan paket ini.
                        </div>
                    @else
                        <div class="overflow-x-auto border border-slate-200 rounded-xl">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Nama Sesi</th>
                                        <th class="px-4 py-3 text-left">Jadwal Sesi</th>
                                        <th class="px-4 py-3 text-left">Ruang Lab</th>
                                        <th class="px-4 py-3 text-left">Kapasitas</th>
                                        <th class="px-4 py-3 text-left">Status</th>
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($sessions as $session)
                                    <tr class="hover:bg-slate-50/60">
                                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $session->name }}</td>
                                        <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $session->start_at->format('d/m/Y H:i') }} - {{ $session->end_at->format('H:i') }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $session->room ?? '-' }}</td>
                                        <td class="px-4 py-3 font-mono text-xs">{{ $session->max_participants }} siswa</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 text-xs font-bold rounded-full border
                                                @switch($session->status)
                                                    @case('open') @case('in_progress') bg-emerald-100 text-emerald-800 border-emerald-200 @break
                                                    @case('completed') bg-slate-100 text-slate-800 border-slate-200 @break
                                                    @default bg-amber-100 text-amber-800 border-amber-200
                                                @endswitch
                                            ">
                                                {{ $session->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('sessions.show', $session) }}" class="text-brand-800 hover:text-brand-900 font-semibold text-xs">Kelola Sesi →</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>