<x-filament-panels::page>
    <div class="space-y-6">
        <x-card class="shadow-md">
            <x-slot name="header">
                <h2 class="text-lg font-semibold text-gray-900">
                    Selamat Datang, {{ auth()->user()->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    NIS: {{ auth()->user()->nisn ?? 'Belum terdaftar' }}
                </p>
            </x-slot>
            
            <div class="space-y-4 p-4">
                @if(count($activeSessions) > 0)
                    <div>
                        <h3 class="text-base font-medium text-gray-800 mb-2">
                            Ujian Sedang Berlangsung
                        </h3>
                        <div class="space-y-2">
                            @foreach($activeSessions as $session)
                                <div class="p-3 border border-green-200 rounded-lg bg-green-50">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-green-800">{{ $session->exam->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $session->name }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                                    Mulai: {{ $session->start_at->format('d M H:i') }}
                                                </span>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs ml-2">
                                                    Selesai: {{ $session->end_at->format('d M H:i') }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            @php
                                                $userAttempt = \App\Models\ExamAttempt::where('exam_session_id', $session->id)
                                                    ->where('user_id', auth()->id())
                                                    ->first();
                                            @endphp
                                            @if(!$userAttempt || $userAttempt->status === 'not_started' || $userAttempt->status === 'cancelled')
                                                <a href="{{ route('student.exam.token') }}" 
                                                   class="bg-green-600 hover:bg-green-700 text-white font-medium py-1 px-3 rounded">
                                                    Mulai Ujian
                                                </a>
                                            @elseif($userAttempt->status === 'in_progress')
                                                <a href="{{ route('student.exam.take', $userAttempt) }}" 
                                                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1 px-3 rounded">
                                                    Lanjutkan
                                                </a>
                                            @else
                                                <span class="bg-gray-200 text-gray-600 px-2 py-1 rounded text-xs">
                                                    Selesai
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if(count($upcomingSessions) > 0)
                    <div>
                        <h3 class="text-base font-medium text-gray-800 mb-2">
                            Ujian Akan Datang
                        </h3>
                        <div class="space-y-2">
                            @foreach($upcomingSessions as $session)
                                <div class="p-3 border border-blue-200 rounded-lg bg-blue-50">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-blue-800">{{ $session->exam->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $session->name }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                                    Mulai: {{ $session->start_at->format('d M H:i') }}
                                                </span>
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs ml-2">
                                                    Selesai: {{ $session->end_at->format('d M H:i') }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                                {{ $session->start_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if(count($activeSessions) === 0 && count($upcomingSessions) === 0)
                    <div class="p-4 text-center text-gray-500">
                        Tidak ada ujian yang tersedia saat ini.
                    </div>
                @endif
            </div>
        </x-card>

        @if(count($completedExams) > 0)
            <x-card class="shadow-md">
                <x-slot name="header">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Riwayat Ujian Saya
                    </h2>
                </x-slot>
                
                <div class="p-4">
                    <div class="space-y-3">
                        @foreach($completedExams as $attempt)
                            <div class="p-3 border border-indigo-200 rounded-lg bg-indigo-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-indigo-800">{{ $attempt->session->exam->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $attempt->session->name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Diselesaikan: {{ $attempt->submitted_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        @if($attempt->result)
                                            <div class="space-y-1">
                                                <p class="font-medium text-indigo-800 text-xl">
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
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-card>
        @endif
    </div>
</x-filament-panels::page>