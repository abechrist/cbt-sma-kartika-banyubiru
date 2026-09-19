<x-layouts.app :title="'Manajemen Kelas'">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">Rombongan Belajar (Kelas)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Daftar tingkatan kelas, rombel siswa, dan alokasi tahun ajaran aktif di sekolah.</p>
        </div>
        <a href="{{ route('classes.create') }}" class="px-5 py-2.5 rounded-2xl bg-brand-900 hover:bg-brand-950 text-white font-extrabold text-xs shadow-md hover:shadow-lg btn-glow-brand transition-all duration-150 flex items-center gap-2">
            <span class="text-base font-black leading-none">+</span>
            <span>Tambah Kelas Baru</span>
        </a>
    </div>

    <!-- Table Card Container -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/70 text-slate-600 text-xs uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4 text-left">Nama Kelas / Rombel</th>
                        <th class="px-6 py-4 text-left">Tingkat</th>
                        <th class="px-6 py-4 text-left">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-left">Jumlah Siswa</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($classes as $class)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">{{ $class->name }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-slate-700 font-semibold whitespace-nowrap">Tingkat {{ $class->grade ?? '-' }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-slate-600 whitespace-nowrap">{{ $class->academic_year ?? 'T.A. 2026/2027' }}</td>
                        <td class="px-6 py-4 font-mono font-bold text-slate-800 whitespace-nowrap">{{ $class->students()->count() }} siswa</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border whitespace-nowrap {{ $class->is_active ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                {{ $class->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('classes.show', $class) }}" class="px-3 py-2 sm:py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                    Detail
                                </a>
                                <a href="{{ route('classes.edit', $class) }}" class="px-3 py-2 sm:py-1.5 rounded-xl bg-gold-50 hover:bg-gold-100 text-gold-900 font-bold text-xs border border-gold-200 transition">
                                    Edit
                                </a>
                                <form action="{{ route('classes.destroy', $class) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kelas ini beserta seluruh asosiasi datanya?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-2 sm:py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">Belum ada data kelas yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/40">
            {{ $classes->links() }}
        </div>
    </div>
</x-layouts.app>