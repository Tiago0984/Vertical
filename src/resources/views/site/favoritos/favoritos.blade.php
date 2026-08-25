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
                    <h2>Favoritos</h2>
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
                        <li class="active"><a href="#">Favoritos</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     GRID DE FAVORITOS (renderizado via favoritos.js a partir do localStorage)
══════════════════════════════════════════ --}}
<section class="main_category_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="main_category_right cat-2">

                    <div class="row" id="favoritos-grid" style="display:none;">
                        {{-- Preenchido via JS (favoritos.js) --}}
                    </div>

                    <div class="favoritos-empty" id="favoritos-empty">
                        <i class="fa fa-heart-o"></i>
                        <p class="favoritos-empty-title">Você ainda não tem favoritos</p>
                        <p class="favoritos-empty-msg">Clique no coração dos produtos que você mais gostar para encontrá-los aqui rapidinho.</p>
                        <a href="{{ route('loja') }}" class="favoritos-empty-btn">Explorar produtos</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection
