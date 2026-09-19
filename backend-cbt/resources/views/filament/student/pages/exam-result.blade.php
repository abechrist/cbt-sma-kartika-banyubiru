<x-filament-panels::page>
    <div class="max-w-4xl mx-auto space-y-6 py-8">
        <x-card class="shadow-md">
            <x-slot name="header">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $attempt->status === 'auto_submitted' ? 'Ujian Selesai Otomatis' : 'Hasil Ujian' }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $attempt->session->exam->name }} - {{ $attempt->session->name }}
                    </p>
                </div>
            </x-slot>
            
            <div class="p-6">
                @if($attempt->status === 'auto_submitted')
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-yellow-800">Ujian dikumpulkan secara otomatis</h3>
                                <p class="text-sm text-gray-600">
                                    Waktu ujian telah habis sebelum Anda mengumpulkan jawaban.
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">
                                Auto-Submit
                            </span>
                        </div>
                    </div>
                @endif

                @if($result)
                    <div class="space-y-6">
                        <!-- Score Summary -->
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-24 h-24 mb-4
                                @if($result->percentage >= 85)
                                    bg-green-100 text-green-800
                                @elseif($result->percentage >= 70)
                                    bg-blue-100 text-blue-800
                                @elseif($result->percentage >= 55)
                                    bg-yellow-100 text-yellow-800
                                @else
                                    bg-red-100 text-red-800
                                @endif">
                                <span class="text-3xl font-bold">{{ $result->percentage }}%</span>
                            </div>
                            
                            <h3 class="text-xl font-semibold text-gray-900">
                                {{ $result->grading_status === 'partial' ? 'Hasil Sementara' : 'Nilai Akhir' }}
                            </h3>
                            
                            <p class="text-gray-600 mt-2">
                                <span class="font-medium">{{ $result->total_score }}</span> dari 
                                <span class="font-medium">{{ $result->max_possible_score }}</span> poin
                            </p>
                        </div>

                        <!-- Detailed Breakdown -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h4 class="font-medium text-gray-800 mb-2">Detail Jawaban</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Benar:</span>
                                        <span class="font-medium text-green-600">{{ $result->correct_count }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Salah:</span>
                                        <span class="font-medium text-red-600">{{ $result->incorrect_count }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Tidak Dijawab:</span>
                                        <span class="font-medium text-gray-500">{{ $result->unanswered_count }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h4 class="font-medium text-gray-800 mb-2">Status Penilaian</h4>
                                <p class="mb-3">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($result->grading_status === 'completed')
                                            bg-green-100 text-green-800
                                        @elseif($result->grading_status === 'partial')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-blue-100 text-blue-800
                                        @endif">
                                        @if($result->grading_status === 'completed')
                                            Dinilai Lengkap
                                        @elseif($result->grading_status === 'partial')
                                            Menunggu Penilaian Esai
                                        @else
                                            Menunggu Proses
                                        @endif
                                    </span>
                                </p>
                                
                                @if($result->grading_status === 'partial')
                                    <p class="text-xs text-yellow-600 italic">
                                        Jawaban esai belum dinilai. Nilai akhir akan diupdate setelah penilaian selesai.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Question Review -->
                        @if(!$hasPendingEssay || $attempt->session->exam->randomize_questions === false)
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    Tinjauan Jawaban
                                </h3>
                                
                                <div class="space-y-4">
                                    @php
                                        $attempt->load(['answers.question']);
                                        $questions = $attempt->session->exam->questions;
                                        
                                        if ($attempt->session->exam->randomize_questions) {
                                            // For simplicity, we'll show in original order
                                            // In practice, you'd want to reconstruct the randomized order
                                        }
                                    @endphp
                                    
                                    @foreach($questions as $index => $question)
                                        @php
                                            $answer = $attempt->answers->where('question_id', $question->id)->first();
                                            $isCorrect = $answer ? $answer->isCorrect() : false;
                                            $isUnanswered = !$answer || ($answer->answer_text === null && empty($answer->selected_options));
                                        @endphp
                                        <div class="p-4 border border-gray-200 rounded-lg">
                                            <div class="flex justify-between items-start mb-2">
                                                <h4 class="font-medium text-gray-900">
                                                    Soal {{ $index + 1 }}: {{ $question->type_label ?? ucfirst($question->type) }}
                                                </h4>
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    @if($isCorrect)
                                                        bg-green-100 text-green-800
                                                    @elseif($isUnanswered)
                                                        bg-gray-100 text-gray-500
                                                    @else
                                                        bg-red-100 text-red-600
                                                    @endif">
                                                    @if($isCorrect)
                                                        Benar
                                                    @elseif($isUnanswered)
                                                        Tidak Dijawab
                                                    @else
                                                        Salah
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            <div class="prose prose-sm max-w-none mb-3">
                                                {!! $question->question_text !!}
                                            </div>
                                            
                                            @if($question->image_path)
                                                <div class="mb-3">
                                                    <img src="{{ asset('storage/' . $question->image_path) }}" 
                                                         alt="Gambar Soal" 
                                                         class="max-w-full h-auto rounded-lg shadow-sm">
                                                </div>
                                            @endif
                                            
                                            @if($answer && !$isUnanswered)
                                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                    <h5 class="font-medium text-gray-800 mb-2">Jawaban Anda:</h5>
                                                    
                                                    @php
                                                        $type = $question->type;
                                                    @endphp
                                                    
                                                    @if($type === 'pg' || $type === 'pg_kompleks' || $type === 'benar_salah')
                                                        @php
                                                            $selected = collect($answer->selected_options)
                                                                ->map(function($id) use ($question) {
                                                                    return $question->options->find($id)->option_text ?? '';
                                                                })
                                                                ->implode(', ');
                                                        @endphp
                                                        <p class="text-gray-900">{{ $selected ?: 'Tidak ada pilihan' }}</p>
                                                    
                                                    @elseif($type === 'isian_singkat' || $type === 'esai')
                                                        <p class="text-gray-900 whitespace-pre-line">{{ $answer->answer_text }}</p>
                                                    
                                                    @elseif($type === 'menjodohkan')
                                                        @php
                                                            $pairs = collect($answer->selected_options)
                                                                ->map(function($selectedId) use ($question) {
                                                                    $selectedOption = $question->options->find($selectedId);
                                                                    $correctMatch = $selectedOption?->correct_match;
                                                                    $matchedOption = $correctMatch ? $question->options->find($correctMatch) : null;
                                                                    return [
                                                                        'selected' => $selectedOption->option_text ?? '',
                                                                        'matched' => $matchedOption ? $matchedOption->option_text : '',
                                                                        'correct' => $selectedOption?->is_correct ?? false
                                                                    ];
                                                                });
                                                        @endphp
                                                        
                                                        @if(count($pairs) > 0)
                                                            <ul class="list-disc pl-5 space-y-1 text-sm">
                                                                @foreach($pairs as $pair)
                                                                    <li class="flex items-start gap-2">
                                                                        <span>{{ $pair['selected'] }}</span>
                                                                        <span class="text-xs text-gray-500">→</span>
                                                                        <span class="{{ $pair['correct'] ? 'text-green-600' : 'text-red-600' }}">{{ $pair['matched'] }}</span>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <p class="text-gray-500 italic">Tidak ada pasangan yang dipilih</p>
                                                        @endif
                                                    @endif
                                                </div>
                                                
                                                @if($answer->score !== null)
                                                    <div class="mt-2 p-2 bg-blue-50 rounded-lg text-sm">
                                                        <strong>Nilai:</span> {{ $answer->score }}/{{ $question->pivot->score ?? $question->score }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-6 text-center">
                        <div class="flex items-center justify-center mb-4">
                            <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                            </svg>
                        </div>
                        
                        <h3 class="text-xl font-semibold text-gray-900">
                            Hasil Ujian Sedang Diproses
                        </h3>
                        
                        <p class="text-gray-600 mb-4">
                            Ujian Anda telah selesai dan sedang dalam proses penilaian. 
                            Silakan coba lagi dalam beberapa menit.
                        </p>
                        
                        <x-button href="{{ route('student.dashboard') }}">
                            Kembali ke Dashboard
                        </x-button>
                    </div>
                @endif
            </div>
        </x-card>

        <div class="text-center mt-8">
            <x-button href="{{ route('student.dashboard') }}" class="w-full max-w-xs">
                Kembali ke Dashboard
            </x-button>
        </div>
    </div>
</x-filament-panels::page>

@push('scripts')
<script>
    // Auto-refresh for partial results (if essay grading is in progress)
    @if($hasPendingEssay)
    setInterval(() => {
        window.location.reload();
    }, 30000); // Refresh every 30 seconds
    @endif
</script>
@endpush