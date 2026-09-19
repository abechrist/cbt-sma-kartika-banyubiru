<x-layouts.app :title="'Detail Kelas - ' . $class->name">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('classes.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                ← Kembali ke Daftar Kelas
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $class->name }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tingkat {{ $class->grade ?? '-' }} • {{ $class->academic_year ?? 'T.A. 2026/2027' }} • {{ $class->students()->count() }} Siswa Terdaftar</p>
        </div>
        <a href="{{ route('classes.edit', $class) }}" class="px-4 py-2 rounded-xl bg-gold-500 hover:bg-gold-600 text-white font-bold text-xs shadow-xs transition">
            Edit Informasi Kelas
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Class Meta Info Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Informasi Rombel</h2>
            </div>
            <div class="p-6 space-y-3 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Nama Rombel</span>
                    <span class="font-bold text-slate-900">{{ $class->name }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Tingkat Jenjang</span>
                    <span class="font-bold text-slate-900">Kelas {{ $class->grade ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Tahun Ajaran</span>
                    <span class="font-mono text-slate-800">{{ $class->academic_year ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Status Aktif</span>
                    <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full border {{ $class->is_active ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                        {{ $class->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Jumlah Siswa</span>
                    <span class="font-mono font-bold text-brand-800 text-sm">{{ $class->students()->count() }} orang</span>
                </div>
                <div class="pt-2">
                    <span class="text-slate-500 block mb-1">Deskripsi</span>
                    <p class="text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-200">{{ $class->description ?? 'Tidak ada catatan deskripsi.' }}</p>
                </div>
            </div>
        </div>

        <!-- Student List Card -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-slate-50/70 flex justify-between items-center">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Daftar Siswa ({{ $class->students()->count() }})</h2>
                <a href="{{ route('users.create') }}?class_id={{ $class->id }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900">+ Tambah Siswa</a>
            </div>
            @if($class->students->isEmpty())
                <div class="p-10 text-center text-slate-400 text-sm">
                    Belum ada siswa yang dialokasikan ke kelas ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">NISN</th>
                                <th class="px-6 py-3.5 text-left">Nama Siswa</th>
                                <th class="px-6 py-3.5 text-left">Email</th>
                                <th class="px-6 py-3.5 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($class->students as $student)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-700">{{ $student->user->nisn ?? '-' }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $student->user->name }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $student->user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 text-[11px] font-bold rounded-full border {{ $student->user->is_active ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                        {{ $student->user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>