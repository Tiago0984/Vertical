<!-- Seção Tendências -->
<section class="trending_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="trending_box">
                    <h2>Camisetas em Destaque</h2>
                    <div class="multi_line"></div>

                    @php
                        // idCarousel preserva os ids originais (#owl-example,
                        // -two, -three, -four) que main.js já inicializa por
                        // posição fixa. "visivel" some com a aba inteira (nav
                        // + pane) em vez de renderizar vazia -- só faz sentido
                        // pra Destaques, cujo rótulo genérico não explica uma
                        // lista ausente ou curta demais (config/home.php).
                        $abasTendencias = collect([
                            [
                                'idPane' => 'tab-lancamentos',
                                'idCarousel' => 'owl-example',
                                'label' => 'LANÇAMENTOS',
                                'produtos' => $lancamentos,
                                'mensagemVazia' => 'Nenhum lançamento no momento.',
                                'visivel' => true,
                            ],
                            [
                                'idPane' => 'tab-mais-vendidos',
                                'idCarousel' => 'owl-example-two',
                                'label' => 'MAIS VENDIDOS',
                                'produtos' => $maisVendidos,
                                'mensagemVazia' => 'Nenhum produto encontrado.',
                                'visivel' => true,
                            ],
                            [
                                'idPane' => 'tab-promocao',
                                'idCarousel' => 'owl-example-three',
                                'label' => 'EM PROMOÇÃO',
                                'produtos' => $emPromocao,
                                'mensagemVazia' => 'Nenhuma promoção no momento.',
                                'visivel' => true,
                            ],
                            [
                                'idPane' => 'tab-destaques',
                                'idCarousel' => 'owl-example-four',
                                'label' => 'DESTAQUES',
                                'produtos' => $destaques,
                                'mensagemVazia' => 'Nenhum produto encontrado.',
                                'visivel' => $exibirDestaques,
                            ],
                        ])->filter(fn ($aba) => $aba['visivel'])->values();
                    @endphp

                    <div role="tabpanel">

                        {{-- Nav tabs --}}
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach ($abasTendencias as $aba)
                                <li role="presentation" class="{{ $loop->first ? 'active' : '' }}">
                                    <a href="#{{ $aba['idPane'] }}" aria-controls="{{ $aba['idPane'] }}" role="tab" data-toggle="tab">{{ $aba['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach ($abasTendencias as $aba)
                                <div role="tabpanel" class="tab-pane {{ $loop->first ? 'active' : '' }}" id="{{ $aba['idPane'] }}">
                                    @if ($aba['produtos']->isEmpty())
                                        <p style="text-align:center; color:#999; padding:30px 0;">{{ $aba['mensagemVazia'] }}</p>
                                    @else
                                        <div id="{{ $aba['idCarousel'] }}" class="owl-carousel">
                                            @foreach ($aba['produtos'] as $produto)
                                                @include('partials.product-card-carousel', ['product' => $produto])
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>{{-- fim .tab-content --}}
                    </div>{{-- fim role="tabpanel" --}}
                </div>
            </div>
        </div>
    </div>
</section>
