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
                        <p><span>|</span> exibindo {{ $produtos->count() }} resultados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-4 col-xs-12">
                <div class="filter_box_right">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">ordenar por novidade <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Mais vendidos</a></li>
                        <li><a href="#">Mais populares</a></li>
                        <li><a href="#">Ordem alfabética</a></li>
                        <li><a href="#">Mais antigos</a></li>
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
                                        @foreach ($categorias as $categoria)
                                            <li><a href="#">{{ $categoria->nome }} ({{ $categoria->products_count }})</a></li>
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
                                    <div id="slider-range"></div>
                                    <div class="cat_filter_box">
                                        <p>
                                            <label for="amount">Filtro</label>
                                            <input type="text" id="amount" readonly style="border:0; color:#000; font-weight:bold;">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Cores --}}
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingCatThree">
                                <h4 class="panel-title">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#home-accordion" href="#collapseCatThree" aria-expanded="false" aria-controls="collapseCatThree">
                                        CORES
                                        <span class="floatright"><i class="fa fa-plus"></i></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseCatThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingCatThree">
                                <div class="panel-body colors_cat">
                                    <ul id="cat_color">
                                        <li><a class="col-1" href="#"></a></li>
                                        <li><a class="col-2" href="#"></a></li>
                                        <li><a class="col-3" href="#"></a></li>
                                        <li><a class="col-4" href="#"></a></li>
                                        <li><a class="col-5" href="#"></a></li>
                                        <li><a class="col-6" href="#"></a></li>
                                        <li><a class="col-7" href="#"></a></li>
                                        <li><a class="col-8" href="#"></a></li>
                                        <li><a class="col-9" href="#"></a></li>
                                        <li><a class="col-10" href="#"></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Tamanho --}}
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingCatFour">
                                <h4 class="panel-title">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#home-accordion" href="#collapseCatFour" aria-expanded="false" aria-controls="collapseCatFour">
                                        TAMANHO
                                        <span class="floatright"><i class="fa fa-plus"></i></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseCatFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingCatFour">
                                <div class="panel-body">
                                    <ul id="cat_size">
                                        <li><a href="#">pp</a></li>
                                        <li><a href="#">p</a></li>
                                        <li><a href="#">m</a></li>
                                        <li><a href="#">g</a></li>
                                        <li><a href="#">gg</a></li>
                                        <li><a href="#">xgg</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Marcas --}}
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingCatFive">
                                <h4 class="panel-title">
                                    <a class="collapsed" data-toggle="collapse" data-parent="#home-accordion" href="#collapseCatFive" aria-expanded="false" aria-controls="collapseCatFive">
                                        MARCAS
                                        <span class="floatright"><i class="fa fa-plus"></i></span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseCatFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingCatFive">
                                <div class="panel-body">
                                    <ul id="c_tab2">
                                        <li><a href="#">Vertical Basics (72)</a></li>
                                        <li><a href="#">Urban Street (14)</a></li>
                                        <li><a href="#">Active Sport (23)</a></li>
                                        <li><a href="#">Premium Line (19)</a></li>
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

                        @foreach ($produtos as $produto)
                            <div class="col-md-4 col-sm-6 col-xs-12 produto-col">
                                @include('partials.product-card', ['product' => $produto])
                            </div>
                        @endforeach

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
