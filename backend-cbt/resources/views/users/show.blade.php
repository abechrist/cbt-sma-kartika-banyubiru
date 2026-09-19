<x-layouts.app :title="'Profil Pengguna - ' . $user->name">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('users.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                ← Kembali ke Manajemen Pengguna
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $user->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">NISN/NIP: <span class="font-mono font-bold text-slate-800">{{ $user->nisn ?? $user->nip ?? '-' }}</span> • Terdaftar {{ $user->created_at?->format('d M Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 rounded-xl bg-gold-500 hover:bg-gold-600 text-white font-bold text-xs shadow-xs transition">
                Edit Profil Akun
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Info Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Biodata Pribadi</h2>
            </div>
            <div class="p-6 space-y-3 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Nama Lengkap</span>
                    <span class="font-bold text-slate-900 text-right">{{ $user->name }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Alamat Email</span>
                    <span class="font-mono font-semibold text-slate-800 text-right">{{ $user->email }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">NISN / NIP</span>
                    <span class="font-mono font-bold text-slate-900">{{ $user->nisn ?? $user->nip ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Jenis Kelamin</span>
                    <span class="font-semibold text-slate-800">{{ $user->gender === 'L' ? 'Laki-laki' : ($user->gender === 'P' ? 'Perempuan' : '-') }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Nomor Telepon</span>
                    <span class="font-mono text-slate-800">{{ $user->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Tanggal Lahir</span>
                    <span class="font-mono text-slate-800">{{ $user->birth_date ? $user->birth_date->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="pt-2">
                    <span class="text-slate-500 block mb-1">Alamat Lengkap</span>
                    <p class="text-slate-800 leading-relaxed bg-slate-50 p-2.5 rounded-lg border border-slate-200">{{ $user->address ?? 'Belum ada alamat' }}</p>
                </div>
            </div>
        </div>

        <!-- Role & Assignment Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Otorisasi & Penugasan</h2>
            </div>
            <div class="p-6 space-y-4 text-xs">
                <div>
                    <span class="text-slate-500 block mb-1">Hak Akses Peran</span>
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full border inline-block
                        @switch($user->role?->name)
                            @case('super_admin') bg-purple-100 text-purple-800 border-purple-200 @break
                            @case('admin') bg-blue-100 text-blue-800 border-blue-200 @break
                            @case('guru') bg-emerald-100 text-emerald-800 border-emerald-200 @break
                            @case('siswa') bg-amber-100 text-amber-900 border-amber-200 @break
                            @case('proktor') bg-orange-100 text-orange-800 border-orange-200 @break
                            @case('kepala_sekolah') bg-rose-100 text-rose-800 border-rose-200 @break
                            @case('wali_kelas') bg-teal-100 text-teal-800 border-teal-200 @break
                            @default bg-slate-100 text-slate-700 border-slate-200
                        @endswitch
                    ">
                        {{ ucfirst(str_replace('_', ' ', $user->role?->name ?? 'Tanpa Peran')) }}
                    </span>
                </div>

                <div class="flex justify-between py-2 border-t border-b border-slate-100">
                    <span class="text-slate-500">Kelas / Rombel</span>
                    <span class="font-bold text-slate-900">{{ $user->class->name ?? 'Bukan Siswa' }}</span>
                </div>

                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Status Akun</span>
                    <span class="px-2 py-0.5 text-[11px] font-bold rounded-full border {{ $user->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-rose-100 text-rose-800 border-rose-200' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-500 font-bold uppercase text-[10px] block mb-2">Mata Pelajaran yang Diampu</span>
                    <div class="space-y-1">
                        @forelse($user->subjects as $subject)
                            <div class="px-2.5 py-1 rounded bg-slate-50 border border-slate-200 text-slate-800 font-medium">
                                {{ $subject->name }}
                            </div>
                        @empty
                            <p class="text-slate-400 italic">Tidak ada mata pelajaran terkait.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Log Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Aktivitas Terakhir</h2>
            </div>
            <div class="p-6 divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse($user->activityLogs->take(10) as $log)
                <div class="py-3 first:pt-0 last:pb-0 flex items-start gap-2.5">
                    <span class="mt-1 w-2 h-2 rounded-full shrink-0 bg-brand-600"></span>
                    <div class="flex-1">
                        <p class="text-xs font-bold text-slate-900">{{ $log->action }}</p>
                        <p class="text-[11px] text-slate-600 mt-0.5">{{ $log->description }}</p>
                        <p class="text-[10px] text-slate-400 font-mono mt-1">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-slate-400 text-xs text-center py-6">Belum ada riwayat aktivitas yang tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>