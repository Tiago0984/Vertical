<footer class="entire_footer_area">

    {{-- Newsletter + Pagamentos --}}
    <div class="footer_top_area">
        <div class="container">
            <div class="row">
                <div class="col-md-7 col-sm-6 col-xs-12">
                    <div class="footer_top_left">
                        <a href=""><i class="fa fa-envelope"></i>assine nossa newsletter</a>
                        <input type="text" placeholder="seu@email.com.br"/>
                        <input type="submit" value="assinar"/>
                    </div>
                </div>
                <div class="col-md-5 col-sm-6 col-xs-12">
                    <div class="footer_top_right">
                        {{-- Ícones de pagamento aceito --}}
                        <ul id="payment">
                            <li>
                                <span title="Visa" class="payment-badge payment-visa">VISA</span>
                            </li>
                            <li>
                                <span title="Mastercard" class="payment-badge payment-mastercard">
                                    <span class="mc-circle-red"></span>
                                    <span class="mc-circle-yellow"></span>
                                </span>
                            </li>
                            <li>
                                <span title="PIX" class="payment-badge payment-pix">PIX</span>
                            </li>
                            <li>
                                <span title="Boleto Bancário" class="payment-badge payment-boleto">
                                    <span class="boleto-bar boleto-bar-long"></span>
                                    <span class="boleto-bar boleto-bar-short"></span>
                                    <span class="boleto-bar boleto-bar-long"></span>
                                </span>
                            </li>
                            <li>
                                <span title="Elo" class="payment-badge payment-elo">ELO</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Colunas do rodapé --}}
    <div class="footer_area">
        <div class="container">
            <div class="row">

                {{-- Coluna 1: Informações --}}
                <div class="col-md-3 col-sm-3 col-xs-12">
                    <div class="single_widget">
                        <h5>Informações</h5>
                        <div class="wid_line"></div>
                        <ul class="widget_nav">
                            <li><a href="{{ route('loja') }}">Novidades</a></li>
                            <li><a href="{{ route('loja') }}">Mais Vendidos</a></li>
                            <li><a href="{{ route('categorias') }}">Promoções</a></li>
                            <li><a href="{{ route('loja') }}">Lançamentos</a></li>
                            <li><a href="{{ route('loja') }}">Coleções</a></li>
                            <li><a href="{{ route('blog') }}">Blog de Moda</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Coluna 2: Minha Conta --}}
                <div class="col-md-3 col-sm-3 col-xs-12">
                    <div class="single_widget">
                        <h5>Minha Conta</h5>
                        <div class="wid_line"></div>
                        <ul class="widget_nav">
                            <li><a href="#">Minha Conta</a></li>
                            <li><a href="#">Dados Pessoais</a></li>
                            <li><a href="#">Endereços</a></li>
                            <li><a href="#">Cupons e Descontos</a></li>
                            <li><a href="#">Histórico de Pedidos</a></li>
                            <li><a href="#">Lista de Desejos</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Coluna 3: Atendimento --}}
                <div class="col-md-3 col-sm-3 col-xs-12">
                    <div class="single_widget">
                        <h5>Atendimento ao Cliente</h5>
                        <div class="wid_line"></div>
                        <ul class="widget_nav">
                            <li><a href="#">Ajuda e Contato</a></li>
                            <li><a href="#">Trocas e Devoluções</a></li>
                            <li><a href="#">Prazo de Entrega</a></li>
                            <li><a href="#">Política de Privacidade</a></li>
                            <li><a href="#">Termos de Uso</a></li>
                            <li><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Coluna 4: Contato --}}
                <div class="col-md-3 col-sm-3 col-xs-12">
                    <div class="single_widget">
                        <h5>Informações de Contato</h5>
                        <div class="wid_line"></div>
                        <address class="footer-address">
                            <i class="fa fa-map-marker footer-address-icon"></i>
                            Rua das Camisetas, 42<br>
                            <span class="footer-address-indent">São Paulo, SP — 01310-100</span><br>
                            <i class="fa fa-phone footer-address-icon"></i>
                            (11) 99999-0000<br>
                            <i class="fa fa-envelope footer-address-icon"></i>
                            contato@camisetashop.com.br
                        </address>
                        <ul class="wid_social">
                            <li><a class="fa fa-facebook" href="#" title="Facebook"></a></li>
                            <li><a class="fa fa-instagram" href="#" title="Instagram"></a></li>
                            <li><a class="fa fa-twitter" href="#" title="Twitter/X"></a></li>
                            <li><a class="fa fa-pinterest" href="#" title="Pinterest"></a></li>
                            <li><a class="fa fa-youtube-play" href="#" title="YouTube"></a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Copyright --}}
    <div class="footer_bottom_area">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="footer_bottom">
                        <p>
                            Copyright &copy; {{ date('Y') }}
                            <a href="{{ route('home') }}" class="footer-brand-link">CamisetaShop</a>.
                            Todos os direitos reservados.
                        </p>
                        <p class="footer-made-in">
                            Feito com <i class="fa fa-heart footer-heart"></i> no Brasil
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>
