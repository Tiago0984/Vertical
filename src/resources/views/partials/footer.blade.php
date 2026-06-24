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
                        <ul id="payment" style="display:flex; align-items:center; gap:8px; list-style:none; margin:0; padding:0; flex-wrap:wrap; justify-content:flex-end;">
                            <li>
                                <span title="Visa" style="display:inline-flex; align-items:center; justify-content:center; width:52px; height:32px; background:#1a1f71; color:#fff; font-family:'Montserrat',sans-serif; font-size:13px; font-weight:700; letter-spacing:0.5px; border-radius:4px; font-style:italic;">
                                    VISA
                                </span>
                            </li>
                            <li>
                                <span title="Mastercard" style="display:inline-flex; align-items:center; justify-content:center; width:52px; height:32px; background:#fff; border:1px solid #ddd; border-radius:4px; gap:0; overflow:hidden;">
                                    <span style="width:22px; height:22px; background:#eb001b; border-radius:50%; margin-right:-8px; opacity:0.9;"></span>
                                    <span style="width:22px; height:22px; background:#f79e1b; border-radius:50%; opacity:0.9;"></span>
                                </span>
                            </li>
                            <li>
                                <span title="PIX" style="display:inline-flex; align-items:center; justify-content:center; width:52px; height:32px; background:#32bcad; color:#fff; font-family:'Montserrat',sans-serif; font-size:13px; font-weight:700; border-radius:4px; letter-spacing:0.5px;">
                                    PIX
                                </span>
                            </li>
                            <li>
                                <span title="Boleto Bancário" style="display:inline-flex; align-items:center; justify-content:center; width:52px; height:32px; background:#fff; border:1px solid #ddd; border-radius:4px; flex-direction:column; gap:2px; padding:4px;">
                                    <span style="display:block; width:36px; height:3px; background:#333; border-radius:1px;"></span>
                                    <span style="display:block; width:28px; height:3px; background:#333; border-radius:1px;"></span>
                                    <span style="display:block; width:36px; height:3px; background:#333; border-radius:1px;"></span>
                                </span>
                            </li>
                            <li>
                                <span title="Elo" style="display:inline-flex; align-items:center; justify-content:center; width:52px; height:32px; background:#FFD600; color:#333; font-family:'Montserrat',sans-serif; font-size:13px; font-weight:700; border-radius:4px; letter-spacing:0.5px;">
                                    ELO
                                </span>
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
                        <address style="line-height:1.9; font-size:13px;">
                            <i class="fa fa-map-marker" style="color:#e91e8c; margin-right:6px; width:14px;"></i>
                            Rua das Camisetas, 42<br>
                            <span style="margin-left:20px;">São Paulo, SP — 01310-100</span><br>
                            <i class="fa fa-phone" style="color:#e91e8c; margin-right:6px; width:14px;"></i>
                            (11) 99999-0000<br>
                            <i class="fa fa-envelope" style="color:#e91e8c; margin-right:6px; width:14px;"></i>
                            contato@camisetashop.com.br
                        </address>
                        <ul class="wid_social" style="margin-top:14px;">
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
                    <div class="footer_bottom" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                        <p style="margin:0;">
                            Copyright &copy; {{ date('Y') }}
                            <a href="{{ route('home') }}" style="color:#e91e8c; font-weight:700;">CamisetaShop</a>.
                            Todos os direitos reservados.
                        </p>
                        <p style="margin:0; font-size:12px; color:#aaa;">
                            Feito com <i class="fa fa-heart" style="color:#e91e8c;"></i> no Brasil
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</footer>
