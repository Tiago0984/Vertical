@extends('layout.site')

@section('content')

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
                        <li class="active"><a href="#">{{ $produto->nome }}</a></li>
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
                    <div id="img-principal" style="border: 1px solid #f0f0f0; border-radius: 4px; overflow: hidden;">
                        <img id="foto-principal" src="{{ asset($produto->imagem) }}"
                             alt="{{ $produto->nome }}"
                             style="width:100%; display:block; transition: opacity 0.2s;" />
                    </div>

                </div>
            </div>

            {{-- Informações do produto --}}
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="product_detail_text" style="padding-left: 20px;">

                    {{-- Badge --}}
                    @if ($produto->is_novo || $produto->is_promocao)
                        <span style="display:inline-block; background:#000; color:#fff; font-size:11px; font-weight:700; padding:4px 12px; border-radius:20px; letter-spacing:1px; margin-bottom:12px;">
                            {{ $produto->is_novo ? 'NOVO' : 'OFERTA' }}
                        </span>
                    @endif

                    {{-- Nome --}}
                    <h2 style="font-size:26px; font-weight:700; color:#232323; margin:0 0 6px;">
                        {{ $produto->nome }}
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
                        <span style="font-size:28px; font-weight:700; color:#000;">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                        @if ($produto->is_promocao)
                            <span style="font-size:16px; color:#aaa; margin-left:10px; text-decoration:line-through;">R$ {{ number_format($produto->preco_promocional, 2, ',', '.') }}</span>
                            <span style="background:#d90000; color:#fff; font-size:11px; font-weight:700; padding:3px 8px; border-radius:3px; margin-left:8px;">
                                -{{ round((($produto->preco_promocional - $produto->preco) / $produto->preco_promocional) * 100) }}%
                            </span>
                        @endif
                    </div>

                    <hr style="border-color:#f0f0f0; margin: 0 0 16px;">

                    {{-- Seletor de COR --}}
                    <div>
                        <p style="font-size:12px; font-weight:700; color:#333; text-transform:uppercase; margin-bottom:4px;">
                            Cor: <span id="cor-selecionada" style="color:#000; font-weight:400; text-transform:none;">Branca</span>
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
                    <div style="margin-top: 12px;">
                        <p style="font-size:12px; font-weight:700; color:#333; text-transform:uppercase; margin-bottom:4px;">
                            Tamanho: <span id="tamanho-selecionado" style="color:#000; font-weight:400; text-transform:none;">—</span>
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
                        <p style="font-size:12px; font-weight:700; color:#333; text-transform:uppercase; margin-bottom:6px;">Quantidade:</p>
                        <div class="qty-wrap">
                            <input type="number" id="quantidade" value="1" min="1" max="99" />
                        </div>
                        <div style="display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-top:10px;">
                            <button class="btn-cart-main" onclick="adicionarAoCarrinho()">
                                <i class="fa fa-shopping-cart" style="margin-right:8px;"></i>ADICIONAR AO CARRINHO
                            </button>
                            <button class="btn-fav-main" title="Adicionar aos favoritos"
                                    data-product-id="{{ $produto->id }}"
                                    data-product-nome="{{ $produto->nome }}"
                                    data-product-preco="{{ $produto->preco }}"
                                    data-product-imagem="{{ asset($produto->imagem) }}">
                                <i class="fa fa-heart-o"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Informações rápidas --}}
                    <div style="margin-top:22px; padding-top:16px; border-top:1px solid #f0f0f0;">
                        <p style="font-size:13px; color:#666; margin-bottom:5px;">
                            <i class="fa fa-check" style="color:#4caf50; margin-right:6px;"></i>Em estoque — envio em até 2 dias úteis
                        </p>
                        <p style="font-size:13px; color:#666; margin-bottom:5px;">
                            <i class="fa fa-truck" style="color:#000; margin-right:6px;"></i>Frete grátis para pedidos acima de R$150
                        </p>
                        <p style="font-size:13px; color:#666; margin-bottom:0;">
                            <i class="fa fa-refresh" style="color:#000; margin-right:6px;"></i>Troca em até 30 dias
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
                        style="border-bottom:2px solid #000; margin-bottom:24px;">
                        <li role="presentation" class="active">
                            <a href="#aba-descricao" role="tab" data-toggle="tab"
                               style="font-size:12px; font-weight:700; text-transform:uppercase;">
                               Descrição
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#aba-material" role="tab" data-toggle="tab"
                               style="font-size:12px; font-weight:700; text-transform:uppercase;">
                               Material &amp; Cuidados
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#aba-avaliacoes" role="tab" data-toggle="tab"
                               style="font-size:12px; font-weight:700; text-transform:uppercase;">
                               Avaliações <span style="background:#000; color:#fff; border-radius:10px; padding:1px 7px; font-size:10px;">42</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        {{-- Descrição --}}
                        <div role="tabpanel" class="tab-pane active" id="aba-descricao">
                            <div class="row">
                                <div class="col-md-8">
                                    <p style="font-size:15px; color:#555; line-height:1.8; margin-bottom:16px;">
                                        {{ $produto->descricao ?: $produto->nome . '. Malha premium de algodão de alta qualidade, com toque macio e conforto durante todo o dia. Ideal para compor looks casuais com personalidade.' }}
                                    </p>
                                    <p style="font-size:15px; color:#555; line-height:1.8;">
                                        Disponível em 6 cores e todos os tamanhos, do PP ao XGG.
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
                                        <p style="font-size:13px; color:#555; margin:0;">Lavar fria</p>
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
                                            <div style="width:42px; height:42px; background:#000; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:16px;">M</div>
                                            <div>
                                                <strong style="font-size:13px;">Maria S.</strong>
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
                                        <p style="font-size:14px; color:#555; margin:0;">Camiseta incrível! O algodão é super macio e a estampa ficou perfeita depois de várias lavagens. Comprei tamanho M e caiu certinho.</p>
                                    </div>

                                    {{-- Avaliação 2 --}}
                                    <div style="border-bottom:1px solid #f0f0f0; padding-bottom:20px; margin-bottom:20px;">
                                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                                            <div style="width:42px; height:42px; background:#1565C0; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:16px;">J</div>
                                            <div>
                                                <strong style="font-size:13px;">João P.</strong>
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
                                        <p style="font-size:14px; color:#555; margin:0;">Muito boa qualidade! Entrega rápida e embalagem excelente. Só tirei uma estrela porque o tamanho G ficou um pouco largo — sugiro pedir um tamanho a menos se preferir mais ajustado.</p>
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
                <h3 style="font-size:20px; font-weight:700; color:#232323; text-transform:uppercase; letter-spacing:1px; margin-bottom:6px;">
                    Você também pode gostar
                </h3>
                <div class="multi_line" style="margin-bottom:28px;"></div>
            </div>
        </div>
        <div class="row">

            @foreach ($relacionados as $rel)
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="related-item"
                         data-product-id="{{ $rel->id }}"
                         data-product-nome="{{ $rel->nome }}"
                         data-product-preco="{{ $rel->preco }}"
                         data-product-imagem="{{ asset($rel->imagem) }}">
                        <div class="item-img">
                            <img src="{{ asset($rel->imagem) }}" alt="{{ $rel->nome }}" />
                            <div class="tr-add-cart">
                                <ul>
                                    <li><a class="fa fa-shopping-cart tr_cart" href="#" data-add-to-cart></a></li>
                                    <li><a class="tr_text" href="#" data-add-to-cart>ADICIONAR AO CARRINHO</a></li>
                                    <li><a class="fa fa-heart-o tr_heart" href="#"></a></li>
                                    <li><a class="fa fa-search tr_search" href="{{ route('produto', $rel->slug) }}"></a></li>
                                </ul>
                            </div>
                        </div>
                        <h5><a href="{{ route('produto', $rel->slug) }}" style="color:#333; text-decoration:none;">{{ $rel->nome }}</a></h5>
                        <p class="preco">
                            R$ {{ number_format($rel->preco, 2, ',', '.') }}
                            @if ($rel->is_promocao)
                                <del>R$ {{ number_format($rel->preco_promocional, 2, ',', '.') }}</del>
                                <span style="background:#d90000; color:#fff; font-size:10px; padding:2px 6px; border-radius:3px; margin-left:4px;">
                                    -{{ round((($rel->preco_promocional - $rel->preco) / $rel->preco_promocional) * 100) }}%
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach

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

                <p style="font-size:13px; color:#666; margin-bottom:18px;">
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
                    <p style="margin:0; font-size:13px; color:#555;">
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
    const qty = parseInt(document.getElementById('quantidade').value, 10) || 1;

    if (tamanho === '—') {
        alert('Por favor, selecione um tamanho antes de adicionar ao carrinho.');
        return;
    }

    window.VerticalCart.add({
        id: '{{ $produto->id }}',
        nome: '{{ addslashes($produto->nome) }}',
        preco: {{ $produto->preco }},
        imagem: '{{ asset($produto->imagem) }}',
        cor: cor,
        tamanho: tamanho,
        qty: qty,
    });
}

</script>

@endsection
