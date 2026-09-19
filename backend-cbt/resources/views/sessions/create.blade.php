<x-layouts.app :title="'Jadwalkan Sesi Ujian Baru'">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('sessions.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Jadwal Sesi
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Jadwalkan Sesi Ujian Baru</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tentukan waktu pelaksanaan gelombang ujian di ruang laboratorium.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <form action="{{ route('sessions.store') }}" method="POST">
                @csrf
                <div class="p-6 sm:p-8 space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Paket Ujian *</label>
                        <select name="exam_id" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                            <option value="">Pilih Paket Ujian</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Identitas Sesi *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Gelombang 1 - Lab Komputer A"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jadwal Mulai *</label>
                            <input type="datetime-local" name="start_at" value="{{ old('start_at') }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jadwal Selesai *</label>
                            <input type="datetime-local" name="end_at" value="{{ old('end_at') }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ruang / Tempat Pelaksanaan</label>
                            <input type="text" name="room" value="{{ old('room') }}" placeholder="Contoh: Laboratorium CBT 1"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kapasitas Maksimal Siswa *</label>
                            <input type="number" name="max_participants" value="{{ old('max_participants', 40) }}" min="1" max="150" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Awalan Kode Token (Prefix)</label>
                        <input type="text" name="token_prefix" value="{{ old('token_prefix', 'EXAM') }}" maxlength="10"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono uppercase font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        <p class="text-xs text-slate-400 mt-1">Sistem otomatis menghasilkan token berformat: [PREFIX]-XXXXXXXX</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Petunjuk / Tata Tertib Ruang</label>
                        <textarea name="instructions" rows="3" placeholder="Tuliskan petunjuk khusus pengawas ruang..."
                            class="w-full p-3.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">{{ old('instructions') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <label class="flex items-center p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="allow_resume" value="1" {{ old('allow_resume', true) ? 'checked' : '' }} class="h-4 w-4 text-brand-700 rounded">
                            <span class="ml-3 text-xs font-semibold text-slate-800">Izinkan Resume Bila Terputus</span>
                        </label>
                        <label class="flex items-center p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="auto_submit_on_timeout" value="1" {{ old('auto_submit_on_timeout', true) ? 'checked' : '' }} class="h-4 w-4 text-brand-700 rounded">
                            <span class="ml-3 text-xs font-semibold text-slate-800">Kumpulkan Otomatis Saat Waktu Habis</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('sessions.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-sm shadow-xs transition">
                            Simpan Sesi Ujian
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>