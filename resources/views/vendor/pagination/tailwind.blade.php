@if ($paginator->hasPages())
<div >
    <div class="bg-white shadow-sm rounded-md p-4 mt-3">
        <nav class="flex items-center justify-between">

            {{-- Informações --}}
            <div class="text-sm text-gray-600">
                @if ($paginator->firstItem())
                    Mostrando
                    <span class="font-semibold">{{ $paginator->firstItem() }}</span>
                    até
                    <span class="font-semibold">{{ $paginator->lastItem() }}</span>
                    de
                    <span class="font-semibold">{{ $paginator->total() }}</span>
                    registros
                @else
                    {{ $paginator->count() }} registros
                @endif
            </div>

            {{-- Paginação --}}
            <ul class="flex -space-x-px text-sm">

                {{-- Previous --}}
                <li>
                    @if ($paginator->onFirstPage())
                        <span class="flex items-center justify-center border shadow-xs font-medium leading-5 rounded-l-md text-sm px-3 h-9 opacity-50 cursor-not-allowed">
                            Anterior
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        class="flex items-center justify-center border shadow-xs font-medium leading-5 rounded-l-md text-sm px-3 h-9 hover:text-white hover:bg-primary hover:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 transition duration-200">
                            Anterior
                        </a>
                    @endif
                </li>

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)

                    {{-- ... --}}
                    @if (is_string($element))
                        <li>
                            <span class="flex items-center justify-center w-9 h-9 border text-gray-400 select-none">
                                {{ $element }}
                            </span>
                        </li>
                    @endif

                    {{-- Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)

                            {{-- Página atual --}}
                            @if ($page == $paginator->currentPage())
                                <li>
                                    <span class="flex items-center justify-center bg-primary text-white border border-primary shadow-xs font-semibold text-sm w-9 h-9">
                                        {{ $page }}
                                    </span>
                                </li>

                            {{-- Outras páginas --}}
                            @else
                                <li>
                                    <a href="{{ $url }}"
                                    class="flex items-center justify-center border shadow-xs font-medium text-sm w-9 h-9 hover:bg-neutral-tertiary-medium hover:text-heading hover:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 transition duration-200"
                                    aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif

                        @endforeach
                    @endif

                @endforeach

                {{-- Next --}}
                <li>
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                        class="flex items-center justify-center border shadow-xs font-medium leading-5 rounded-r-md text-sm px-3 h-9 hover:text-white hover:bg-primary hover:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 transition duration-200">
                            Próximo
                        </a>
                    @else
                        <span class="flex items-center justify-center border shadow-xs font-medium leading-5 rounded-r-md text-sm px-3 h-9 opacity-50 cursor-not-allowed">
                            Próximo
                        </span>
                    @endif
                </li>

            </ul>

        </nav>
        @endif
    </div>
</div>