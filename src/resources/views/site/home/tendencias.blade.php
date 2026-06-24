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

                        {{-- ============================================================
                             TAB 1 — LANÇAMENTOS
                             ============================================================ --}}
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab-lancamentos">
                                <div id="owl-example" class="owl-carousel">

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item1.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Básica Branca</h5></a>
                                            <p>R$49,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item2.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Estampada Street Art</h5></a>
                                            <p>R$69,90 <span><del>R$89,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item3.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Oversized Preta</h5></a>
                                            <p>R$79,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item4.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Dry-Fit Sport</h5></a>
                                            <p>R$59,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item1.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Gola V Azul</h5></a>
                                            <p>R$54,90 <span><del>R$74,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item2.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Long Line</h5></a>
                                            <p>R$74,90 <span><del>R$94,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item3.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Básica Cinza Mescla</h5></a>
                                            <p>R$49,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item12.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Estampada Geométrica</h5></a>
                                            <p>R$64,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item13.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Premium Slim Fit</h5></a>
                                            <p>R$99,90 <span><del>R$119,90</del></span></p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- ============================================================
                                 TAB 2 — MAIS VENDIDOS
                                 ============================================================ --}}
                            <div role="tabpanel" class="tab-pane" id="tab-mais-vendidos">
                                <div id="owl-example-two" class="owl-carousel">

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item1.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Gola V Azul</h5></a>
                                            <p>R$54,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item7.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Dry-Fit Sport</h5></a>
                                            <p>R$59,90 <span><del>R$79,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item14.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Estampada Street Art</h5></a>
                                            <p>R$69,90 <span><del>R$89,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item5.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Básica Branca</h5></a>
                                            <p>R$49,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item6.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Long Line</h5></a>
                                            <p>R$74,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item7.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Oversized Preta</h5></a>
                                            <p>R$79,90 <span><del>R$99,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item15.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Premium Slim Fit</h5></a>
                                            <p>R$99,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item8.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Estampada Geométrica</h5></a>
                                            <p>R$64,90 <span><del>R$84,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item9.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Básica Cinza Mescla</h5></a>
                                            <p>R$49,90</p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- ============================================================
                                 TAB 3 — EM PROMOÇÃO (todos com desconto)
                                 ============================================================ --}}
                            <div role="tabpanel" class="tab-pane" id="tab-promocao">
                                <div id="owl-example-three" class="owl-carousel">

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item10.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Básica Branca</h5></a>
                                            <p>R$39,90 <span><del>R$49,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item16.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Estampada Street Art</h5></a>
                                            <p>R$55,90 <span><del>R$69,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item4.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Dry-Fit Sport</h5></a>
                                            <p>R$47,90 <span><del>R$59,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item11.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Gola V Azul</h5></a>
                                            <p>R$43,90 <span><del>R$54,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item1.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Long Line</h5></a>
                                            <p>R$59,90 <span><del>R$74,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item12.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Oversized Preta</h5></a>
                                            <p>R$63,90 <span><del>R$79,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item3.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Estampada Geométrica</h5></a>
                                            <p>R$51,90 <span><del>R$64,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item2.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Premium Slim Fit</h5></a>
                                            <p>R$79,90 <span><del>R$99,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item3.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Básica Cinza Mescla</h5></a>
                                            <p>R$39,90 <span><del>R$49,90</del></span></p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- ============================================================
                                 TAB 4 — DESTAQUES
                                 ============================================================ --}}
                            <div role="tabpanel" class="tab-pane" id="tab-destaques">
                                <div id="owl-example-four" class="owl-carousel">

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item1.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Oversized Preta</h5></a>
                                            <p>R$79,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item2.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Long Line</h5></a>
                                            <p>R$74,90 <span><del>R$94,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item7.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Estampada Street Art</h5></a>
                                            <p>R$69,90 <span><del>R$89,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item4.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Gola V Azul</h5></a>
                                            <p>R$54,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item1.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Premium Slim Fit</h5></a>
                                            <p>R$99,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item2.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Básica Branca</h5></a>
                                            <p>R$49,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item3.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new">
                                            <p>NOVO</p>
                                            <span class="badge-promo">PROMOÇÃO</span>
                                        </div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Dry-Fit Sport</h5></a>
                                            <p>R$47,90 <span><del>R$59,90</del></span></p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item12.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Estampada Geométrica</h5></a>
                                            <p>R$64,90</p>
                                        </div>
                                    </div>

                                    <div class="item">
                                        <div class="item-img">
                                            <img src="vertical/images/t_item14.jpg" alt="" />
                                            <div class="tr-add-cart">
                                                <ul>
                                                    <li><a class="fa fa-shopping-cart tr_cart" href="#"></a></li>
                                                    <li><a class="tr_text" href="#">ADICIONAR AO CARRINHO</a></li>
                                                    <li><a class="fa fa-heart tr_heart" href="#"></a></li>
                                                    <li><a class="fa fa-search tr_search" href="{{ route('produto') }}"></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="item-new"><p>NOVO</p></div>
                                        <div class="item-sub">
                                            <a href="{{ route('produto') }}"><h5>Camiseta Básica Cinza Mescla</h5></a>
                                            <p>R$49,90</p>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>{{-- fim .tab-content --}}
                    </div>{{-- fim role="tabpanel" --}}
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Fim Seção Tendências -->
