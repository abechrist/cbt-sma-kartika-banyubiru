<x-layouts.app :title="'Edit Sesi Ujian - ' . $session->name">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('sessions.show', $session) }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Detail Sesi
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Sesi: {{ $session->name }}</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui jadwal pelaksanaan, ruang ujian, dan parameter sesi.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <form action="{{ route('sessions.update', $session) }}" method="POST">
                @csrf @method('PUT')
                <div class="p-6 sm:p-8 space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Paket Ujian *</label>
                        <select name="exam_id" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ old('exam_id', $session->exam_id) == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Sesi *</label>
                        <input type="text" name="name" value="{{ old('name', $session->name) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jadwal Mulai *</label>
                            <input type="datetime-local" name="start_at" value="{{ old('start_at', $session->start_at->format('Y-m-d\TH:i')) }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jadwal Selesai *</label>
                            <input type="datetime-local" name="end_at" value="{{ old('end_at', $session->end_at->format('Y-m-d\TH:i')) }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ruang Pelaksanaan</label>
                            <input type="text" name="room" value="{{ old('room', $session->room) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kapasitas Maksimal Siswa *</label>
                            <input type="number" name="max_participants" value="{{ old('max_participants', $session->max_participants) }}" min="1" max="150" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Awalan Token</label>
                        <input type="text" name="token_prefix" value="{{ old('token_prefix', $session->token_prefix) }}" maxlength="10"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono uppercase font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Instruksi Ujian</label>
                        <textarea name="instructions" rows="3"
                            class="w-full p-3.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">{{ old('instructions', $session->instructions) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <label class="flex items-center p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="allow_resume" value="1" {{ old('allow_resume', $session->allow_resume) ? 'checked' : '' }} class="h-4 w-4 text-brand-700 rounded">
                            <span class="ml-3 text-xs font-semibold text-slate-800">Izinkan Resume Saat Terputus</span>
                        </label>
                        <label class="flex items-center p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="auto_submit_on_timeout" value="1" {{ old('auto_submit_on_timeout', $session->auto_submit_on_timeout) ? 'checked' : '' }} class="h-4 w-4 text-brand-700 rounded">
                            <span class="ml-3 text-xs font-semibold text-slate-800">Auto Submit Saat Waktu Habis</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('sessions.show', $session) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition">
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