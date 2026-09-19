<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
    <meta name="theme-color" content="#061d13">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="CBT Kartika">
    <title>{{ $attempt->session->exam->name }} - CBT SMA Kartika III-1 Banyubiru</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/pwa-icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .question-nav-btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            font-family: ui-monospace, SFMono-Regular, monospace;
        }
        .question-nav-btn.unanswered {
            background: #f1f5f9;
            color: #334155;
            border: 1.5px solid #cbd5e1;
        }
        .question-nav-btn.unanswered:hover {
            background: #e2e8f0;
            color: #0f172a;
            border-color: #94a3b8;
        }
        .question-nav-btn.answered {
            background: #12472e;
            color: #ffffff;
            border: 1.5px solid #0b3120;
        }
        .question-nav-btn.current {
            background: #f59e0b;
            color: #0f172a;
            border: 2px solid #b45309;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.35);
            transform: scale(1.08);
            z-index: 10;
        }
        .question-nav-btn.flagged {
            background: #e11d48;
            color: #ffffff;
            border: 1.5px solid #be123c;
        }
        .question-card { display: none; }
        .question-card.active { display: block; }
        
        #countdown {
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.05em;
        }
        .timer-warning {
            color: #e11d48 !important;
            animation: urgent-pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes urgent-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.04); }
        }
        .auto-save-indicator { transition: opacity 0.3s; }
        @media print { .no-print { display: none !important; } }
        
        /* Lockdown / Kiosk prevention */
        body.locked-down, body.locked-down * { -webkit-user-select: none; -moz-user-select: none; user-select: none; cursor: default; }
        body.locked-down input[type="text"], body.locked-down textarea { -webkit-user-select: text; user-select: text; cursor: text; }
        body.locked-down { overflow-x: hidden; }

        /* Font Scaling styles */
        body.font-scale-sm .exam-text { font-size: 14px; line-height: 1.6; }
        body.font-scale-md .exam-text { font-size: 16px; line-height: 1.65; }
        body.font-scale-lg .exam-text { font-size: 19px; line-height: 1.7; }
    </style>
</head>
<body class="min-h-full flex flex-col font-sans text-slate-900 bg-slate-100 font-scale-md"
    oncontextmenu="return false;" onselectstart="return false;" ondragstart="return false;" oncopy="return false;" oncut="return false;" onpaste="return false;">

    <!-- CBT Cockpit Top HUD Bar -->
    <header class="bg-brand-950 text-white border-b border-brand-900/80 shadow-md sticky top-0 z-40 no-print">
        <div class="max-w-7xl mx-auto px-4 py-2.5 flex flex-wrap justify-between items-center gap-3">
            <!-- School Crest & Exam Title -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/logo-kartika.png') }}" alt="Logo SMA Kartika III-1 Banyubiru" class="w-10 h-10 object-contain drop-shadow-xs">
                </div>
                <div>
                    <h1 class="text-sm sm:text-base font-bold text-white leading-tight tracking-tight">
                        {{ $attempt->session->exam->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-xs text-brand-200 mt-0.5">
                        <span class="font-medium text-gold-300">{{ $attempt->session->name }}</span>
                        <span>•</span>
                        <span>{{ $attempt->user->name }} ({{ $attempt->user->nisn ?? 'Peserta' }})</span>
                    </div>
                </div>
            </div>

            <!-- Controls & Timer Cockpit -->
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Auto Save Status Indicator -->
                <div class="auto-save-indicator flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs font-semibold" id="saveIndicator" style="opacity: 0;">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>✓ Tersimpan</span>
                </div>

                <!-- Font Size Adjuster -->
                <div class="hidden sm:flex items-center gap-1 bg-brand-900/90 p-1 rounded-lg border border-brand-800 text-xs">
                    <button type="button" onclick="setFontScale('sm')" title="Kecilkan Font" class="px-2 py-0.5 rounded text-brand-200 hover:text-white hover:bg-brand-800 font-bold transition">A-</button>
                    <button type="button" onclick="setFontScale('md')" title="Font Normal" class="px-2 py-0.5 rounded bg-brand-800 text-gold-300 font-bold transition">A</button>
                    <button type="button" onclick="setFontScale('lg')" title="Besarkan Font" class="px-2 py-0.5 rounded text-brand-200 hover:text-white hover:bg-brand-800 font-bold transition">A+</button>
                </div>

                <!-- Fullscreen Button -->
                <button type="button" onclick="enterFullscreen()" title="Mode Layar Penuh" class="hidden sm:flex p-1.5 rounded-lg bg-brand-900/90 hover:bg-brand-800 text-brand-200 hover:text-white border border-brand-800 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </button>

                <!-- High-Impact Countdown Timer -->
                <div class="bg-brand-900 px-3.5 py-1.5 rounded-xl border border-brand-800 flex items-center gap-2 shadow-xs">
                    <div class="text-right">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-brand-300 leading-none">Sisa Waktu</div>
                        <div id="countdown" class="text-xl sm:text-2xl font-bold font-mono text-gold-400 leading-none mt-1 tabular-nums">--:--</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col lg:flex-row gap-6 flex-1">
        <!-- Question Workspace (Main Column) -->
        <div class="flex-1 order-1 lg:order-1">
            @csrf
            <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">
            <input type="hidden" name="token" value="{{ request()->query('token', '') }}">

            @foreach($questions as $index => $question)
            <div class="question-card bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8" id="question{{ $index }}" data-question-id="{{ $question->id }}">
                <!-- Card Header -->
                <div class="flex flex-wrap items-center justify-between gap-3 pb-5 border-b border-slate-200 mb-6">
                    <div class="flex items-center gap-2.5">
                        <span class="px-3.5 py-1 rounded-lg bg-brand-800 text-white font-bold text-sm tracking-wide">
                            Soal Nomor {{ $index + 1 }}
                        </span>
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-lg
                            @switch($question->difficulty)
                                @case('easy') bg-emerald-100 text-emerald-800 border border-emerald-200 @break
                                @case('medium') bg-amber-100 text-amber-800 border border-amber-200 @break
                                @case('hard') bg-rose-100 text-rose-800 border border-rose-200 @break
                                @default bg-slate-100 text-slate-700
                            @endswitch
                        ">
                            Tingkat: {{ ucfirst($question->difficulty) }}
                        </span>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-lg bg-slate-100 text-slate-600 border border-slate-200">
                            Bobot: {{ $question->score }} poin
                        </span>
                    </div>

                    <!-- Flag / Ragu-ragu Checkbox -->
                    <label class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 cursor-pointer transition">
                        <input type="checkbox" class="flag-checkbox h-4 w-4 text-amber-600 focus:ring-amber-500 rounded border-amber-300"
                            data-index="{{ $index }}" onchange="toggleFlag({{ $index }}, this.checked)">
                        <span class="text-xs font-bold text-amber-900">Ragu-ragu</span>
                    </label>
                </div>

                <!-- Soal Content -->
                <div class="mb-8">
                    <div class="prose max-w-none text-slate-900 exam-text font-normal leading-relaxed">
                        {!! $question->question_text !!}
                    </div>
                    @if($question->image_path)
                        <div class="mt-4 p-2 bg-slate-50 border border-slate-200 rounded-xl inline-block max-w-full">
                            <img src="{{ asset('storage/' . $question->image_path) }}" alt="Lampiran Gambar Soal" class="max-w-full h-auto rounded-lg object-contain max-h-96">
                        </div>
                    @endif
                </div>

                <!-- Pilihan Ganda & Pilihan Ganda Kompleks -->
                @if($question->type === 'pg' || $question->type === 'pg_kompleks')
                <div class="space-y-3">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                        {{ $question->type === 'pg_kompleks' ? 'Pilihlah satu atau lebih jawaban yang benar:' : 'Pilihlah salah satu jawaban berikut:' }}
                    </div>
                    @foreach($question->options->sortBy('sort_order') as $option)
                    @php
                        $isSelected = in_array($option->id, $attempt->answers->where('question_id', $question->id)->first()?->selected_options ?? []);
                    @endphp
                    <label class="flex items-start gap-3.5 p-4 rounded-xl border-2 cursor-pointer transition hover:border-brand-600 hover:bg-brand-50/40 min-h-[52px]
                        {{ $isSelected ? 'bg-brand-50 border-brand-700 shadow-xs' : 'border-slate-200 bg-white' }}">
                        <input type="{{ $question->type === 'pg_kompleks' ? 'checkbox' : 'radio' }}" 
                            name="question_{{ $question->id }}" 
                            value="{{ $option->id }}"
                            {{ $isSelected ? 'checked' : '' }}
                            onchange="saveAnswer({{ $question->id }}, this, '{{ $question->type }}')"
                            class="mt-1 h-5 w-5 text-brand-800 focus:ring-brand-700 border-slate-300 rounded">
                        <span class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs shrink-0
                            {{ $isSelected ? 'bg-brand-800 text-white' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                            {{ $option->label }}
                        </span>
                        <span class="exam-text text-slate-800 pt-0.5">{{ $option->option_text }}</span>
                    </label>
                    @endforeach
                </div>
                @endif

                <!-- Benar / Salah -->
                @if($question->type === 'benar_salah')
                @php
                    $savedAns = $attempt->answers->where('question_id', $question->id)->first()?->answer_text;
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center gap-4 p-5 rounded-xl border-2 cursor-pointer transition min-h-[56px]
                        {{ $savedAns === 'benar' ? 'bg-emerald-50 border-emerald-600 ring-2 ring-emerald-600/20' : 'border-slate-200 bg-white hover:border-emerald-400 hover:bg-slate-50' }}">
                        <input type="radio" name="question_{{ $question->id }}" value="benar" 
                            onchange="saveAnswerText({{ $question->id }}, 'benar')"
                            {{ $savedAns === 'benar' ? 'checked' : '' }}
                            class="h-5 w-5 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="text-base font-bold text-slate-900">BENAR</span>
                    </label>
                    <label class="flex items-center gap-4 p-5 rounded-xl border-2 cursor-pointer transition min-h-[56px]
                        {{ $savedAns === 'salah' ? 'bg-rose-50 border-rose-600 ring-2 ring-rose-600/20' : 'border-slate-200 bg-white hover:border-rose-400 hover:bg-slate-50' }}">
                        <input type="radio" name="question_{{ $question->id }}" value="salah" 
                            onchange="saveAnswerText({{ $question->id }}, 'salah')"
                            {{ $savedAns === 'salah' ? 'checked' : '' }}
                            class="h-5 w-5 text-rose-600 focus:ring-rose-500 border-slate-300">
                        <span class="text-base font-bold text-slate-900">SALAH</span>
                    </label>
                </div>
                @endif

                <!-- Menjodohkan (Matching) -->
                @if($question->type === 'menjodohkan')
                @php($savedMatching = $attempt->answers->where('question_id', $question->id)->first()?->selected_options ?? [])
                <div class="space-y-3">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Pasangkan pernyataan dengan pilihan jawaban yang tepat:</div>
                    @foreach($question->options->sortBy('sort_order') as $option)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded bg-brand-100 text-brand-900 font-bold text-xs flex items-center justify-center shrink-0">{{ $option->label }}</span>
                            <span class="exam-text font-medium text-slate-800">{{ $option->option_text }}</span>
                        </div>
                        <select onchange="saveMatching({{ $question->id }}, {{ $option->id }}, this.value)"
                            class="w-full sm:w-64 bg-white border-2 border-slate-300 rounded-xl px-3 py-2 text-sm text-slate-800 font-medium focus:ring-2 focus:ring-brand-700 focus:border-brand-700 outline-none transition">
                            <option value="">-- Pilih Pasangan --</option>
                            @foreach($question->options->pluck('correct_match')->filter()->unique()->values() as $choice)
                            <option value="{{ $choice }}" {{ ($savedMatching[$option->id] ?? null) === $choice ? 'selected' : '' }}>{{ $choice }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Isian Singkat -->
                @if($question->type === 'isian_singkat')
                <div>
                    <label for="input_{{ $question->id }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jawaban Singkat Anda:</label>
                    <input type="text" id="input_{{ $question->id }}" 
                        value="{{ $attempt->answers->where('question_id', $question->id)->first()?->answer_text ?? '' }}"
                        onchange="saveAnswerText({{ $question->id }}, this.value)"
                        placeholder="Ketikkan jawaban Anda di sini..."
                        class="w-full px-4 py-3 bg-slate-50 border-2 border-slate-300 rounded-xl text-base font-medium text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 focus:border-brand-700 outline-none transition">
                </div>
                @endif

                <!-- Esai / Uraian -->
                @if($question->type === 'esai')
                <div>
                    <label for="input_{{ $question->id }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tuliskan Penjelasan Lengkap Anda:</label>
                    <textarea id="input_{{ $question->id }}" rows="7"
                        onchange="saveAnswerText({{ $question->id }}, this.value)"
                        placeholder="Uraikan jawaban dan argumen Anda secara jelas dan sistematis..."
                        class="w-full p-4 bg-slate-50 border-2 border-slate-300 rounded-xl text-base text-slate-900 focus:bg-white focus:ring-2 focus:ring-brand-700 focus:border-brand-700 outline-none transition leading-relaxed">{{ $attempt->answers->where('question_id', $question->id)->first()?->answer_text ?? '' }}</textarea>
                </div>
                @endif

                <!-- Bottom Navigation Buttons -->
                <div class="flex items-center justify-between mt-10 pt-6 border-t border-slate-200">
                    <button type="button" onclick="prevQuestion()" {{ $index === 0 ? 'disabled' : '' }}
                        class="px-5 py-2.5 rounded-xl border-2 border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition flex items-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        <span>Soal Sebelumnya</span>
                    </button>

                    <span class="text-xs font-bold text-slate-400 font-mono tracking-wider">
                        {{ $index + 1 }} DARI {{ $questions->count() }}
                    </span>

                    <button type="button" onclick="nextQuestion()" {{ $index === $questions->count() - 1 ? 'disabled' : '' }}
                        class="px-5 py-2.5 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-semibold text-sm transition flex items-center gap-2 shadow-xs disabled:opacity-40 disabled:cursor-not-allowed">
                        <span>Soal Berikutnya</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Question Navigation Sidebar (Palette) -->
        <aside class="w-full lg:w-80 shrink-0 order-2 lg:order-2 no-print">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 sticky top-20">
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                    <h3 class="font-bold text-slate-900 text-sm tracking-tight">Navigasi Nomor Soal</h3>
                    <span class="text-[11px] font-semibold text-brand-800 bg-brand-50 px-2 py-0.5 rounded-md border border-brand-200">
                        Total: {{ $questions->count() }}
                    </span>
                </div>

                <!-- Questions Grid -->
                <div class="grid grid-cols-5 sm:grid-cols-6 lg:grid-cols-5 gap-2.5 mb-5" id="questionNav">
                    @foreach($questions as $index => $question)
                    <button type="button" 
                        onclick="goToQuestion({{ $index }})"
                        id="navBtn{{ $index }}"
                        class="question-nav-btn unanswered"
                        data-index="{{ $index }}">
                        {{ $index + 1 }}
                    </button>
                    @endforeach
                </div>

                <!-- Visual Legend -->
                <div class="grid grid-cols-2 gap-2 text-xs py-3 border-y border-slate-100 mb-4 bg-slate-50/70 p-3 rounded-xl">
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded bg-brand-800 shrink-0"></span>
                        <span class="text-slate-600">Dijawab</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded bg-amber-400 ring-1 ring-amber-600 shrink-0"></span>
                        <span class="text-slate-600">Soal Aktif</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded bg-rose-500 shrink-0"></span>
                        <span class="text-slate-600">Ragu-ragu</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded bg-slate-200 border border-slate-300 shrink-0"></span>
                        <span class="text-slate-600">Belum Jawab</span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mb-5">
                    <div class="flex justify-between text-xs font-semibold text-slate-600 mb-1.5">
                        <span>Kemajuan Pengerjaan</span>
                        <span><span id="progressText" class="text-brand-800 font-bold">0</span> / {{ $questions->count() }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden border border-slate-200">
                        <div id="progressBar" class="bg-gradient-to-r from-brand-700 to-brand-600 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Final Submission Action -->
                <form id="submitForm" action="{{ route('exam.submit', $attempt) }}" method="POST" onsubmit="return confirmSubmit()">
                    @csrf
                    <input type="hidden" name="exam_attempt_id" value="{{ $attempt->id }}">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm transition shadow-sm hover:shadow flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Kumpulkan Ujian</span>
                    </button>
                </form>
            </div>
        </aside>
    </div>

    <!-- Scripting Engine -->
    <script>
        const ATTEMPT_ID = {{ $attempt->id }};
        const TOTAL_QUESTIONS = {{ $questions->count() }};
        const END_AT = '{{ $attempt->ended_at->toIso8601String() }}';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        let currentQuestion = 0;
        let answers = {};
        let flagged = {};

        // Load existing answers
        @foreach($attempt->answers as $answer)
            answers[{{ $answer->question_id }}] = {
                answer_text: @json($answer->answer_text),
                selected_options: @json($answer->selected_options)
            };
        @endforeach

        // Font scaling helper
        function setFontScale(scale) {
            document.body.classList.remove('font-scale-sm', 'font-scale-md', 'font-scale-lg');
            document.body.classList.add('font-scale-' + scale);
        }

        // Timer engine
        let expiresAt = new Date(END_AT).getTime();

        function syncFromServer(payload) {
            if (payload && typeof payload.time_remaining === 'number' && payload.status === 'in_progress') {
                const corrected = Date.now() + payload.time_remaining * 1000;
                if (corrected < expiresAt) {
                    expiresAt = corrected;
                }
            }
        }

        function updateCountdown() {
            const diff = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;
            const el = document.getElementById('countdown');
            if (el) {
                el.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                
                if (diff <= 300) {
                    el.classList.add('timer-warning');
                } else {
                    el.classList.remove('timer-warning');
                }
            }
            
            if (diff <= 0) {
                document.getElementById('submitForm').submit();
            }
        }
        setInterval(updateCountdown, 1000);
        updateCountdown();

        // Heartbeat — keeps session alive and re-syncs server-authoritative timer
        function sendHeartbeat() {
            fetch('/exam/heartbeat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ exam_attempt_id: ATTEMPT_ID })
            })
                .then(r => r.ok ? r.json() : null)
                .then(data => { if (data) syncFromServer(data); });
        }
        setInterval(sendHeartbeat, 15000);
        sendHeartbeat();

        // Activity indicators (FR-5.3)
        function sendActivity(event, suspicious) {
            fetch('/exam/activity', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ exam_attempt_id: ATTEMPT_ID, event, suspicious: Boolean(suspicious) })
            });
        }
        document.addEventListener('visibilitychange', () => {
            sendActivity(document.hidden ? 'tab_blur' : 'tab_focus', document.hidden);
        });
        window.addEventListener('blur', () => sendActivity('window_blur', true));
        window.addEventListener('focus', () => sendActivity('window_focus', false));
        window.addEventListener('online', () => sendActivity('reconnect', true));
        window.addEventListener('offline', () => sendActivity('reconnect', false));
        document.addEventListener('paste', () => sendActivity('paste', true));

        // ═══ Kiosk / Lockdown Mode (FR-4.7) ═══
        const KIOSK_URL_PARAM = new URLSearchParams(window.location.search).has('kiosk');
        let kioskActive = KIOSK_URL_PARAM;
        let fullscreenViolations = 0;

        function enterFullscreen() {
            const el = document.documentElement;
            const req = el.requestFullscreen || el.webkitRequestFullscreen || el.msRequestFullscreen;
            if (req && !document.fullscreenElement && !document.webkitFullscreenElement) {
                try { req.call(el); } catch (e) { }
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const leaving = document.fullscreenElement == null && !document.webkitFullscreenElement;
            if (kioskActive && leaving) {
                fullscreenViolations++;
                sendActivity('kiosk_fullscreen_broken', true);
                if (fullscreenViolations <= 10) enterFullscreen();
            } else if (kioskActive) {
                sendActivity('kiosk_fullscreen_ok', false);
            }
        });

        if (kioskActive) {
            document.body.classList.add('locked-down');
            enterFullscreen();
            const firstGesture = () => { if (!document.fullscreenElement) enterFullscreen(); };
            document.addEventListener('click', firstGesture, { once: true });

            const blockedKeys = ['F5', 'F11', 'F12', 'PrintScreen'];
            document.addEventListener('keydown', (e) => {
                const ctrl = e.ctrlKey, alt = e.altKey, meta = e.metaKey;
                if (ctrl && ['r','R','w','W','s','S','p','P','n','N','t','T','u','U'].includes(e.key)) {
                    e.preventDefault(); sendActivity('kiosk_shortcut_blocked', true);
                }
                if (blockedKeys.includes(e.key)) { e.preventDefault(); sendActivity('kiosk_shortcut_blocked', true); }
                if (alt && e.key === 'Tab') { e.preventDefault(); sendActivity('kiosk_shortcut_blocked', true); }
                if ((ctrl || meta) && e.key.toLowerCase() === 'c') { e.preventDefault(); sendActivity('kiosk_shortcut_blocked', true); }
                if (ctrl && e.key.toLowerCase() === 'd') { e.preventDefault(); sendActivity('kiosk_shortcut_blocked', true); }
            });
            window.addEventListener('beforeunload', (e) => { e.preventDefault(); e.returnValue = ''; sendActivity('kiosk_navigation_attempt', true); });
            disableBackNavigation();
        }

        if (window.location.search.includes('kiosk=0')) {
            document.body.classList.remove('locked-down');
            kioskActive = false;
        }

        if (kioskActive) {
            const badge = document.createElement('div');
            badge.id = 'kioskBadge';
            badge.textContent = 'KIOSK LOCKDOWN AKTIF';
            badge.style.cssText = 'position:fixed;bottom:12px;right:12px;z-index:9999;background:#12472e;color:#fff;' +
                'padding:5px 12px;border-radius:8px;font-size:11px;font-weight:700;letter-spacing:0.05em;border:1px solid #d97706;pointer-events:none;';
            document.body.appendChild(badge);
        }

        function disableBackNavigation() {
            history.pushState(null, '', window.location.href);
            window.addEventListener('popstate', () => {
                history.pushState(null, '', window.location.href);
                sendActivity('kiosk_navigation_blocked', true);
            });
        }

        window.addEventListener('blur', () => {
            if (kioskActive) setTimeout(() => { if (document.hidden) sendActivity('kiosk_tab_switch', true); }, 50);
        });

        // Navigation
        function goToQuestion(index) {
            document.querySelectorAll('.question-card').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('[id^="navBtn"]').forEach(el => el.classList.remove('current'));
            const card = document.getElementById('question' + index);
            const btn = document.getElementById('navBtn' + index);
            if (card) card.classList.add('active');
            if (btn) btn.classList.add('current');
            currentQuestion = index;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function nextQuestion() {
            if (currentQuestion < TOTAL_QUESTIONS - 1) goToQuestion(currentQuestion + 1);
        }

        function prevQuestion() {
            if (currentQuestion > 0) goToQuestion(currentQuestion - 1);
        }

        // Keyboard Shortcuts for Rapid Student Input
        document.addEventListener('keydown', (e) => {
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) return;
            if (e.key === 'ArrowLeft') prevQuestion();
            if (e.key === 'ArrowRight') nextQuestion();
            
            const activeCard = document.getElementById('question' + currentQuestion);
            if (!activeCard) return;
            const inputs = Array.from(activeCard.querySelectorAll('input[type="radio"], input[type="checkbox"]'));
            if (inputs.length === 0) return;
            
            let targetIdx = -1;
            if (['1','2','3','4','5'].includes(e.key)) targetIdx = parseInt(e.key) - 1;
            if (['a','A'].includes(e.key)) targetIdx = 0;
            if (['b','B'].includes(e.key)) targetIdx = 1;
            if (['c','C'].includes(e.key)) targetIdx = 2;
            if (['d','D'].includes(e.key)) targetIdx = 3;
            if (['e','E'].includes(e.key)) targetIdx = 4;
            
            if (targetIdx >= 0 && targetIdx < inputs.length) {
                inputs[targetIdx].checked = true;
                inputs[targetIdx].dispatchEvent(new Event('change'));
            }
        });

        // Save answers
        function saveAnswer(questionId, input, type) {
            const checked = Array.from(document.querySelectorAll(`input[name="question_${questionId}"]:checked`))
                .map(el => parseInt(el.value));
            answers[questionId] = { selected_options: checked, answer_text: null };
            persistAnswer(questionId, { selected_options: checked });
            updateNavButton(questionId, checked.length > 0);
        }

        function saveAnswerText(questionId, text) {
            answers[questionId] = { answer_text: text, selected_options: [] };
            persistAnswer(questionId, { answer_text: text });
            updateNavButton(questionId, text.trim() !== '');
        }

        function saveMatching(questionId, optionId, value) {
            const current = answers[questionId]?.selected_options && !Array.isArray(answers[questionId].selected_options)
                ? { ...answers[questionId].selected_options } : {};
            if (value === '') {
                delete current[optionId];
            } else {
                current[optionId] = value;
            }
            answers[questionId] = { selected_options: current, answer_text: null };
            persistAnswer(questionId, { selected_options: current });
            updateNavButton(questionId, Object.keys(current).length > 0);
        }

        function countSelected(sel) {
            if (sel == null) return 0;
            return Array.isArray(sel) ? sel.length : Object.keys(sel).length;
        }

        function persistAnswer(questionId, data) {
            fetch('/exam/answer/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                body: JSON.stringify({ exam_attempt_id: ATTEMPT_ID, question_id: questionId, ...data })
            }).then(r => {
                if (r.ok) {
                    const ind = document.getElementById('saveIndicator');
                    if (ind) {
                        ind.style.opacity = '1';
                        setTimeout(() => { ind.style.opacity = '0'; }, 2000);
                    }
                }
            });
        }

        function updateNavButton(questionId, answered) {
            const qIndex = [...document.querySelectorAll('.question-card')].findIndex(el => parseInt(el.dataset.questionId) === questionId);
            if (qIndex === -1) return;
            const btn = document.getElementById('navBtn' + qIndex);
            if (flagged[qIndex]) return;
            btn.className = 'question-nav-btn ' + (answered ? 'answered' : 'unanswered');
            if (qIndex === currentQuestion) btn.classList.add('current');
            updateProgress();
        }

        function toggleFlag(index, isFlagged) {
            flagged[index] = isFlagged;
            const btn = document.getElementById('navBtn' + index);
            btn.className = 'question-nav-btn ' + (isFlagged ? 'flagged' : 'unanswered');
            const card = document.getElementById('question' + index);
            const qId = parseInt(card?.dataset.questionId);
            if (!isFlagged && answers[qId]) {
                const hasAnswer = countSelected(answers[qId].selected_options) > 0 || (answers[qId].answer_text && answers[qId].answer_text.trim() !== '');
                btn.className = 'question-nav-btn ' + (hasAnswer ? 'answered' : 'unanswered');
            }
            if (index === currentQuestion) btn.classList.add('current');
        }

        function updateProgress() {
            let count = 0;
            document.querySelectorAll('.question-nav-btn.answered').forEach(() => count++);
            const txt = document.getElementById('progressText');
            const bar = document.getElementById('progressBar');
            if (txt) txt.textContent = count;
            if (bar) bar.style.width = (count / TOTAL_QUESTIONS * 100) + '%';
        }

        function confirmSubmit() {
            const answered = document.querySelectorAll('.question-nav-btn.answered').length;
            const unanswered = TOTAL_QUESTIONS - answered;
            const flagCount = document.querySelectorAll('.question-nav-btn.flagged').length;
            
            let message = `Ringkasan Pengerjaan:\n• Soal Terjawab: ${answered}\n• Belum Dijawab: ${unanswered}`;
            if (flagCount > 0) message += `\n• Masih Ragu-ragu: ${flagCount}`;
            message += '\n\nApakah Anda yakin ingin menyelesaikan dan mengumpulkan ujian ini sekarang?';
            
            return confirm(message);
        }

        // Initialize first question and answered states
        goToQuestion(0);
        @foreach($attempt->answers as $answer)
            updateNavButton({{ $answer->question_id }}, true);
        @endforeach
        updateProgress();

        // Periodic sync
        setInterval(() => {
            Object.entries(answers).forEach(([qId, data]) => {
                if (data.selected_options && countSelected(data.selected_options) > 0) {
                    persistAnswer(parseInt(qId), { selected_options: data.selected_options });
                }
            });
        }, 30000);
        // Service Worker Registration for PWA Exam Mode
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('PWA Service Worker registered:', reg.scope))
                    .catch((err) => console.error('PWA Service Worker registration failed:', err));
            });
        }
    </script>
</body>
</html>