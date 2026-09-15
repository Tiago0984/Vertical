<div class="header_top">
		<div class="container">
			<div class="row">
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="header_top_left">
					<img src="{{ asset('vertical/images/car.png') }}" alt="Header Car Icon" />
					<p>FRETE GRÁTIS EM PEDIDOS ACIMA DE <span>R$150</span></p>
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="header_top_right floatright">
					<p>
						@guest
							<a href="{{ route('login') }}">entrar</a> / <a href="{{ route('register') }}">criar conta</a>
						@else
							olá, <a href="{{ route('conta.index') }}">{{ explode(' ', auth()->user()->name)[0] }}</a> /
							<a href="#" onclick="event.preventDefault(); document.getElementById('form-sair').submit();">sair</a>
							<form id="form-sair" action="{{ route('logout') }}" method="POST" style="display:none;">
								@csrf
							</form>
						@endguest
					</p>
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
							<li>
								<a href="{{ route('favoritos') }}">
									<i class="fa fa-heart-o"></i>favoritos
									<span class="w_likes js-favoritos-count">0</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<section class="nav_area">
		<div class="container">
			<div class="nav_left floatleft">
				<a href="#" onclick="return false;">categorias<i class="fa fa-bars"></i></a>
				<ul id="sub-menu3">
					<li><a href="{{ route('home') }}">Início</a></li>
					<li><a href="{{ route('categorias') }}">Camisetas</a></li>
					<li><a href="{{ route('loja') }}">Loja</a></li>
					<li><a href="{{ route('favoritos') }}">Favoritos</a></li>
				</ul>
			</div>
			<div class="nav_center">
				<nav class="mainmenu">
					<ul id="nav">
						<li class="current-page-item"><a href="{{ route('home') }}">Início</a></li>
						<li><a href="{{ route('categorias') }}">Camisetas</a></li>
						<li><a href="{{ route('loja') }}">Loja</a></li>
					</ul>
				</nav>
			</div>
		<div class="nav_right floatright">
			<a href="#" class="js-cart-open"><img src="{{ asset('vertical/images/bag.png') }}" alt="Bag" />carrinho: <span class="js-cart-count">0</span> itens</a>
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
							<li>
								<a href='{{ route("home") }}'><span>Início</span></a>
							</li>

							<li>
								<a href='{{ route("categorias") }}'><span>Camisetas</span></a>
							</li>
							<li>
								<a href='{{ route('loja') }}'><span>Loja</span></a>
							</li>
							<li>
								<a href='#' class="js-cart-open"><span>Carrinho</span></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- MOBILE ONLY CONTENT -->
		</div>
	</section>
