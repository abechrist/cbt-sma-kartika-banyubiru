<x-layouts.app :title="'Manajemen Paket Ujian'">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight leading-tight">Daftar Paket Ujian</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola paket ujian terstruktur, alokasi waktu pengerjaan, dan publikasi jadwal sesi ujian.</p>
        </div>
        <a href="{{ route('exams.create') }}" class="px-4 py-2.5 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition flex items-center gap-2">
            <span class="text-base font-bold leading-none">+</span>
            <span>Buat Paket Ujian Baru</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3.5">
            <div class="w-full sm:w-56">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                <select name="subject_id" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800 font-semibold focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none transition">
                    <option value="">Semua Mata Pelajaran</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-44">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Publikasi</label>
                <select name="status" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800 font-semibold focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none transition">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Cari Nama Ujian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama paket ujian..."
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none transition">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition">
                    Filter
                </button>
                <a href="{{ route('exams.index') }}" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-3.5 text-left">Nama Paket Ujian</th>
                        <th class="px-6 py-3.5 text-left">Mata Pelajaran</th>
                        <th class="px-6 py-3.5 text-left">Durasi</th>
                        <th class="px-6 py-3.5 text-left">Jumlah Butir</th>
                        <th class="px-6 py-3.5 text-left">Status</th>
                        <th class="px-6 py-3.5 text-left">Pembuat</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($exams as $exam)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3.5">
                            <a href="{{ route('exams.show', $exam) }}" class="font-bold text-slate-900 hover:text-brand-900 transition">
                                {{ $exam->name }}
                            </a>
                            @if($exam->description)
                                <p class="text-xs text-slate-400 mt-1 line-clamp-1 leading-relaxed">{{ $exam->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-slate-600 font-semibold whitespace-nowrap">{{ $exam->subject->name ?? '-' }}</td>
                        <td class="px-6 py-3.5 font-mono text-xs text-slate-700 font-bold whitespace-nowrap tabular-nums">{{ $exam->duration_minutes }} menit</td>
                        <td class="px-6 py-3.5 font-mono font-bold text-brand-900 whitespace-nowrap tabular-nums">{{ $exam->questionCount() }} butir</td>
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-bold rounded-md border whitespace-nowrap
                                @switch($exam->status)
                                    @case('draft') bg-slate-100 text-slate-700 border-slate-200 @break
                                    @case('published') bg-blue-50 text-blue-800 border-blue-200 @break
                                    @case('active') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                    @case('archived') bg-rose-50 text-rose-800 border-rose-200 @break
                                    @default bg-slate-100 text-slate-800 border-slate-200
                                @endswitch
                            ">
                                {{ $statuses[$exam->status] ?? $exam->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-xs text-slate-500 font-medium whitespace-nowrap">{{ $exam->creator->name ?? '-' }}</td>
                        <td class="px-6 py-3.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('exams.show', $exam) }}" class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                    Detail
                                </a>
                                <a href="{{ route('exams.edit', $exam) }}" class="px-2.5 py-1 rounded-md bg-gold-50 hover:bg-gold-100 text-gold-900 font-bold text-xs border border-gold-200 transition">
                                    Edit
                                </a>
                                @if($exam->status === 'published' && $exam->questionCount() > 0)
                                    <form action="{{ route('exams.publish', $exam) }}" method="POST" class="inline" onsubmit="return confirm('Terbitkan ujian ini?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition">
                                            Terbitkan
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus paket ujian ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">Belum ada paket ujian yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/40">
            {{ $exams->links() }}
        </div>
    </div>
</x-layouts.app>
