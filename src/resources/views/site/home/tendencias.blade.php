<!-- Seção Tendências -->
<section class="trending_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="trending_box">
                    <h2>Camisetas em Destaque</h2>
                    <div class="multi_line"></div>

                    <div role="tabpanel">

                        {{-- Nav tabs --}}
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tab-lancamentos" aria-controls="tab-lancamentos" role="tab" data-toggle="tab">LANÇAMENTOS</a>
                            </li>
                            <li role="presentation">
                                <a href="#tab-mais-vendidos" aria-controls="tab-mais-vendidos" role="tab" data-toggle="tab">MAIS VENDIDOS</a>
                            </li>
                            <li role="presentation">
                                <a href="#tab-promocao" aria-controls="tab-promocao" role="tab" data-toggle="tab">EM PROMOÇÃO</a>
                            </li>
                            <li role="presentation">
                                <a href="#tab-destaques" aria-controls="tab-destaques" role="tab" data-toggle="tab">DESTAQUES</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            {{-- TAB 1 — LANÇAMENTOS --}}
                            <div role="tabpanel" class="tab-pane active" id="tab-lancamentos">
                                @if ($lancamentos->isEmpty())
                                    <p style="text-align:center; color:#999; padding:30px 0;">Nenhum lançamento no momento.</p>
                                @else
                                    <div id="owl-example" class="owl-carousel">
                                        @foreach ($lancamentos as $produto)
                                            @include('partials.product-card-carousel', ['product' => $produto])
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- TAB 2 — MAIS VENDIDOS --}}
                            <div role="tabpanel" class="tab-pane" id="tab-mais-vendidos">
                                @if ($maisVendidos->isEmpty())
                                    <p style="text-align:center; color:#999; padding:30px 0;">Nenhum produto encontrado.</p>
                                @else
                                    <div id="owl-example-two" class="owl-carousel">
                                        @foreach ($maisVendidos as $produto)
                                            @include('partials.product-card-carousel', ['product' => $produto])
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- TAB 3 — EM PROMOÇÃO --}}
                            <div role="tabpanel" class="tab-pane" id="tab-promocao">
                                @if ($emPromocao->isEmpty())
                                    <p style="text-align:center; color:#999; padding:30px 0;">Nenhuma promoção no momento.</p>
                                @else
                                    <div id="owl-example-three" class="owl-carousel">
                                        @foreach ($emPromocao as $produto)
                                            @include('partials.product-card-carousel', ['product' => $produto])
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- TAB 4 — DESTAQUES --}}
                            <div role="tabpanel" class="tab-pane" id="tab-destaques">
                                @if ($destaques->isEmpty())
                                    <p style="text-align:center; color:#999; padding:30px 0;">Nenhum produto encontrado.</p>
                                @else
                                    <div id="owl-example-four" class="owl-carousel">
                                        @foreach ($destaques as $produto)
                                            @include('partials.product-card-carousel', ['product' => $produto])
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        </div>{{-- fim .tab-content --}}
                    </div>{{-- fim role="tabpanel" --}}
                </div>
            </div>
        </div>
    </div>
</section>
