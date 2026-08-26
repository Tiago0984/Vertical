{{-- ══════════════════════════════════════════
     TOPO
══════════════════════════════════════════ --}}
<section class="error_slider_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="error_slider">
                    <h2>404</h2>
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
                        <li class="active"><a href="#">404</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MENSAGEM DE ERRO
══════════════════════════════════════════ --}}
<section class="error_not_found_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="error_not_found">
                    <img src="{{ asset('vertical/images/not_found.png') }}" alt="" />
                    <h2>Ops! Página não encontrada</h2>
                    <p>Essa página pode ter sido movida ou não existe mais. Confira se o endereço foi digitado corretamente.</p>
                    <a href="{{ route('home') }}" class="btn-voltar-inicio">Voltar para o início</a>
                </div>
            </div>
        </div>
    </div>
</section>
