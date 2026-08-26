@extends('layout.site')

@section('content')

{{-- ══════════════════════════════════════════
     TOPO
══════════════════════════════════════════ --}}
<section class="breadcumb_top_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="bread_top_box">
                    <h2>Camisetas</h2>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     BREADCRUMB
══════════════════════════════════════════ --}}
<div class="breadcumb_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="bread_box">
                    <ul class="breadcumb">
                        <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                        <li class="active"><a href="#">Camisetas</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     BARRA DE ORDENAÇÃO (sem filtro — página sem sidebar)
══════════════════════════════════════════ --}}
<div class="filter_area">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-8 col-xs-12">
                <div class="filter_box_left">
                    <div class="s_results">
                        <p>exibindo {{ $produtos->count() }} resultados</p>
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
     GRID DE PRODUTOS (sem sidebar de filtros)
══════════════════════════════════════════ --}}
<section class="main_category_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="main_category_right cat-2">
                    <div class="row">

                        @foreach ($produtos as $produto)
                            <div class="col-md-3 col-sm-4 col-xs-12">
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

@endsection
