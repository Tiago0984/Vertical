{{-- ══════════════════════════════════════════
     BARRA DE FILTRO / ORDENAÇÃO
══════════════════════════════════════════ --}}
<div class="filter_area">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-8 col-xs-12">
                <div class="filter_box_left">
                    <p>FILTRAR:</p>
                    <div class="filter_cont">
                        <ul>
                            <li><a href="#" id="filtro-toggle-on" class="active">on</a></li>
                            <li><img src="{{ asset('vertical/images/filter_ico.png') }}" id="filtro-toggle-icon" alt="" /></li>
                            <li><a href="#" id="filtro-toggle-off">off</a></li>
                        </ul>
                    </div>
                    <div class="s_results">
                        <p>
                            <span>|</span> exibindo {{ $produtos->count() }} resultados
                            @if ($temFiltroAtivo)
                                &nbsp;&mdash;&nbsp;<a href="{{ $urlLimparFiltros }}">limpar filtros</a>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-4 col-xs-12">
                <div class="filter_box_right">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">ordenar por {{ \Illuminate\Support\Str::lower($ordemAtualLabel) }} <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        @foreach ($opcoesOrdem as $opcao)
                            <li class="{{ $opcao['ativa'] ? 'active' : '' }}">
                                <a href="{{ $opcao['url'] }}" style="{{ $opcao['ativa'] ? 'font-weight:700;' : '' }}">{{ $opcao['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     GRID DE PRODUTOS + FILTROS LATERAIS
══════════════════════════════════════════ --}}
<section class="main_category_area">
    <div class="container">
        <div class="row">

            {{-- Filtros laterais --}}
            <div class="col-md-3 col-sm-4 col-xs-12" id="filtros-sidebar-col">
                <div class="main_category_left">

                    <div class="panel-group" id="home-accordion" role="tablist" aria-multiselectable="true">

                        {{-- Categorias --}}
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingCatOne">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#home-accordion" href="#collapseCatOne" aria-expanded="true" aria-controls="collapseCatOne">
                                        CATEGORIAS
                                        <span class="floatright"><i class="fa fa-minus"></i></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseCatOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingCatOne">
                                <div class="panel-body">
                                    <ul id="c_tab1">
                                        <li>
                                            <a href="{{ $urlTodasCategorias }}" style="{{ ! $categorias->contains('ativa', true) ? 'font-weight:700;' : '' }}">Todas as categorias</a>
                                        </li>
                                        @foreach ($categorias as $categoria)
                                            <li>
                                                <a href="{{ $categoria['url'] }}" style="{{ $categoria['ativa'] ? 'font-weight:700; text-decoration:underline;' : '' }}">
                                                    {{ $categoria['nome'] }} ({{ $categoria['products_count'] }})
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Filtro de preço --}}
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingCatTwo">
                                <h4 class="panel-title">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#home-accordion" href="#collapseCatTwo" aria-expanded="false" aria-controls="collapseCatTwo">
                                        FAIXA DE PREÇO
                                        <span class="floatright"><i class="fa fa-plus"></i></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseCatTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingCatTwo">
                                <div class="panel-body">
                                    {{-- Faixas fixas, não slider -- o template tinha um jQuery UI
                                         slider (#slider-range) que nunca foi ligado a nada; faixa
                                         pré-definida é o que dá pra manter funcionando de verdade
                                         sem entrar em trabalho de front que não se paga aqui. --}}
                                    <ul id="c_tab_preco">
                                        <li>
                                            <a href="{{ $urlTodosPrecos }}" style="{{ ! $faixasPreco->contains('ativa', true) ? 'font-weight:700;' : '' }}">Todos os preços</a>
                                        </li>
                                        @foreach ($faixasPreco as $faixa)
                                            <li>
                                                <a href="{{ $faixa['url'] }}" style="{{ $faixa['ativa'] ? 'font-weight:700; text-decoration:underline;' : '' }}">
                                                    {{ $faixa['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Grid de produtos --}}
            <div class="col-md-9 col-sm-8 col-xs-12" id="produtos-grid-col">
                <div class="main_category_right">
                    <div class="row">

                        @forelse ($produtos as $produto)
                            <div class="col-md-4 col-sm-6 col-xs-12 produto-col">
                                @include('partials.product-card', ['product' => $produto])
                            </div>
                        @empty
                            <div class="col-md-12">
                                <p style="text-align:center; color:#777; padding:40px 0;">
                                    Nenhum produto encontrado com esse filtro.
                                    <a href="{{ $urlLimparFiltros }}">Limpar filtros</a>.
                                </p>
                            </div>
                        @endforelse

                    </div>

                    {{-- Paginação --}}
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="pagi_line"></div>
                            <div class="pagi_ul">
                                <ul id="pagination">
                                    <li><a href="#">Anterior</a></li>
                                    <li><a href="#">1</a></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#">Próximo</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
/* ── Toggle ON/OFF do filtro lateral (category-1 vs category-2 do template original) ── */
document.addEventListener('DOMContentLoaded', function () {
    var btnOn = document.getElementById('filtro-toggle-on');
    var btnOff = document.getElementById('filtro-toggle-off');
    var icon = document.getElementById('filtro-toggle-icon');
    var sidebar = document.getElementById('filtros-sidebar-col');
    var gridCol = document.getElementById('produtos-grid-col');
    var produtoCols = document.querySelectorAll('.produto-col');

    if (!btnOn || !btnOff || !sidebar || !gridCol) return;

    function setFiltro(ativo) {
        btnOn.classList.toggle('active', ativo);
        btnOff.classList.toggle('active', !ativo);
        icon.src = ativo
            ? '{{ asset("vertical/images/filter_ico.png") }}'
            : '{{ asset("vertical/images/filter_ico_off.png") }}';

        if (ativo) {
            sidebar.style.display = '';
            gridCol.classList.remove('col-md-12');
            gridCol.classList.add('col-md-9');
            produtoCols.forEach(function (col) {
                col.classList.remove('col-md-3');
                col.classList.add('col-md-4');
            });
        } else {
            sidebar.style.display = 'none';
            gridCol.classList.remove('col-md-9');
            gridCol.classList.add('col-md-12');
            produtoCols.forEach(function (col) {
                col.classList.remove('col-md-4');
                col.classList.add('col-md-3');
            });
        }
    }

    btnOn.addEventListener('click', function (e) {
        e.preventDefault();
        setFiltro(true);
    });
    btnOff.addEventListener('click', function (e) {
        e.preventDefault();
        setFiltro(false);
    });
});
</script>
