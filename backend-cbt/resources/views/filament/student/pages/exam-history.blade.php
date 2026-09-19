<x-filament-panels::page>
    <div class="space-y-6">
        <x-card class="shadow-md">
            <x-slot name="header">
                <h2 class="text-lg font-semibold text-gray-900">
                    Riwayat Ujian Saya
                </h2>
            </x-slot>
            
            <div class="p-4">
                @if(count($attempts) > 0)
                    <div class="space-y-3">
                        @foreach($attempts as $attempt)
                            <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $attempt->session->exam->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $attempt->session->name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Diselesaikan: {{ $attempt->submitted_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    
                                    <div class="text-right">
                                        @if($attempt->result)
                                            <div class="space-y-1">
                                                <p class="font-medium text-gray-800 text-xl">
                                                    {{ $attempt->result->percentage }}%
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Nilai: {{ $attempt->result->total_score }}/{{ $attempt->result->max_possible_score }}
                                                </p>
                                            </div>
                                        @else
                                            <span class="bg-gray-200 text-gray-600 px-2 py-1 rounded text-xs">
                                                Belum Dinilai
                                            </span>
                                        @endif
                                        
                                        <div class="mt-2">
                                            <x-button 
                                                href="{{ route('student.exam.result', $attempt) }}" 
                                                variant="secondary"
                                                size="sm">
                                                Lihat Detail
                                            </x-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <p class="text-lg font-medium">Belum ada riwayat ujian</p>
                        <p class="text-sm mt-1">Anda belum pernah menyelesaikan ujian apapun.</p>
                    </div>
                @endif
            </div>
        </x-card>
    </div>
</x-filament-panels::page>