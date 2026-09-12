@php
    // Vitrine pública: coluna sem produto não é renderizada (ao contrário do
    // dashboard admin, aqui um "buraco" visual não ajuda ninguém). As
    // colunas restantes redistribuem a largura do grid Bootstrap 3.
    $colunasVitrine = collect([
        ['titulo' => 'Itens em Destaque', 'produtos' => $vitrineDestaque, 'classeSlider' => 'slider8'],
        ['titulo' => 'Ofertas Especiais', 'produtos' => $vitrineOfertas, 'classeSlider' => 'slider9'],
        ['titulo' => 'Mais Vendidos', 'produtos' => $vitrineMaisVendidos, 'classeSlider' => 'slider10'],
    ])->filter(fn ($coluna) => $coluna['produtos']->isNotEmpty())->values();

    $larguraColuna = match ($colunasVitrine->count()) {
        3 => 4,
        2 => 6,
        1 => 12,
        default => 4,
    };
@endphp

@if ($colunasVitrine->isNotEmpty())
<!-- Produtos -->
<section class="t_to_b_slider_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="t_to_b_slider">
                    <div class="row">

                        @foreach ($colunasVitrine as $coluna)
                            <div class="col-md-{{ $larguraColuna }} col-sm-{{ $larguraColuna }} col-xs-12">
                                <div class="single_t_to_b_slider">
                                    <h3>{{ $coluna['titulo'] }}</h3>
                                    <div class="multi_line"></div>
                                    <div class="single_t_to_b">
                                        <div class="{{ $coluna['classeSlider'] }}">
                                            @foreach ($coluna['produtos'] as $produto)
                                                @include('site.home.partials.vitrine-slide', ['produto' => $produto])
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Fim Produtos -->
@endif
