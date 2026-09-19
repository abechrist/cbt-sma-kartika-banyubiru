<x-filament-panels::page>
    <div class="max-w-2xl mx-auto space-y-6">
        <x-card class="shadow-md">
            <x-slot name="header">
                <h2 class="text-lg font-semibold text-gray-900">
                    Masukkan Token Ujian
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Token diberikan oleh pengawas ujian
                </p>
            </x-slot>
            
            <div class="p-6">
                @if($error)
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-600 text-sm">{{ $error }}</p>
                    </div>
                @endif

                @if(!$sessionInfo)
                    <form wire:submit="validateToken" class="space-y-4">
                        <x-forms::field-wrapper label="Token Ujian" required>
                            <input 
                                type="text" 
                                wire:model="token"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-center text-2xl tracking-widest uppercase"
                                placeholder="XXXXXX-XXXXXX"
                                autofocus
                            />
                        </x-forms::field-wrapper>
                        
                        <x-button type="submit" class="w-full">
                            Validasi Token
                        </x-button>
                    </form>
                @else
                    <div class="space-y-4">
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <h3 class="font-semibold text-green-800 mb-2">Token Valid!</h3>
                            <p class="text-sm text-gray-600">Silakan periksa informasi ujian di bawah ini sebelum memulai.</p>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-gray-100">
                                    <tr>
                                        <td class="px-4 py-2 bg-gray-50 font-medium text-gray-600">Ujian</td>
                                        <td class="px-4 py-2">{{ $sessionInfo['exam_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2 bg-gray-50 font-medium text-gray-600">Sesi</td>
                                        <td class="px-4 py-2">{{ $sessionInfo['session_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2 bg-gray-50 font-medium text-gray-600">Durasi</td>
                                        <td class="px-4 py-2">{{ $sessionInfo['duration'] }} menit</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2 bg-gray-50 font-medium text-gray-600">Waktu Mulai</td>
                                        <td class="px-4 py-2">{{ $sessionInfo['start_at'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2 bg-gray-50 font-medium text-gray-600">Waktu Selesai</td>
                                        <td class="px-4 py-2">{{ $sessionInfo['end_at'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if(!empty($sessionInfo['instructions']))
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <h4 class="font-medium text-yellow-800 mb-2">Instruksi:</h4>
                                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $sessionInfo['instructions'] }}</p>
                            </div>
                        @endif

                        <div class="flex gap-3">
                            <x-button variant="secondary" wire:click="$set('sessionInfo', null)" class="flex-1">
                                Batal
                            </x-button>
                            <x-button wire:click="startExam" class="flex-1 bg-green-600 hover:bg-green-700">
                                Mulai Ujian
                            </x-button>
                        </div>
                    </div>
                @endif
            </div>
        </x-card>

        <div class="text-center text-sm text-gray-500">
            <p>Jika mengalami kesulitan, hubungi pengawas ujian.</p>
        </div>
    </div>
</x-filament-panels::page>