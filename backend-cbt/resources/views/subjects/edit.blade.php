<x-layouts.app :title="'Edit Mata Pelajaran - ' . $subject->name">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('subjects.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Daftar Mapel
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Mata Pelajaran: {{ $subject->name }}</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui nama, kode kurikulum, atau guru pengampu bidang studi.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <form action="{{ route('subjects.update', $subject) }}" method="POST">
                @csrf @method('PUT')
                <div class="p-6 sm:p-8 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Mata Pelajaran *</label>
                        <input type="text" name="name" value="{{ old('name', $subject->name) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Mapel</label>
                            <input type="text" name="code" value="{{ old('code', $subject->code) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Guru Pengampu</label>
                            <select name="teacher_id"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                                <option value="">Pilih Guru Pengampu</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $subject->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Deskripsi / Ruang Lingkup Materi</label>
                        <textarea name="description" rows="3"
                            class="w-full p-3.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">{{ old('description', $subject->description) }}</textarea>
                    </div>

                    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('subjects.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-sm shadow-xs transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>