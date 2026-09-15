{{--
    View de paginação própria pro tema (não a Tailwind/Bootstrap padrão do
    Laravel, que não bate nada com o CSS deste projeto) -- reaproveita as
    classes pagi_ul/#pagination que já existem em style.css, mas com links
    reais gerados pelo LengthAwarePaginator ($paginator/$elements vêm de
    LengthAwarePaginator::render(), não são inventados aqui).
--}}
@if ($paginator->hasPages())
    <div class="pagi_ul">
        <ul id="pagination">
            @if ($paginator->onFirstPage())
                <li class="disabled"><span>Anterior</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="disabled"><span>{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Próximo</a></li>
            @else
                <li class="disabled"><span>Próximo</span></li>
            @endif
        </ul>
    </div>
@endif
