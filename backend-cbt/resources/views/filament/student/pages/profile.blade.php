<x-filament-panels::page>
    <div class="max-w-2xl mx-auto space-y-6">
        <x-card class="shadow-md">
            <x-slot name="header">
                <h2 class="text-lg font-semibold text-gray-900">
                    Profil Saya
                </h2>
            </x-slot>
            
            <div class="p-6">
                @if(session('message'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-600 text-sm">{{ session('message') }}</p>
                    </div>
                @endif
                
                <form wire:submit.prevent="updateProfile">
                    <x-forms::field-wrapper label="Nama Lengkap" required>
                        <input 
                            type="text" 
                            wire:model="name"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            value="{{ old('name', $name) }}"
                        />
                        @error('name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </x-forms::field-wrapper>
                    
                    <x-forms::field-wrapper label="Email" required>
                        <input 
                            type="email" 
                            wire:model="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            value="{{ old('email', $email) }}"
                        />
                        @error('email')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </x-forms::field-wrapper>
                    
                    <x-forms::field-wrapper label="NISN">
                        <input 
                            type="text" 
                            wire:model="nisn"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            value="{{ old('nisn', $nisn) }}"
                        />
                        @error('nisn')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </x-forms::field-wrapper>
                    
                    <x-forms::field-wrapper label="NIP (untuk guru/staff)">
                        <input 
                            type="text" 
                            wire:model="nip"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            value="{{ old('nip', $nip) }}"
                            {{ !auth()->user()->hasRole('guru') && !auth()->user()->hasRole('admin') ? 'disabled' : '' }}
                        />
                        @error('nip')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </x-forms::field-wrapper>
                    
                    <x-forms::field-wrapper label="Nomor Telepon">
                        <input 
                            type="tel" 
                            wire:model="phone"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            value="{{ old('phone', $phone) }}"
                        />
                        @error('phone')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </x-forms::field-wrapper>
                    
                    <x-forms::field-wrapper label="Jenis Kelamin" required>
                        <select 
                            wire:model="gender"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('gender', $gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender', $gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </x-forms::field-wrapper>
                    
                    <x-forms::field-wrapper label="Alamat">
                        <textarea 
                            wire:model="address"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            rows="3"
                            value="{{ old('address', $address) }}">{{ old('address', $address) }}</textarea>
                        @error('address')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </x-forms::field-wrapper>
                    
                    <x-forms::field-wrapper>
                        <x-forms::field-label>
                            Akun Aktif
                        </x-forms::field-label>
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model="is_active"
                                class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                {{ $is_active ? 'checked' : '' }}
                            >
                            <span class="ml-2 text-gray-700">Akun aktif dan dapat digunakan untuk masuk</span>
                        </div>
                    </x-forms::field-wrapper>
                    
                    <hr class="my-6">
                    
                    <x-forms::field-wrapper label="Password Baru">
                        <input 
                            type="password" 
                            wire:model="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Biarkan kosong jika tidak ingin mengubah password"
                        />
                        @error('password')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </x-forms::field-wrapper>
                    
                    <x-forms::field-wrapper label="Konfirmasi Password Baru">
                        <input 
                            type="password" 
                            wire:model="password_confirmation"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Ulangi password baru untuk konfirmasi"
                        />
                        @error('password_confirmation')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </x-forms::field-wrapper>
                    
                    <div class="flex justify-end pt-4">
                        <x-button 
                            type="button" 
                            wire:click="$refresh()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 mr-2">
                            Batal
                        </x-button>
                        <x-button 
                            wire:submit="updateProfile" 
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                            Simpan Perubahan
                        </x-button>
                    </div>
                </form>
            </div>
        </x-card>
        
        <div class="text-center text-sm text-gray-500 mt-8">
            <p>Last updated: {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>
</x-filament-panels::page>