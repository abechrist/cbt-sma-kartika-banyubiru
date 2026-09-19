<x-layouts.app :title="$material->title . ' - ' . $course->subject->name">
    <div class="max-w-4xl mx-auto py-6 sm:py-8 px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <div class="mb-4 flex items-center gap-2 text-xs font-medium text-slate-500">
            <a href="{{ route('lms.courses.index') }}" class="hover:text-brand-800 transition">Kelas Belajar</a>
            <span>/</span>
            <a href="{{ route('lms.courses.show', $course->id) }}" class="hover:text-brand-800 transition">{{ $course->subject->name }}</a>
            <span>/</span>
            <span class="text-slate-800 font-bold truncate">{{ $material->title }}</span>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Material Container Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <!-- Header -->
            <div class="p-6 sm:p-8 bg-slate-50/80 border-b border-slate-200">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-100 text-brand-900">
                        {{ $material->chapter ?? 'Umum' }}
                    </span>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold uppercase
                        @if($material->type === 'video') bg-rose-100 text-rose-800
                        @elseif($material->type === 'file') bg-blue-100 text-blue-800
                        @else bg-emerald-100 text-emerald-800
                        @endif
                    ">
                        {{ $material->type }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono">{{ $material->created_at->format('d M Y') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    {{ $material->title }}
                </h1>
                <p class="text-xs text-slate-500 mt-2">
                    Diunggah oleh: <strong class="text-slate-800">{{ $material->creator->name ?? $course->teacher->name }}</strong>
                </p>
            </div>

            <!-- Content Area -->
            <div class="p-6 sm:p-8">
                <!-- Video Player Embed -->
                @if($material->type === 'video' && $material->video_url)
                    @php
                        // Helper sederhana untuk ekstrak YouTube embed ID
                        $youtubeId = null;
                        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $material->video_url, $matches)) {
                            $youtubeId = $matches[1];
                        }
                    @endphp
                    <div class="mb-8 rounded-2xl overflow-hidden bg-black aspect-video shadow-md">
                        @if($youtubeId)
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $youtubeId }}" title="{{ $material->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        @else
                            <div class="p-8 text-center text-white">
                                <p class="text-sm">Video External URL:</p>
                                <a href="{{ $material->video_url }}" target="_blank" class="mt-2 inline-block px-4 py-2 rounded-xl bg-rose-600 font-bold text-xs hover:bg-rose-700">Tonton Video Pembelajaran ↗</a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- File Attachment Box -->
                @if($material->type === 'file' && $material->file_path)
                    <div class="mb-8 p-6 rounded-2xl bg-blue-50/70 border border-blue-200 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-blue-950">Berkas Lampiran Modul</h4>
                                <p class="text-xs text-blue-700 mt-0.5">Dokumen panduan materi belajar kurikulum merdeka</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" download
                            class="px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs shadow-xs transition shrink-0 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Berkas</span>
                        </a>
                    </div>
                @endif

                <!-- Rich Text Reading Body -->
                @if($material->content_text)
                    <div class="prose prose-slate max-w-none text-sm leading-relaxed mb-8 text-slate-800 whitespace-pre-line bg-slate-50/60 p-6 rounded-2xl border border-slate-100">
                        {{ $material->content_text }}
                    </div>
                @endif

                <!-- Student Completion Action -->
                @if(auth()->user()->isSiswa())
                    <div class="mt-8 pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500">
                            @if($isCompleted)
                                <span class="text-emerald-700 font-bold flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    Anda telah menandai materi ini selesai dipelajari.
                                </span>
                            @else
                                <span>Tandai selesai jika Anda sudah memahami materi di atas.</span>
                            @endif
                        </div>

                        <form action="{{ route('lms.materials.toggle-complete', [$course->id, $material->id]) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                class="px-6 py-2.5 rounded-xl font-bold text-xs shadow-xs transition flex items-center gap-2 @if($isCompleted) bg-slate-200 hover:bg-slate-300 text-slate-800 @else bg-emerald-700 hover:bg-emerald-800 text-white @endif">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ $isCompleted ? 'Tandai Belum Selesai' : 'Tandai Sudah Selesai' }}</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
