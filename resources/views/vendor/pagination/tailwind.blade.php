@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        
        {{-- Texto de Resultados (Mostrando X a Y de Z) --}}
        <div class="text-xs text-gray-500 font-medium">
            <span>Mostrando</span>
            <span class="font-bold text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span>
            <span>a</span>
            <span class="font-bold text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span>
            <span>de</span>
            <span class="font-bold text-gray-900">{{ $paginator->total() }}</span>
            <span>resultados</span>
        </div>

        {{-- Barra de Paginación Estilo « Anterior 1 2 3 Siguiente » --}}
        <div class="inline-flex items-stretch rounded-xl border border-gray-300 bg-[#f1f5f9] p-0.5 shadow-2xs overflow-hidden text-xs">
            
            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-gray-400 font-medium cursor-not-allowed bg-transparent select-none flex items-center">
                    « Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-3 py-1.5 text-blue-600 hover:text-blue-800 font-bold hover:bg-white/80 transition rounded-lg flex items-center">
                    « Anterior
                </a>
            @endif

            <div class="w-px bg-gray-300 my-1"></div>

            {{-- Elementos de Páginas --}}
            @foreach ($elements as $element)
                {{-- Separador "..." --}}
                @if (is_string($element))
                    <span class="px-2.5 py-1.5 text-gray-400 font-medium flex items-center">
                        {{ $element }}
                    </span>
                    <div class="w-px bg-gray-300 my-1"></div>
                @endif

                {{-- Enlaces de Páginas --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1.5 font-black bg-blue-600 text-white shadow-xs rounded-lg flex items-center justify-center min-w-[32px]">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 font-bold text-blue-600 hover:text-blue-800 hover:bg-white/80 transition rounded-lg flex items-center justify-center min-w-[32px]">
                                {{ $page }}
                            </a>
                        @endif
                        <div class="w-px bg-gray-300 my-1 last:hidden"></div>
                    @endforeach
                @endif
            @endforeach

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-3 py-1.5 text-blue-600 hover:text-blue-800 font-bold hover:bg-white/80 transition rounded-lg flex items-center">
                    Siguiente »
                </a>
            @else
                <span class="px-3 py-1.5 text-gray-400 font-medium cursor-not-allowed bg-transparent select-none flex items-center">
                    Siguiente »
                </span>
            @endif

        </div>

    </nav>
@endif
