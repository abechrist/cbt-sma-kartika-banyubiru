<x-layouts.app :title="'Tambah Pengguna Baru'">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('users.index') }}" class="text-xs font-semibold text-brand-800 hover:text-brand-900 flex items-center gap-1 mb-2">
                    ← Kembali ke Manajemen Pengguna
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Akun Pengguna Baru</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftarkan akun peserta didik, guru pendidik, proktor, atau administrator sekolah.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap sesuai ijazah/KTP"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@kartika.sch.id"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Hak Akses (Peran) *</label>
                            <select name="role_id" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-bold text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                                <option value="">Pilih Peran Akun</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NISN (Khusus Siswa)</label>
                            <input type="text" name="nisn" value="{{ old('nisn') }}" placeholder="10 Digit NISN"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIP (Khusus Guru / Staff)</label>
                            <input type="text" name="nip" value="{{ old('nip') }}" placeholder="18 Digit NIP"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kelas / Rombongan Belajar</label>
                            <select name="class_id"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                                <option value="">Pilih Kelas (Bila Siswa/Wali)</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Kelamin</label>
                            <select name="gender"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-800 font-medium focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Lahir</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Domisili</label>
                            <textarea name="address" rows="2" placeholder="Alamat lengkap siswa/guru..."
                                class="w-full p-3.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">{{ old('address') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi (Password) *</label>
                            <input type="password" name="password" required placeholder="Minimal 8 karakter"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi *</label>
                            <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 outline-none transition">
                        </div>

                        <div class="sm:col-span-2 pt-2">
                            <label class="flex items-center p-3.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition">
                                <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 text-brand-700 rounded">
                                <span class="ml-3 text-xs font-bold text-slate-800">Akun Aktif (Dapat Login ke Sistem)</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                        <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-bold text-sm shadow-xs transition">
                            Simpan Pengguna
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>