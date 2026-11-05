<x-layouts.app :title="'Manajemen Sesi Ujian'">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight leading-tight">Jadwal &amp; Sesi Ujian</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Pengaturan jadwal sesi pelaksanaan ujian, alokasi ruang/kelas, dan distribusi token akses.</p>
        </div>
        <a href="{{ route('sessions.create') }}" class="px-4 py-2.5 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition flex items-center gap-2">
            <span class="text-base font-bold leading-none">+</span>
            <span>Jadwalkan Sesi Baru</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3.5">
            <div class="w-full sm:w-64">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Paket Ujian</label>
                <select name="exam_id" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800 font-semibold focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none transition">
                    <option value="">Semua Ujian</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-48">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Pelaksanaan</label>
                <select name="status" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800 font-semibold focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none transition">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition">
                    Filter
                </button>
                <a href="{{ route('sessions.index') }}" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
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
                        <th class="px-6 py-3.5 text-left">Nama Sesi</th>
                        <th class="px-6 py-3.5 text-left">Paket Ujian</th>
                        <th class="px-6 py-3.5 text-left">Jadwal Pelaksanaan</th>
                        <th class="px-6 py-3.5 text-left">Ruang / Kelas</th>
                        <th class="px-6 py-3.5 text-left">Kapasitas</th>
                        <th class="px-6 py-3.5 text-left">Status</th>
                        <th class="px-6 py-3.5 text-left">Token</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3.5 font-bold text-slate-900">
                            <a href="{{ route('sessions.show', $session) }}" class="hover:text-brand-900 transition">
                                {{ $session->name }}
                            </a>
                        </td>
                        <td class="px-6 py-3.5 text-slate-600 font-semibold">{{ $session->exam->name }}</td>
                        <td class="px-6 py-3.5 font-mono text-xs text-slate-700 tabular-nums">
                            {{ $session->start_at->format('d/m/Y H:i') }} - {{ $session->end_at->format('H:i') }} WIB
                        </td>
                        <td class="px-6 py-3.5 text-xs font-bold text-slate-800">{{ $session->room ?? '-' }}</td>
                        <td class="px-6 py-3.5 font-mono text-xs text-slate-700 font-semibold tabular-nums">{{ $session->max_participants }} siswa</td>
                        <td class="px-6 py-3.5">
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-md border
                                @switch($session->status)
                                    @case('open') @case('in_progress') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                    @case('completed') bg-slate-100 text-slate-800 border-slate-200 @break
                                    @default bg-amber-50 text-amber-800 border-amber-200
                                @endswitch
                            ">
                                {{ $statuses[$session->status] ?? $session->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 font-mono text-xs tabular-nums">
                            <span class="px-2.5 py-0.5 rounded-md bg-gold-50 text-gold-900 font-bold border border-gold-200">
                                {{ $session->tokens->where('is_active', true)->count() }} Aktif
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <a href="{{ route('sessions.show', $session) }}" class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                    Detail
                                </a>
                                <a href="{{ route('sessions.edit', $session) }}" class="px-2.5 py-1 rounded-md bg-gold-50 hover:bg-gold-100 text-gold-900 font-bold text-xs border border-gold-200 transition">
                                    Edit
                                </a>
                                @if($session->status === 'scheduled' || $session->status === 'open')
                                    <form action="{{ route('sessions.open', $session) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 transition">
                                            Buka
                                        </button>
                                    </form>
                                    <a href="{{ route('sessions.print-tokens', $session) }}" target="_blank" class="px-2.5 py-1 rounded-md bg-brand-50 hover:bg-brand-100 text-brand-900 font-bold text-xs border border-brand-200 transition">
                                        Cetak Token
                                    </a>
                                @endif
                                @if($session->status === 'open' || $session->status === 'in_progress')
                                    <form action="{{ route('sessions.close', $session) }}" method="POST" class="inline" onsubmit="return confirm('Tutup sesi ujian ini sekarang?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-md bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-xs border border-amber-200 transition">
                                            Tutup
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('sessions.destroy', $session) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sesi ini?')">
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
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-sm">Belum ada sesi ujian yang terjadwal.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/40">
            {{ $sessions->links() }}
        </div>
    </div>
</x-layouts.app>
