<x-layouts.app :title="'Manajemen Modul Ajar (RPP)'">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">Daftar RPP & Modul Ajar</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Kelola modul ajar Kurikulum Merdeka, alokasi waktu, dan sinkronisasi ke kelas daring LMS & CBT.</p>
        </div>
        <a href="{{ route('rpps.create') }}" class="px-5 py-2.5 rounded-2xl bg-brand-900 hover:bg-brand-950 text-white font-extrabold text-xs shadow-md hover:shadow-lg btn-glow-brand transition-all duration-150 flex items-center gap-2">
            <span class="text-base font-black leading-none">+</span>
            <span>Impor RPP Baru</span>
        </a>
    </div>

    <!-- Filter Bar (Modern Bento Shell) -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200/90 mb-8">
        <form method="GET" class="flex flex-wrap items-end gap-3.5">
            <div class="w-full sm:w-56">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Mata Pelajaran</label>
                <select name="subject_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs text-slate-800 font-semibold focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                    <option value="">Semua Mapel</option>
                    @if(isset($subjects))
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="w-full sm:w-44">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Status RPP</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs text-slate-800 font-semibold focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="integrated" {{ request('status') == 'integrated' ? 'selected' : '' }}>Integrated</option>
                </select>
            </div>
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Pencarian Topik & Materi</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari topik, materi, atau tahun ajaran..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-2xl text-xs text-slate-800 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-2xl bg-brand-900 hover:bg-brand-950 text-white font-extrabold text-xs shadow-md transition">
                    Filter
                </button>
                <a href="{{ route('rpps.index') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if($rpps->isEmpty())
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center mx-auto mb-4 font-bold shadow-2xs">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h2 class="text-lg font-black text-slate-900">Belum Ada RPP yang Sesuai</h2>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Tidak ditemukan data RPP untuk kriteria yang dipilih atau belum ada RPP yang diimpor.</p>
            <div class="mt-5 flex items-center justify-center gap-3">
                <a href="{{ route('rpps.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-brand-900 hover:bg-brand-950 text-white font-extrabold text-xs shadow-md transition btn-glow-brand">
                    <span>Impor RPP Baru</span>
                    <span class="text-gold-300">→</span>
                </a>
                @if(request()->hasAny(['search', 'subject_id', 'status']))
                    <a href="{{ route('rpps.index') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Bersihkan Filter
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @foreach($rpps as $rpp)
                <div class="bento-card p-6 flex flex-col justify-between group hover:border-brand-600">
                    <div>
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex-1">
                                <h2 class="text-base font-extrabold text-slate-900 group-hover:text-brand-900 transition leading-snug">
                                    {{ $rpp->topic }}
                                </h2>
                                <p class="text-xs font-semibold text-slate-500 mt-1">
                                    {{ $rpp->subject->name ?? '-' }} • <span class="text-brand-800">Kelas {{ $rpp->classGroup->name ?? '-' }}</span>
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full border shrink-0
                                @if($rpp->status === 'draft') bg-amber-50 text-amber-800 border-amber-200
                                @elseif($rpp->status === 'published') bg-emerald-50 text-emerald-800 border-emerald-200
                                @elseif($rpp->status === 'integrated') bg-blue-50 text-blue-800 border-blue-200
                                @else bg-slate-100 text-slate-700 border-slate-200
                                @endif">
                                {{ ucfirst($rpp->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-3 p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100 text-xs text-slate-600 mb-5">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tahun Ajaran</span>
                                <span class="font-mono font-bold text-slate-800">{{ $rpp->academic_year }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Semester</span>
                                <span class="font-semibold text-slate-800">{{ $rpp->semester }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Alokasi Waktu</span>
                                <span class="font-semibold text-slate-800">{{ $rpp->time_allocation }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Status Integrasi</span>
                                <span class="font-semibold {{ $rpp->integrated_at ? 'text-emerald-700' : 'text-slate-500' }}">
                                    {{ $rpp->integrated_at ? $rpp->integrated_at->format('d/m/y H:i') : 'Belum Terhubung' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <a href="{{ route('rpps.show', $rpp) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            Lihat Detail
                        </a>
                        <a href="{{ route('rpps.edit', $rpp) }}" class="px-3.5 py-1.5 rounded-xl bg-gold-50 hover:bg-gold-100 text-gold-900 font-bold text-xs border border-gold-200 transition">
                            Edit
                        </a>
                        <form action="{{ route('rpps.destroy', $rpp) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus RPP ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Modern Pagination Container Card -->
        <div class="p-4 sm:p-5 bg-white rounded-3xl border border-slate-200/90 shadow-sm">
            {{ $rpps->links() }}
        </div>
    @endif
</x-layouts.app>