<x-layouts.app :title="'Mata Pelajaran'">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">Mata Pelajaran Kurikulum</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Daftar mata pelajaran, kode bidang studi, dan penugasan guru pengampu utama.</p>
        </div>
        <a href="{{ route('subjects.create') }}" class="px-5 py-2.5 rounded-2xl bg-brand-900 hover:bg-brand-950 text-white font-extrabold text-xs shadow-md hover:shadow-lg btn-glow-brand transition-all duration-150 flex items-center gap-2">
            <span class="text-base font-black leading-none">+</span>
            <span>Tambah Mata Pelajaran</span>
        </a>
    </div>

    <!-- Table Card Container -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/70 text-slate-600 text-xs uppercase tracking-wider font-bold">
                    <tr>
                        <th class="px-6 py-4 text-left">Kode</th>
                        <th class="px-6 py-4 text-left">Nama Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left">Guru Pengampu Utama</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subjects as $subject)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-6 py-4 font-mono font-black text-brand-900 text-xs whitespace-nowrap">{{ $subject->code ?? '-' }}</td>
                        <td class="px-6 py-4 font-bold text-slate-900">{{ $subject->name }}</td>
                        <td class="px-6 py-4 text-slate-600 text-xs font-semibold whitespace-nowrap">{{ $subject->teacher->name ?? 'Belum ditentukan' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full border whitespace-nowrap {{ $subject->is_active ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                {{ $subject->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('subjects.edit', $subject) }}" class="px-3 py-2 sm:py-1.5 rounded-xl bg-gold-50 hover:bg-gold-100 text-gold-900 font-bold text-xs border border-gold-200 transition">
                                    Edit
                                </a>
                                <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="inline" onsubmit="return confirm('Hapus mata pelajaran ini?')">
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
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">Belum ada mata pelajaran yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/40">
            {{ $subjects->links() }}
        </div>
    </div>
</x-layouts.app>