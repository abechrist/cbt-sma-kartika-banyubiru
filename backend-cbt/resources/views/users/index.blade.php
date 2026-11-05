<x-layouts.app :title="'Manajemen Pengguna'">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight leading-tight">Manajemen Pengguna</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola data otorisasi akun Siswa, Dewan Guru, Pengawas Ujian (Proktor), dan Pimpinan Sekolah.</p>
        </div>
        <a href="{{ route('users.create') }}" class="px-4 py-2.5 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition flex items-center gap-2">
            <span class="text-base font-bold leading-none">+</span>
            <span>Tambah Pengguna Baru</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-3.5">
            <div class="w-full sm:w-48">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Peran / Hak Akses</label>
                <select name="role_id" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800 font-semibold focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none transition">
                    <option value="">Semua Peran</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:w-44">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rombel / Kelas</label>
                <select name="class_id" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800 font-semibold focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none transition">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pencarian Cepat</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, NISN, atau NIP..."
                    class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-700/25 outline-none transition">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-900 hover:bg-brand-950 text-white font-bold text-xs transition">
                    Filter
                </button>
                <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
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
                        <th class="px-6 py-3.5 text-left">Nama Pengguna</th>
                        <th class="px-6 py-3.5 text-left">Email Resmi</th>
                        <th class="px-6 py-3.5 text-left">NISN / NIP</th>
                        <th class="px-6 py-3.5 text-left">Hak Akses (Peran)</th>
                        <th class="px-6 py-3.5 text-left">Kelas</th>
                        <th class="px-6 py-3.5 text-left">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-3.5 font-bold text-slate-900">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-3.5 text-xs font-mono text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-3.5 font-mono text-xs font-bold text-slate-700 tabular-nums">{{ $user->nisn ?? $user->nip ?? '-' }}</td>
                        <td class="px-6 py-3.5">
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-md border
                                @switch($user->role?->name)
                                    @case('super_admin') bg-purple-50 text-purple-800 border-purple-200 @break
                                    @case('admin') bg-blue-50 text-blue-800 border-blue-200 @break
                                    @case('guru') bg-emerald-50 text-emerald-800 border-emerald-200 @break
                                    @case('siswa') bg-amber-50 text-amber-900 border-amber-200 @break
                                    @case('proktor') bg-orange-50 text-orange-800 border-orange-200 @break
                                    @case('kepala_sekolah') bg-rose-50 text-rose-800 border-rose-200 @break
                                    @case('wali_kelas') bg-teal-50 text-teal-800 border-teal-200 @break
                                    @default bg-slate-100 text-slate-700 border-slate-200
                                @endswitch
                            ">
                                {{ $user->role?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-xs font-semibold text-slate-700 whitespace-nowrap">{{ $user->class->name ?? '-' }}</td>
                        <td class="px-6 py-3.5 whitespace-nowrap">
                            <span @class([
                                'inline-flex items-center px-2.5 py-0.5 text-[11px] font-bold rounded-md border whitespace-nowrap',
                                'bg-emerald-50 text-emerald-800 border-emerald-200' => $user->is_active,
                                'bg-slate-100 text-slate-700 border-slate-200' => !$user->is_active,
                            ])>
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('users.show', $user) }}" class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                    Detail
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="px-2.5 py-1 rounded-md bg-gold-50 hover:bg-gold-100 text-gold-900 font-bold text-xs border border-gold-200 transition">
                                    Edit
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengguna ini?')">
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
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">Tidak ada pengguna yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/40">
            {{ $users->links() }}
        </div>
    </div>
</x-layouts.app>
