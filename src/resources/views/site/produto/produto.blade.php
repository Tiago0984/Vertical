@extends('layout.site')

@section('content')

<style>
/* ── Seletor de tamanho ── */
.size-selector { display: flex; gap: 8px; flex-wrap: wrap; margin: 10px 0 18px; }
.size-btn {
    display: inline-block;
    min-width: 46px;
    padding: 7px 10px;
    border: 2px solid #ddd;
    background: #fff;
    color: #333;
    font-family: 'Montserrat', sans-serif;
    font-size: 12px;
    font-weight: 700;
    text-align: center;
    cursor: pointer;
    border-radius: 3px;
    transition: all 0.15s;
    user-select: none;
}
.size-btn:hover, .size-btn.active {
    border-color: #e91e8c;
    color: #e91e8c;
    background: #fff0f7;
}
.size-btn.esgotado {
    border-color: #eee;
    color: #ccc;
    cursor: not-allowed;
    text-decoration: line-through;
}

/* ── Seletor de cor ── */
.color-selector { display: flex; gap: 10px; align-items: center; margin: 10px 0 18px; flex-wrap: wrap; }
.color-dot {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 3px solid transparent;
    cursor: pointer;
    box-shadow: 0 0 0 1px #ccc;
    transition: box-shadow 0.15s, transform 0.15s;
    position: relative;
}
.color-dot:hover, .color-dot.active {
    box-shadow: 0 0 0 2px #e91e8c;
    transform: scale(1.18);
}
.color-dot[title]::after {
    content: attr(title);
    position: absolute;
    bottom: -22px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 10px;
    color: #666;
    white-space: nowrap;
    font-family: 'Cabin', sans-serif;
    pointer-events: none;
}
.color-label { font-size: 12px; color: #777; margin-left: 4px; }

/* ── Guia de tamanhos link ── */
.size-guide-link {
    font-size: 12px;
    color: #e91e8c;
    text-decoration: underline;
    cursor: pointer;
    display: inline-block;
    margin-bottom: 14px;
}
.size-guide-link i { margin-right: 4px; }

/* ── Descrição do produto ── */
.product-specs { margin: 0; padding: 0; list-style: none; }
.product-specs li {
    padding: 9px 0;
    border-bottom: 1px solid #f0f0f0;
    font-family: 'Cabin', sans-serif;
    font-size: 14px;
    color: #444;
}
.product-specs li strong { color: #222; min-width: 120px; display: inline-block; }
.product-specs li:last-child { border-bottom: none; }

/* ── Modal tabela de medidas ── */
#modal-medidas .modal-header { background: #e91e8c; color: #fff; border-radius: 4px 4px 0 0; }
#modal-medidas .modal-title { font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 16px; }
#modal-medidas .modal-header .close { color: #fff; opacity: 1; }
.tabela-medidas { width: 100%; border-collapse: collapse; font-family: 'Cabin', sans-serif; font-size: 13px; }
.tabela-medidas th {
    background: #f8f8f8;
    color: #333;
    padding: 10px 14px;
    text-align: center;
    font-weight: 700;
    border: 1px solid #e0e0e0;
}
.tabela-medidas td {
    padding: 9px 14px;
    text-align: center;
    border: 1px solid #e8e8e8;
    color: #555;
}
.tabela-medidas tr:nth-child(even) td { background: #fafafa; }
.tabela-medidas tr:hover td { background: #fff0f7; }

/* ── Produto relacionado ── */
.related-item { margin-bottom: 20px; }
.related-item .item-img { position: relative; overflow: hidden; }
.related-item .item-img img { width: 100%; display: block; transition: transform 0.3s; }
.related-item .item-img:hover img { transform: scale(1.05); }
.related-item h5 {
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 600;
    margin: 10px 0 4px;
    color: #333;
}
.related-item .preco { color: #e91e8c; font-weight: 700; font-size: 14px; }
.related-item .preco del { color: #aaa; font-weight: 400; font-size: 12px; margin-left: 6px; }

/* ── Botão carrinho / favoritos ── */
.btn-cart-main {
    background: #e91e8c;
    color: #fff;
    border: none;
    padding: 14px 30px;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 3px;
    cursor: pointer;
    transition: background 0.2s;
}
.btn-cart-main:hover { background: #c2185b; }
.btn-fav-main {
    background: #fff;
    color: #e91e8c;
    border: 2px solid #e91e8c;
    padding: 12px 18px;
    border-radius: 3px;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.2s;
    margin-left: 8px;
}
.btn-fav-main:hover { background: #e91e8c; color: #fff; }
.qty-wrap { display: flex; align-items: center; gap: 0; margin-bottom: 16px; }
.qty-wrap input[type=number] {
    width: 58px;
    text-align: center;
    border: 2px solid #ddd;
    padding: 10px 6px;
    font-size: 14px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    outline: none;
    border-radius: 3px;
}
.qty-wrap input[type=number]::-webkit-inner-spin-button { opacity: 1; }
</style>

{{-- ══════════════════════════════════════════
     BREADCRUMB
══════════════════════════════════════════ --}}
<div class="breadcumb_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="bread_box">
                    <ul class="breadcumb">
                        <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                        <li><a href="{{ route('loja') }}">Loja <span>|</span></a></li>
                        <li><a href="{{ route('categorias') }}">Camisetas <span>|</span></a></li>
                        <li class="active"><a href="#">Camiseta Estampada Street Art</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     DETALHE DO PRODUTO
══════════════════════════════════════════ --}}
<section class="product_detail_area" style="padding: 50px 0;">
    <div class="container">
        <div class="row">

            {{-- Galeria de imagens --}}
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="product_detail_img">

                    {{-- Imagem principal --}}
                    <div id="img-principal" style="border: 1px solid #f0f0f0; border-radius: 4px; overflow: hidden; margin-bottom: 12px;">
                        <img id="foto-principal" src="{{ asset('vertical/images/product_detail_lg.png') }}"
                             alt="Camiseta Estampada Street Art"
                             style="width:100%; display:block; transition: opacity 0.2s;" />
                    </div>

                    {{-- Miniaturas --}}
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <img src="{{ asset('vertical/images/product_detail.png') }}"
                             alt="thumb 1"
                             onclick="document.getElementById('foto-principal').src=this.src.replace('product_detail','product_detail_lg')"
                             style="width:70px; height:70px; object-fit:cover; border:2px solid #e91e8c; border-radius:3px; cursor:pointer;" />
                        <img src="{{ asset('vertical/images/product_detail2.png') }}"
                             alt="thumb 2"
                             onclick="document.getElementById('foto-principal').src=this.src.replace('product_detail2','product_detail_lg2')"
                             style="width:70px; height:70px; object-fit:cover; border:2px solid #ddd; border-radius:3px; cursor:pointer;" />
                        <img src="{{ asset('vertical/images/product_detail3.png') }}"
                             alt="thumb 3"
                             onclick="document.getElementById('foto-principal').src=this.src.replace('product_detail3','product_detail_lg3')"
                             style="width:70px; height:70px; object-fit:cover; border:2px solid #ddd; border-radius:3px; cursor:pointer;" />
                        <img src="{{ asset('vertical/images/product_detail_05.png') }}"
                             alt="thumb 4"
                             onclick="document.getElementById('foto-principal').src=this.src"
                             style="width:70px; height:70px; object-fit:cover; border:2px solid #ddd; border-radius:3px; cursor:pointer;" />
                    </div>

                </div>
            </div>

            {{-- Informações do produto --}}
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="product_detail_text" style="padding-left: 20px;">

                    {{-- Badge --}}
                    <span style="display:inline-block; background:#e91e8c; color:#fff; font-family:'Montserrat',sans-serif; font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px; letter-spacing:1px; margin-bottom:12px;">NOVO</span>

                    {{-- Nome --}}
                    <h2 style="font-family:'Montserrat',sans-serif; font-size:26px; font-weight:700; color:#232323; margin:0 0 6px;">
                        Camiseta Estampada Street Art
                    </h2>

                    {{-- Avaliação --}}
                    <div style="margin-bottom: 10px;">
                        <i class="fa fa-star" style="color:#f5a623;"></i>
                        <i class="fa fa-star" style="color:#f5a623;"></i>
                        <i class="fa fa-star" style="color:#f5a623;"></i>
                        <i class="fa fa-star" style="color:#f5a623;"></i>
                        <i class="fa fa-star-half-o" style="color:#f5a623;"></i>
                        <span style="font-size:12px; color:#999; margin-left:6px;">(42 avaliações)</span>
                    </div>

                    {{-- Preço --}}
                    <div style="margin-bottom: 18px;">
                        <span style="font-family:'Montserrat',sans-serif; font-size:28px; font-weight:700; color:#e91e8c;">R$69,90</span>
                        <span style="font-size:16px; color:#aaa; margin-left:10px; text-decoration:line-through;">R$89,90</span>
                        <span style="background:#e53935; color:#fff; font-size:11px; font-weight:700; padding:3px 8px; border-radius:3px; margin-left:8px;">-22%</span>
                    </div>

                    <hr style="border-color:#f0f0f0; margin: 0 0 16px;">

                    {{-- Seletor de COR --}}
                    <div>
                        <p style="font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; color:#333; text-transform:uppercase; margin-bottom:4px;">
                            Cor: <span id="cor-selecionada" style="color:#e91e8c; font-weight:400; text-transform:none;">Branca</span>
                        </p>
                        <div class="color-selector">
                            <div class="color-dot active" style="background:#FFFFFF;" title="Branca"
                                 onclick="selecionarCor(this,'Branca')"></div>
                            <div class="color-dot" style="background:#1a1a1a;" title="Preta"
                                 onclick="selecionarCor(this,'Preta')"></div>
                            <div class="color-dot" style="background:#9E9E9E;" title="Cinza"
                                 onclick="selecionarCor(this,'Cinza')"></div>
                            <div class="color-dot" style="background:#1565C0;" title="Azul"
                                 onclick="selecionarCor(this,'Azul')"></div>
                            <div class="color-dot" style="background:#C62828;" title="Vermelha"
                                 onclick="selecionarCor(this,'Vermelha')"></div>
                            <div class="color-dot" style="background:#2E7D32;" title="Verde"
                                 onclick="selecionarCor(this,'Verde')"></div>
                        </div>
                    </div>

                    {{-- Seletor de TAMANHO --}}
                    <div>
                        <p style="font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; color:#333; text-transform:uppercase; margin-bottom:4px;">
                            Tamanho: <span id="tamanho-selecionado" style="color:#e91e8c; font-weight:400; text-transform:none;">—</span>
                        </p>
                        <div class="size-selector">
                            <div class="size-btn" onclick="selecionarTamanho(this)">PP</div>
                            <div class="size-btn" onclick="selecionarTamanho(this)">P</div>
                            <div class="size-btn" onclick="selecionarTamanho(this)">M</div>
                            <div class="size-btn" onclick="selecionarTamanho(this)">G</div>
                            <div class="size-btn" onclick="selecionarTamanho(this)">GG</div>
                            <div class="size-btn" onclick="selecionarTamanho(this)">XGG</div>
                        </div>
                        <a class="size-guide-link" data-toggle="modal" data-target="#modal-medidas">
                            <i class="fa fa-arrows-h"></i> Ver tabela de medidas
                        </a>
                    </div>

                    {{-- Quantidade + Botões --}}
                    <div style="margin-top: 6px;">
                        <p style="font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; color:#333; text-transform:uppercase; margin-bottom:6px;">Quantidade:</p>
                        <div class="qty-wrap">
                            <input type="number" id="quantidade" value="1" min="1" max="99" />
                        </div>
                        <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-top:10px;">
                            <button class="btn-cart-main" onclick="adicionarAoCarrinho()">
                                <i class="fa fa-shopping-cart" style="margin-right:8px;"></i>ADICIONAR AO CARRINHO
                            </button>
                            <button class="btn-fav-main" title="Adicionar aos favoritos" onclick="toggleFavorito(this)">
                                <i class="fa fa-heart-o"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Informações rápidas --}}
                    <div style="margin-top:22px; padding-top:16px; border-top:1px solid #f0f0f0;">
                        <p style="font-size:13px; color:#666; font-family:'Cabin',sans-serif; margin-bottom:5px;">
                            <i class="fa fa-check" style="color:#4caf50; margin-right:6px;"></i>Em estoque — envio em até 2 dias úteis
                        </p>
                        <p style="font-size:13px; color:#666; font-family:'Cabin',sans-serif; margin-bottom:5px;">
                            <i class="fa fa-truck" style="color:#e91e8c; margin-right:6px;"></i>Frete grátis para pedidos acima de R$150
                        </p>
                        <p style="font-size:13px; color:#666; font-family:'Cabin',sans-serif; margin-bottom:0;">
                            <i class="fa fa-refresh" style="color:#e91e8c; margin-right:6px;"></i>Troca em até 30 dias
                        </p>
                    </div>

                </div>
            </div>

        </div>{{-- /row principal --}}
    </div>{{-- /container --}}
</section>

{{-- ══════════════════════════════════════════
     ABAS: DESCRIÇÃO / MATERIAL / AVALIAÇÕES
══════════════════════════════════════════ --}}
<section style="background:#fafafa; padding:40px 0 50px;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div role="tabpanel">

                    <ul class="nav nav-tabs" role="tablist"
                        style="border-bottom:2px solid #e91e8c; margin-bottom:24px;">
                        <li role="presentation" class="active">
                            <a href="#aba-descricao" role="tab" data-toggle="tab"
                               style="font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; text-transform:uppercase;">
                               Descrição
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#aba-material" role="tab" data-toggle="tab"
                               style="font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; text-transform:uppercase;">
                               Material &amp; Cuidados
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#aba-avaliacoes" role="tab" data-toggle="tab"
                               style="font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; text-transform:uppercase;">
                               Avaliações <span style="background:#e91e8c; color:#fff; border-radius:10px; padding:1px 7px; font-size:10px;">42</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        {{-- Descrição --}}
                        <div role="tabpanel" class="tab-pane active" id="aba-descricao">
                            <div class="row">
                                <div class="col-md-8">
                                    <p style="font-family:'Cabin',sans-serif; font-size:15px; color:#555; line-height:1.8; margin-bottom:16px;">
                                        Camiseta com estampa exclusiva inspirada na arte urbana. Malha premium de algodão de alta qualidade, com toque macio e conforto durante todo o dia. Ideal para compor looks casuais com personalidade.
                                    </p>
                                    <p style="font-family:'Cabin',sans-serif; font-size:15px; color:#555; line-height:1.8;">
                                        Disponível em 6 cores e todos os tamanhos, do PP ao XGG. As estampas são aplicadas com tecnologia de sublimação resistente a múltiplas lavagens, mantendo a cor e o brilho originais por muito mais tempo.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Material & Cuidados --}}
                        <div role="tabpanel" class="tab-pane" id="aba-material">
                            <div class="row">
                                <div class="col-md-7">
                                    <ul class="product-specs">
                                        <li>
                                            <strong>Material:</strong>
                                            100% Algodão Fio 30
                                        </li>
                                        <li>
                                            <strong>Gramatura:</strong>
                                            160g/m²
                                        </li>
                                        <li>
                                            <strong>Modelagem:</strong>
                                            Regular Fit
                                        </li>
                                        <li>
                                            <strong>Origem:</strong>
                                            Fabricado no Brasil
                                        </li>
                                        <li>
                                            <strong>Cuidados:</strong>
                                            Lavar à mão ou máquina fria (30°C), não usar alvejante, não torcer, secar à sombra
                                        </li>
                                        <li>
                                            <strong>Certificação:</strong>
                                            OEKO-TEX® Standard 100 — produto testado contra substâncias nocivas
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-5" style="display:flex; align-items:center; justify-content:center; padding-top:10px;">
                                    <div style="text-align:center; background:#fff; border:1px solid #eee; border-radius:8px; padding:20px;">
                                        <i class="fa fa-tint" style="font-size:36px; color:#1565C0; display:block; margin-bottom:10px;"></i>
                                        <p style="font-family:'Cabin',sans-serif; font-size:13px; color:#555; margin:0;">Lavar fria</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Avaliações --}}
                        <div role="tabpanel" class="tab-pane" id="aba-avaliacoes">
                            <div class="row">
                                <div class="col-md-8">

                                    {{-- Avaliação 1 --}}
                                    <div style="border-bottom:1px solid #f0f0f0; padding-bottom:20px; margin-bottom:20px;">
                                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                                            <div style="width:42px; height:42px; background:#e91e8c; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-family:'Montserrat',sans-serif; font-weight:700; font-size:16px;">M</div>
                                            <div>
                                                <strong style="font-family:'Montserrat',sans-serif; font-size:13px;">Maria S.</strong>
                                                <div>
                                                    <i class="fa fa-star" style="color:#f5a623; font-size:11px;"></i>
                                                    <i class="fa fa-star" style="color:#f5a623; font-size:11px;"></i>
                                                    <i class="fa fa-star" style="color:#f5a623; font-size:11px;"></i>
                                                    <i class="fa fa-star" style="color:#f5a623; font-size:11px;"></i>
                                                    <i class="fa fa-star" style="color:#f5a623; font-size:11px;"></i>
                                                    <span style="font-size:11px; color:#aaa; margin-left:4px;">há 3 dias</span>
                                                </div>
                                            </div>
                                        </div>
                                        <p style="font-family:'Cabin',sans-serif; font-size:14px; color:#555; margin:0;">Camiseta incrível! O algodão é super macio e a estampa ficou perfeita depois de várias lavagens. Comprei tamanho M e caiu certinho.</p>
                                    </div>

                                    {{-- Avaliação 2 --}}
                                    <div style="border-bottom:1px solid #f0f0f0; padding-bottom:20px; margin-bottom:20px;">
                                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                                            <div style="width:42px; height:42px; background:#1565C0; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-family:'Montserrat',sans-serif; font-weight:700; font-size:16px;">J</div>
                                            <div>
                                                <strong style="font-family:'Montserrat',sans-serif; font-size:13px;">João P.</strong>
                                                <div>
                                                    <i class="fa fa-star" style="color:#f5a623; font-size:11px;"></i>
                                                    <i class="fa fa-star" style="color:#f5a623; font-size:11px;"></i>
                                                    <i class="fa fa-star" style="color:#f5a623; font-size:11px;"></i>
                                                    <i class="fa fa-star" style="color:#f5a623; font-size:11px;"></i>
                                                    <i class="fa fa-star-o" style="color:#f5a623; font-size:11px;"></i>
                                                    <span style="font-size:11px; color:#aaa; margin-left:4px;">há 1 semana</span>
                                                </div>
                                            </div>
                                        </div>
                                        <p style="font-family:'Cabin',sans-serif; font-size:14px; color:#555; margin:0;">Muito boa qualidade! Entrega rápida e embalagem excelente. Só tirei uma estrela porque o tamanho G ficou um pouco largo — sugiro pedir um tamanho a menos se preferir mais ajustado.</p>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>{{-- /tab-content --}}
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     PRODUTOS RELACIONADOS
══════════════════════════════════════════ --}}
<section style="padding: 50px 0 60px; background:#fff;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3 style="font-family:'Montserrat',sans-serif; font-size:20px; font-weight:700; color:#232323; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">
                    Você também pode gostar
                </h3>
                <div class="multi_line" style="margin-bottom:28px;"></div>
            </div>
        </div>
        <div class="row">

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="related-item">
                    <div class="item-img">
                        <img src="{{ asset('vertical/images/t_item1.jpg') }}" alt="Camiseta Básica Branca" />
                        <div class="tr-add-cart">
                            <ul>
                                <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                            </ul>
                        </div>
                    </div>
                    <h5><a href="{{ route('produto') }}" style="color:#333; text-decoration:none;">Camiseta Básica Branca</a></h5>
                    <p class="preco">R$49,90</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="related-item">
                    <div class="item-img">
                        <img src="{{ asset('vertical/images/t_item3.jpg') }}" alt="Camiseta Oversized Preta" />
                        <div class="tr-add-cart">
                            <ul>
                                <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                            </ul>
                        </div>
                    </div>
                    <h5><a href="{{ route('produto') }}" style="color:#333; text-decoration:none;">Camiseta Oversized Preta</a></h5>
                    <p class="preco">R$79,90</p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="related-item">
                    <div class="item-img">
                        <img src="{{ asset('vertical/images/t_item12.jpg') }}" alt="Camiseta Gola V Azul" />
                        <div class="tr-add-cart">
                            <ul>
                                <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                            </ul>
                        </div>
                    </div>
                    <h5><a href="{{ route('produto') }}" style="color:#333; text-decoration:none;">Camiseta Gola V Azul</a></h5>
                    <p class="preco">
                        R$54,90 <del>R$74,90</del>
                        <span style="background:#e53935; color:#fff; font-size:10px; padding:2px 6px; border-radius:3px; margin-left:4px;">-26%</span>
                    </p>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="related-item">
                    <div class="item-img">
                        <img src="{{ asset('vertical/images/t_item4.jpg') }}" alt="Camiseta Dry-Fit Sport" />
                        <div class="tr-add-cart">
                            <ul>
                                <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                            </ul>
                        </div>
                    </div>
                    <h5><a href="{{ route('produto') }}" style="color:#333; text-decoration:none;">Camiseta Dry-Fit Sport</a></h5>
                    <p class="preco">R$59,90</p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     MODAL — TABELA DE MEDIDAS
══════════════════════════════════════════ --}}
<div class="modal fade" id="modal-medidas" tabindex="-1" role="dialog" aria-labelledby="modal-medidas-titulo">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" id="modal-medidas">
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modal-medidas-titulo">
                    <i class="fa fa-arrows-h" style="margin-right:8px;"></i>Guia de Tamanhos — Camisetas
                </h4>
            </div>
            <div class="modal-body" style="padding: 24px;">

                <p style="font-family:'Cabin',sans-serif; font-size:13px; color:#666; margin-bottom:18px;">
                    Todas as medidas estão em <strong>centímetros (cm)</strong>. Meça seu corpo e compare com a tabela abaixo para encontrar o tamanho ideal.
                </p>

                <table class="tabela-medidas">
                    <thead>
                        <tr>
                            <th>Tamanho</th>
                            <th>Busto (cm)</th>
                            <th>Largura (cm)</th>
                            <th>Comprimento (cm)</th>
                            <th>Manga (cm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>PP</strong></td>
                            <td>80 – 84</td>
                            <td>46</td>
                            <td>65</td>
                            <td>20</td>
                        </tr>
                        <tr>
                            <td><strong>P</strong></td>
                            <td>84 – 88</td>
                            <td>49</td>
                            <td>67</td>
                            <td>21</td>
                        </tr>
                        <tr>
                            <td><strong>M</strong></td>
                            <td>88 – 96</td>
                            <td>52</td>
                            <td>69</td>
                            <td>22</td>
                        </tr>
                        <tr>
                            <td><strong>G</strong></td>
                            <td>96 – 104</td>
                            <td>56</td>
                            <td>71</td>
                            <td>23</td>
                        </tr>
                        <tr>
                            <td><strong>GG</strong></td>
                            <td>104 – 112</td>
                            <td>60</td>
                            <td>73</td>
                            <td>24</td>
                        </tr>
                        <tr>
                            <td><strong>XGG</strong></td>
                            <td>112 – 122</td>
                            <td>64</td>
                            <td>76</td>
                            <td>25</td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top:18px; background:#fff8f0; border-left:4px solid #f5a623; padding:12px 16px; border-radius:0 4px 4px 0;">
                    <p style="margin:0; font-family:'Cabin',sans-serif; font-size:13px; color:#555;">
                        <i class="fa fa-lightbulb-o" style="color:#f5a623; margin-right:6px;"></i>
                        <strong>Dica:</strong> Se suas medidas ficarem entre dois tamanhos, recomendamos escolher o maior para maior conforto. Para um look mais ajustado, escolha o menor.
                    </p>
                </div>

            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f0f0;">
                <button type="button" class="btn-cart-main" data-dismiss="modal" style="padding:10px 24px; font-size:12px;">
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     SCRIPTS DE INTERAÇÃO
══════════════════════════════════════════ --}}
<script>
function selecionarCor(el, nome) {
    document.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('cor-selecionada').textContent = nome;
}

function selecionarTamanho(el) {
    if (el.classList.contains('esgotado')) return;
    document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('tamanho-selecionado').textContent = el.textContent.trim();
}

function adicionarAoCarrinho() {
    const tamanho = document.getElementById('tamanho-selecionado').textContent;
    const cor = document.getElementById('cor-selecionada').textContent;
    const qty = document.getElementById('quantidade').value;

    if (tamanho === '—') {
        alert('Por favor, selecione um tamanho antes de adicionar ao carrinho.');
        return;
    }
    alert('Produto adicionado ao carrinho!\n\nCamiseta Estampada Street Art\nCor: ' + cor + ' | Tamanho: ' + tamanho + ' | Qty: ' + qty);
}

function toggleFavorito(btn) {
    const icon = btn.querySelector('i');
    if (icon.classList.contains('fa-heart-o')) {
        icon.classList.replace('fa-heart-o', 'fa-heart');
        btn.style.background = '#e91e8c';
        btn.style.color = '#fff';
        btn.title = 'Remover dos favoritos';
    } else {
        icon.classList.replace('fa-heart', 'fa-heart-o');
        btn.style.background = '#fff';
        btn.style.color = '#e91e8c';
        btn.title = 'Adicionar aos favoritos';
    }
}
</script>

@endsection
