<x-layouts.app :title="'Tambah Kelas Baru'">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('classes.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Daftar Kelas
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Rombel / Kelas Baru</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftarkan identitas kelas baru dan periode tahun ajaran aktif.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <form action="{{ route('classes.store') }}" method="POST">
                @csrf
                <div class="p-6 sm:p-8 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Rombel / Kelas *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: X-A, XI-MIPA 1, XII-IPS 2"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tingkat Jenjang</label>
                            <input type="text" name="grade" value="{{ old('grade') }}" placeholder="Contoh: 10, 11, atau 12"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun Ajaran</label>
                            <input type="text" name="academic_year" value="{{ old('academic_year', '2026/2027') }}" placeholder="Contoh: 2026/2027"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keterangan / Deskripsi Kelas</label>
                        <textarea name="description" rows="3" placeholder="Contoh: Kelas peminatan MIPA..."
                            class="w-full p-3.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">{{ old('description') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <label class="flex items-center p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-brand-700 rounded">
                            <span class="ml-3 text-xs font-bold text-slate-800">Kelas Aktif (Terbuka untuk Ujian)</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('classes.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-sm shadow-xs transition">
                            Simpan Kelas
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>