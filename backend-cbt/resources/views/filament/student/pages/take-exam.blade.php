<x-filament-panels::page>
    <x-filament::panel-content>
        <div class="h-screen flex flex-col">
            <!-- Top Bar -->
            <div class="bg-white border-b border-gray-200 sticky top-0 z-40">
                <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <h1 class="text-lg font-semibold text-gray-900 truncate max-w-xs">
                            {{ $attempt->session->exam->name }}
                        </h1>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($attempt->status === 'in_progress')
                                bg-green-100 text-green-800
                            @else
                                bg-gray-100 text-gray-600
                            @endif">
                            {{ ucfirst($attempt->status) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Waktu Tersisa</p>
                            <p class="text-xl font-mono font-bold {{ $timeRemaining < 300 ? 'text-red-600' : 'text-gray-900' }}"
                               id="timer-display">
                                {{ $timeRemaining ? \Carbon\CarbonInterval::seconds($timeRemaining)->cascade()->forHumans(['options' => \Carbon\CarbonInterface::NO_ZERO_VALUES]) : '00:00' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600">
                                Soal: <span class="font-medium">{{ $answeredCount }}/{{ $totalQuestions }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex overflow-hidden">
                <!-- Question Navigator Sidebar -->
                <aside class="w-64 bg-gray-50 border-r border-gray-200 overflow-y-auto p-4 hidden lg:block">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Navigasi Soal</h3>
                    <div class="grid grid-cols-5 gap-1">
                        @for($i = 0; $i < $totalQuestions; $i++)
                            @php
                                $isAnswered = in_array($questions[$i]->id, $answeredQuestions);
                                $isFlagged = in_array($questions[$i]->id, $flaggedQuestions);
                                $isCurrent = $i === $currentIndex;
                                $questionNum = $i + 1;
                            @endphp
                            <button 
                                wire:click="goToQuestion({{ $i }})"
                                class="w-full h-10 text-sm font-medium rounded-lg transition-colors
                                    @if($isCurrent)
                                        bg-primary-600 text-white
                                    @elseif($isFlagged)
                                        bg-yellow-100 text-yellow-800 ring-2 ring-yellow-400
                                    @elseif($isAnswered)
                                        bg-green-100 text-green-800
                                    @else
                                        bg-white text-gray-600 hover:bg-gray-100
                                    @endif">
                                {{ $questionNum }}
                            </button>
                        @endfor
                    </div>

                    <div class="mt-4 p-3 bg-white rounded-lg border border-gray-200 text-xs text-gray-600">
                        <div class="flex gap-4">
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded bg-primary-600"></span> Saat ini
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded bg-green-100 border border-green-300"></span> Terjawab
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded bg-yellow-100 border border-yellow-300"></span> Ditandai
                            </span>
                        </div>
                    </div>
                </aside>

                <!-- Question Area -->
                <main class="flex-1 flex flex-col overflow-hidden p-6 lg:p-8">
                    @if($currentQuestion)
                        <div class="flex-1 flex flex-col overflow-y-auto">
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-semibold text-gray-900">
                                        Soal {{ $currentQuestion->number }} dari {{ $totalQuestions }}
                                    </h2>
                                    <button 
                                        wire:click="toggleFlag"
                                        class="px-3 py-1 text-sm font-medium rounded-lg transition-colors
                                            @if($isFlagged)
                                                bg-yellow-100 text-yellow-800
                                            @else
                                                bg-gray-100 text-gray-600 hover:bg-gray-200
                                            @endif
                                            flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h6a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                        </svg>
                                        Tandai
                                    </button>
                                </div>

                                <!-- Question Content -->
                                <div class="prose prose-gray max-w-none mb-6">
                                    @if($currentQuestion->image_path)
                                        <div class="mb-4">
                                            <img src="{{ asset('storage/' . $currentQuestion->image_path) }}" 
                                                 alt="Gambar Soal" 
                                                 class="max-w-full h-auto rounded-lg shadow-sm">
                                        </div>
                                    @endif
                                    
                                    <div class="question-text">
                                        {!! $currentQuestion->question_text !!}
                                    </div>
                                </div>

                                <!-- Answer Input -->
                                <div class="bg-white border border-gray-200 rounded-lg p-6">
                                    @php
                                        $type = $currentQuestion->type;
                                    @endphp

                                    @if($type === 'pg' || $type === 'pg_kompleks' || $type === 'benar_salah')
                                        <!-- Multiple Choice -->
                                        <div class="space-y-3">
                                            @foreach($currentQuestion->options as $option)
                                                <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all
                                                    @if(in_array($option->id, $selectedOptions))
                                                        border-primary-500 bg-primary-50
                                                    @else
                                                        border-gray-200 hover:border-gray-300
                                                    @endif">
                                                    <input 
                                                        type="checkbox" 
                                                        @if($type === 'benar_salah')
                                                            wire:model.defer="selectedOptions"
                                                            value="{{ $option->id }}"
                                                        @else
                                                            wire:model.defer="selectedOptions"
                                                            value="{{ $option->id }}"
                                                        @endif
                                                        class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                                    <span class="ml-3 text-gray-900">{{ $option->option_text }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                    @elseif($type === 'isian_singkat')
                                        <!-- Short Answer -->
                                        <div>
                                            <x-forms::field-wrapper label="Jawaban" required>
                                                <textarea 
                                                    wire:model.defer="answerText"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                    rows="3"
                                                    placeholder="Tulis jawaban Anda di sini..."></textarea>
                                            </x-forms::field-wrapper>
                                        </div>

                                    @elseif($type === 'menjodohkan')
                                        <!-- Matching -->
                                        <div class="space-y-4">
                                            @php
                                                $leftItems = $currentQuestion->options->whereNull('correct_match')->values();
                                                $rightItems = $currentQuestion->options->whereNotNull('correct_match')->shuffle()->values();
                                            @endphp
                                            
                                            @foreach($leftItems as $leftItem)
                                                <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                                                    <div class="flex-1">
                                                        <p class="font-medium text-gray-900">{{ $leftItem->option_text }}</p>
                                                    </div>
                                                    <div class="flex-1">
                                                        <select 
                                                            wire:model.defer="selectedOptions.{{ $leftItem->id }}"
                                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                                            <option value="">-- Pilih Pasangan --</option>
                                                            @foreach($rightItems as $rightItem)
                                                                <option value="{{ $rightItem->id }}">{{ $rightItem->option_text }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                    @elseif($type === 'esai')
                                        <!-- Essay -->
                                        <div>
                                            <x-forms::field-wrapper label="Jawaban" required>
                                                <textarea 
                                                    wire:model.defer="answerText"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                    rows="10"
                                                    placeholder="Tulis jawaban esai Anda di sini..."></textarea>
                                            </x-forms::field-wrapper>
                                            <p class="text-xs text-gray-500 mt-2">
                                                Jawaban esai akan dinilai secara manual oleh guru.
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="mt-6 flex justify-between pt-4 border-t border-gray-200">
                                    <button 
                                        wire:click="previousQuestion"
                                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50"
                                        {{ $currentIndex === 0 ? 'disabled' : '' }}>
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        Sebelumnya
                                    </button>

                                    <div class="flex gap-3">
                                        @if($currentIndex < $totalQuestions - 1)
                                            <button 
                                                wire:click="nextQuestion"
                                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center gap-1">
                                                Selanjutnya
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        @else
                                            <button 
                                                wire:click="submitExam"
                                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center gap-2 font-medium">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Kumpulkan Ujian
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex-1 flex items-center justify-center">
                            <p class="text-gray-500">Tidak ada soal tersedia</p>
                        </div>
                    @endif
                </main>
            </div>

            <!-- Heartbeat script -->
            <script>
                setInterval(() => {
                    @this.heartbeat()
                }, 30000);
            </script>
        </div>
    </x-filament::panel-content>
</x-filament-panels::page>

@push('scripts')
<script>
    // Timer countdown
    let timeRemaining = {{ $timeRemaining }};
    
    const timerDisplay = document.getElementById('timer-display');
    
    function updateTimer() {
        if (timeRemaining <= 0) {
            timerDisplay.textContent = '00:00';
            timerDisplay.classList.add('text-red-600', 'animate-pulse');
            return;
        }
        
        const hours = Math.floor(timeRemaining / 3600);
        const minutes = Math.floor((timeRemaining % 3600) / 60);
        const seconds = timeRemaining % 60;
        
        timerDisplay.textContent = 
            (hours > 0 ? String(hours).padStart(2, '0') + ':' : '') +
            String(minutes).padStart(2, '0') + ':' + 
            String(seconds).padStart(2, '0');
        
        if (timeRemaining < 300) {
            timerDisplay.classList.add('text-red-600');
        }
        
        timeRemaining--;
    }
    
    updateTimer();
    setInterval(updateTimer, 1000);
</script>
@endpush