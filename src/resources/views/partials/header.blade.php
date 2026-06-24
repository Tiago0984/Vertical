<div class="header_top">
		<div class="container">
			<div class="row">
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="header_top_left">
					<img src="vertical/images/car.png" alt="Header Car Icon" />
					<p>FRETE GRÁTIS EM PEDIDOS ACIMA DE <span>R$150</span></p>
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="header_top_right floatright">
					<p><a href="">entrar</a> / <a href="">criar conta</a></p>
					<nav class="currency alignleft">
						<ul>
							<li>
								<img src="https://flagcdn.com/w20/br.png"
								     srcset="https://flagcdn.com/w40/br.png 2x"
								     width="20" height="14"
								     alt="Brasil"
								     class="flag-icon" />
							</li>
						</ul>
					</nav>
				</div>
			</div>
			</div>
		</div>
	</div>
	<div class="header">
		<div class="container">
			<div class="row">
				<div class="col-md-3 col-sm-3 col-xs-12">
					<div class="header_left floatleft">
						<a class="fa fa-search" href=""></a>
						<input type="text" placeholder="pesquisar"/>
					</div>
				</div>
				<div class="col-md-6 col-sm-5 col-xs-12">
					<div class="header_center">
						<a href="{{ route('home') }}" class="logo-link">
							<span class="logo-brand">
								Vertical
							</span>
						</a>
					</div>
				</div>
				<div class="col-md-3 col-sm-4 col-xs-12">
					<div class="header_right floatright">
						<ul class="checkout">
							<li><a href="checkout.html"><i class="fa fa-heart-o"></i>favoritos</a></li>
							<li class="mobi_right_li"><a href="checkout.html"><i class="fa fa-shopping-cart"></i>lista</a></li>
						</ul>
						<div class="w_likes">
							<span>3</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<section class="nav_area">
		<div class="container">
			<div class="nav_left floatleft">
				<a href="{{ route('categorias') }}">categorias<i class="fa fa-bars"></i></a>
				<div class="cat_mega_menu">
						<div class="cat_left">
							<h5>Camisetas Básicas</h5>
							<div class="cat_menu_line"></div>
							<ul class="cat_nav">
								<li><a href="">gola redonda</a></li>
								<li><a href="">gola V</a></li>
								<li><a href="">manga longa</a></li>
							</ul>
						</div>
						<div class="cat_middle">
							<h5>Camisetas Estampadas</h5>
							<div class="cat_menu_line"></div>
							<ul class="cat_nav">
								<li><a href="">frases</a></li>
								<li><a href="">geométricas</a></li>
								<li><a href="">personagens</a></li>
							</ul>
						</div>
						<div class="cat_middle_right">
							<h5>Camisetas Esportivas</h5>
							<div class="cat_menu_line"></div>
							<ul class="cat_nav">
								<li><a href="">dry-fit</a></li>
								<li><a href="">treino</a></li>
								<li><a href="">corrida</a></li>
							</ul>
						</div>
						<div class="cat_middle_right">
							<h5>Camisetas Premium</h5>
							<div class="cat_menu_line"></div>
							<ul class="cat_nav">
								<li><a href="">algodão pima</a></li>
								<li><a href="">oversized</a></li>
								<li><a href="">slim fit</a></li>
							</ul>
						</div>
					<div class="cat_img">
						<img src="vertical/images/menu_cat.png" alt="" />
					</div>
				</div>
			</div>
			<div class="nav_center">
				<nav class="mainmenu">
					<ul id="nav">
						<li class="current-page-item"><a href="{{ route('home') }}">Início</a></li>
						<li><a href="{{ route('categorias') }}">Camisetas</a>
							<ul id="sub-menu4">
								<li><a href="">Camisetas Básicas</a></li>
								<li><a href="">Camisetas Estampadas</a></li>
								<li><a href="">Camisetas Esportivas</a></li>
								<li><a href="">Camisetas Premium</a></li>
							</ul>
						</li>
						<li><a href="{{ route('loja') }}">Loja</a></li>
						<li><a href="{{ route('blog') }}">Blog</a></li>
						<li><a href="{{ route('carrinho') }}">Carrinho</a></li>
					</ul>
				</nav>
			</div>
		<div class="nav_right floatright">
			<a href="{{ route('carrinho') }}"><img src="vertical/images/bag.png" alt="Bag" />carrinho: 3 itens</a>
				<div class="cart_menu">

					{{-- Item 1 --}}
					<div class="cart_items">
						<div class="c_item_img floatleft">
							<a href="{{ route('produto') }}"><img src="vertical/images/t_item2.jpg" alt="" /></a>
						</div>
						<div class="c_item_totals floatleft">
							<div class="c_item_totals_detail floatleft">
								<a href="{{ route('produto') }}"><h5>Camiseta Estampada Street Art</h5></a>
								<span>1 x R$ 69,90</span>
							</div>
							<div class="close_icon_cart floatleft">
								<img src="vertical/images/close.png" alt="" />
							</div>
						</div>
					</div>

					{{-- Item 2 --}}
					<div class="cart_items">
						<div class="c_item_img floatleft">
							<a href="{{ route('produto') }}"><img src="vertical/images/t_item1.jpg" alt="" /></a>
						</div>
						<div class="c_item_totals floatleft">
							<div class="c_item_totals_detail floatleft">
								<a href="{{ route('produto') }}"><h5>Camiseta Básica Branca</h5></a>
								<span>2 x R$ 49,90</span>
							</div>
							<div class="close_icon_cart floatleft">
								<img src="vertical/images/close.png" alt="" />
							</div>
						</div>
					</div>

					{{-- Item 3 --}}
					<div class="cart_items">
						<div class="c_item_img floatleft">
							<a href="{{ route('produto') }}"><img src="vertical/images/t_item4.jpg" alt="" /></a>
						</div>
						<div class="c_item_totals floatleft">
							<div class="c_item_totals_detail floatleft">
								<a href="{{ route('produto') }}"><h5>Camiseta Dry-Fit Sport</h5></a>
								<span>1 x R$ 59,90</span>
							</div>
							<div class="close_icon_cart floatleft">
								<img src="vertical/images/close.png" alt="" />
							</div>
						</div>
					</div>

					<div class="cart_totals">
						<div class="c_totals_left floatleft">
							<p>Frete grátis</p>
						</div>
						<div class="c_totals_right floatleft">
							<p>total &nbsp; R$ 229,60</p>
						</div>
					</div>
					<div class="cart_view_bottom">
						<div class="c_totals_left floatleft">
							<a href="{{ route('carrinho') }}">Ver Carrinho</a>
						</div>
						<div class="c_totals_right floatleft">
							<a href="{{ route('checkout') }}">Finalizar Compra</a>
						</div>
					</div>
				</div>
			</div>



			<!-- MOBILE ONLY CONTENT -->
			<div class="only-for-mobile">
				<ul class="ofm">
					<li class="m_nav"><i class="fa fa-bars"></i> Navegação</li>
				</ul>

				<!-- MOBILE MENU -->
				<div class="mobi-menu">
					<div id='cssmenu'>
						<ul>
							<li class='has-sub'>
								<a href='{{ route("home") }}'><span>Início</span></a>
							</li>

							<li class='has-sub'>
								<a href='{{ route("categorias") }}'><span>Camisetas</span></a>
								<ul>
									<li class='has-sub'>
										<a href='#'><span>Camisetas Básicas</span></a>
										<ul>
											<li><a href="#"><span>gola redonda</span></a></li>
											<li><a href="#"><span>gola V</span></a></li>
											<li class="last"><a href="#"><span>manga longa</span></a></li>
										</ul>
									</li>
									<li class='has-sub'>
										<a href='#'><span>Camisetas Estampadas</span></a>
										<ul>
											<li><a href="#"><span>frases</span></a></li>
											<li><a href="#"><span>geométricas</span></a></li>
											<li class='last'><a href="#"><span>personagens</span></a></li>
										</ul>
									</li>
									<li class='has-sub'>
										<a href='#'><span>Camisetas Esportivas</span></a>
										<ul>
											<li><a href="#"><span>dry-fit</span></a></li>
											<li><a href="#"><span>treino</span></a></li>
											<li class='last'><a href="#"><span>corrida</span></a></li>
										</ul>
									</li>
									<li class='has-sub'>
										<a href='#'><span>Camisetas Premium</span></a>
										<ul>
											<li><a href="#"><span>algodão pima</span></a></li>
											<li><a href="#"><span>oversized</span></a></li>
											<li class='last'><a href="#"><span>slim fit</span></a></li>
										</ul>
									</li>
								</ul>
							</li>
							<li>
								<a href='{{ route('loja') }}'><span>Loja</span></a>
							</li>
							<li>
								<a href='{{ route('blog') }}'><span>Blog</span></a>
							</li>
							<li>
								<a href='{{ route('carrinho') }}'><span>Carrinho</span></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- MOBILE ONLY CONTENT -->
		</div>
	</section>
