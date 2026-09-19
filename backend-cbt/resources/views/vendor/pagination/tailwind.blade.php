<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
    <!-- Info Ringkasan Jumlah Data -->
    <div class="text-xs text-slate-500 font-medium">
        @if ($paginator->total() > 0)
            <span>Menampilkan</span>
            <span class="font-bold text-slate-900 font-mono">{{ $paginator->firstItem() ?? 1 }}</span>
            <span>sampai</span>
            <span class="font-bold text-slate-900 font-mono">{{ $paginator->lastItem() ?? $paginator->total() }}</span>
            <span>dari</span>
            <span class="font-bold text-slate-900 font-mono">{{ $paginator->total() }}</span>
            <span>data</span>
        @else
            <span>Menampilkan 0 data</span>
        @endif
    </div>

    <!-- Tombol Navigasi Paging -->
    @if ($paginator->hasPages())
        <div class="flex items-center gap-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                    class="px-3 py-1.5 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-400 cursor-not-allowed text-xs font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold transition flex items-center gap-1 shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    <span class="hidden sm:inline">Sebelumnya</span>
                </a>
            @endif

            {{-- Numbered Page Links --}}
            <div class="flex items-center gap-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" class="px-2.5 py-1 text-xs text-slate-400 font-bold">…</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                    class="w-8 h-8 rounded-xl bg-brand-900 text-white font-black text-xs flex items-center justify-center shadow-xs border border-brand-950">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="w-8 h-8 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center border border-slate-300 transition shadow-2xs">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="px-3 py-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold transition flex items-center gap-1 shadow-2xs">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                    class="px-3 py-1.5 rounded-xl border border-slate-200/80 bg-slate-50 text-slate-400 cursor-not-allowed text-xs font-semibold flex items-center gap-1">
                    <span class="hidden sm:inline">Selanjutnya</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>
    @else
        <!-- Halaman Tunggal (Disabled Indicator) -->
        <div class="flex items-center gap-1">
            <span class="w-8 h-8 rounded-xl bg-brand-900 text-white font-black text-xs flex items-center justify-center shadow-xs border border-brand-950">
                1
            </span>
        </div>
    @endif
</nav>
