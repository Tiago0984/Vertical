@extends('layout.site')

@section('content')

{{-- ══════════════════════════════════════════
     TOPO DO BLOG
══════════════════════════════════════════ --}}
<section class="blog_slider_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="blog_slider_box">
                    <h2>blog - post</h2>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     BREADCRUMB
══════════════════════════════════════════ --}}
<div class="breadcumb_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="bread_box">
                    <ul class="breadcumb">
                        <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                        <li><a href="{{ route('blog') }}">Blog <span>|</span></a></li>
                        <li class="active"><a href="#">Post do Blog</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     CONTEÚDO DO POST
══════════════════════════════════════════ --}}
<section class="blog_page_area">
    <div class="container">
        <div class="row">

            {{-- Coluna principal --}}
            <div class="col-md-8 col-sm-8 col-xs-12">
                <div class="single_blog_in_page">
                    <div class="blog_img_l"><img src="{{ asset('vertical/images/blog_page2.jpg') }}" alt="" /></div>
                    <div class="blog_date_in_page">
                        <h2>_ <span>18</span></h2>
                        <p>abril 2025</p>
                    </div>
                    <div class="blog_text_in_page">
                        <h3>Trendy cloth designs made from our team</h3>
                        <h4>Por <span>Admin</span>, comentários <span>23</span>, coleção verão</h4>
                    </div>
                    <div class="blog_detail_in_page">
                        <p>Camisetas com estampas exclusivas inspiradas na arte urbana e no streetwear contemporâneo. Malha premium de algodão de alta qualidade, com toque macio e conforto durante todo o dia. Ideal para compor looks casuais com personalidade, unindo praticidade e estilo em cada peça da coleção.</p>
                        <ul id="single_blog_nav">
                            <li><a href=""><i class="fa fa-caret-right"></i> Modelagens pensadas para todos os tipos de corpo e ocasião.</a></li>
                            <li><a href=""><i class="fa fa-caret-right"></i> Estampas exclusivas com tecnologia de sublimação de alta durabilidade.</a></li>
                            <li><a href=""><i class="fa fa-caret-right"></i> Produção 100% nacional, do tecido ao acabamento final.</a></li>
                        </ul>
                        <p>Nossa equipe de design trabalha em cada coleção pensando em conforto, durabilidade e identidade visual. O processo criativo passa por pesquisa de tendências, testes de caimento e seleção cuidadosa de tecidos, garantindo que cada camiseta represente o melhor equilíbrio entre estilo e qualidade.<br><br>

                        Acompanhamos de perto a produção para assegurar que cada lote mantenha o mesmo padrão de qualidade, desde a escolha do algodão até a aplicação das estampas. É esse cuidado que torna cada peça única e pronta para acompanhar o seu dia a dia.
                        </p>
                    </div>
                    <div class="share">
                        <span>12 compartilhamentos</span>
                    </div>
                    <div class="share_icons">
                        <ul id="share_icon">
                            <li><a class="fa fa-facebook" href=""></a></li>
                            <li><a class="fa fa-twitter" href=""></a></li>
                            <li><a class="fa fa-instagram" href=""></a></li>
                            <li><a class="fa fa-linkedin" href=""></a></li>
                        </ul>
                    </div>
                </div>

                {{-- Comentários --}}
                <div class="comments">
                    <h2>Comentários (3)</h2>
                    <div class="multi_line"></div>

                    <div class="single_comment">
                        <div class="comment_img">
                            <img src="{{ asset('vertical/images/comment1.png') }}" alt="" />
                        </div>
                        <div class="comment_text">
                            <div class="comment_name">
                             <h3>MICHELE SANTOS   |   <span>18 de abril de 2025 às 17:00</span></h3>
                            </div>
                            <div class="reply"><span>Responder</span></div>
                            <div class="comment-detail">
                            <p>Muito bom o trabalho de vocês! Amei a qualidade do tecido e o caimento ficou perfeito. Com certeza vou comprar mais peças da coleção.</p>
                            </div>
                        </div>
                    </div>

                    <div class="single_comment even">
                        <div class="comment_img">
                            <img src="{{ asset('vertical/images/comment1.png') }}" alt="" />
                        </div>
                        <div class="comment_text">
                            <div class="comment_name">
                             <h3>KARINA TALCA   |   <span>18 de abril de 2025 às 17:00</span></h3>
                            </div>
                            <div class="reply"><span>Responder</span></div>
                            <div class="comment-detail">
                            <p>Adorei o post! As estampas são realmente exclusivas e o algodão é super macio. Recomendo demais.</p>
                            </div>
                        </div>
                    </div>

                    <div class="single_comment">
                        <div class="comment_img">
                            <img src="{{ asset('vertical/images/comment1.png') }}" alt="" />
                        </div>
                        <div class="comment_text">
                            <div class="comment_name">
                             <h3>ROQUE LANCER   |   <span>18 de abril de 2025 às 17:00</span></h3>
                            </div>
                            <div class="reply"><span>Responder</span></div>
                            <div class="comment-detail">
                            <p>Excelente qualidade, entrega rápida e embalagem impecável. Virei cliente fiel da marca.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Deixe um comentário --}}
                <div class="leave_a_reply">
                    <h2>deixe um comentário</h2>
                    <div class="multi_line"></div>
                    <div class="left_input">
                        <p>Nome<span>*</span></p>
                        <input type="text"/><br>
                        <p>E-mail<span>*</span></p>
                        <input type="text"/><br>
                        <p>Telefone<span>*</span></p>
                        <input type="text"/>
                    </div>
                    <div class="right_input">
                        <p>Comentário</p>
                        <textarea name="" cols="30" rows="10"></textarea>
                        <input type="submit" value="publicar comentário" />
                    </div>
                </div>

            </div>

            {{-- Barra lateral --}}
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="blog_page_sidebar">
                    <div class="blog_search">
                        <input type="text" placeholder="buscar post aqui..." />
                        <i class="fa fa-search"></i>
                    </div>

                    <div class="blog_widget">
                        <h2>SOBRE O BLOG</h2>
                        <div class="multi_line"></div>
                        <p>Novidades, bastidores de produção e inspirações de moda direto da Vertical. Acompanhe as tendências e fique por dentro dos lançamentos da coleção.</p>
                    </div>

                    <div class="blog_categories">
                        <h2>CATEGORIAS</h2>
                        <div class="multi_line"></div>
                        <ul id="blog_categories">
                            <li><a href="">Looks Estilosos</a></li>
                            <li><a href="">Moda da Semana</a></li>
                            <li><a href="">Coleção Verão</a></li>
                            <li><a href="">Ofertas e Descontos</a></li>
                            <li><a href="">Destaque do Dia</a></li>
                            <li><a href="">Melhores Avaliações</a></li>
                        </ul>
                    </div>

                    <div class="blog_recent_post">
                        <h2>POSTS RECENTES</h2>
                        <div class="multi_line"></div>

                        <div class="single_recent_post">
                            <div class="log_li_img">
                                <img src="{{ asset('vertical/images/blog_li.png') }}" alt="" />
                            </div>
                            <div class="recent_post_text">
                                <a href="{{ route('single-blog') }}"><h3>Trendy cloth designs made<br>from our team</h3></a>
                                <p>Abril 2025</p>
                            </div>
                            <div class="recent_post_img">
                                <a href="{{ route('single-blog') }}"><img src="{{ asset('vertical/images/recent_post1.png') }}" alt="" /></a>
                            </div>
                        </div>

                        <div class="single_recent_post">
                            <div class="log_li_img">
                                <img src="{{ asset('vertical/images/blog_li.png') }}" alt="" />
                            </div>
                            <div class="recent_post_text">
                                <a href="{{ route('single-blog') }}"><h3>Trendy cloth designs made<br>from our team</h3></a>
                                <p>Abril 2025</p>
                            </div>
                            <div class="recent_post_img">
                                <a href="{{ route('single-blog') }}"><img src="{{ asset('vertical/images/recent_post2.png') }}" alt="" /></a>
                            </div>
                        </div>

                        <div class="single_recent_post">
                            <div class="log_li_img">
                                <img src="{{ asset('vertical/images/blog_li.png') }}" alt="" />
                            </div>
                            <div class="recent_post_text">
                                <a href="{{ route('single-blog') }}"><h3>Trendy cloth designs made<br>from our team</h3></a>
                                <p>Abril 2025</p>
                            </div>
                            <div class="recent_post_img">
                                <a href="{{ route('single-blog') }}"><img src="{{ asset('vertical/images/recent_post3.png') }}" alt="" /></a>
                            </div>
                        </div>
                    </div>

                    <div class="instrigram">
                        <h2>Instagram</h2>
                        <div class="multi_line"></div>
                        <ul id="instrigram">
                            <li><a href="{{ route('single-blog') }}"><img src="{{ asset('vertical/images/blog-side1.jpg') }}" alt="" /></a></li>
                            <li><a href="{{ route('single-blog') }}"><img src="{{ asset('vertical/images/blog-side2.jpg') }}" alt="" /></a></li>
                            <li><a href="{{ route('single-blog') }}"><img src="{{ asset('vertical/images/blog-side3.jpg') }}" alt="" /></a></li>
                            <li><a href="{{ route('single-blog') }}"><img src="{{ asset('vertical/images/blog-side4.jpg') }}" alt="" /></a></li>
                            <li><a href="{{ route('single-blog') }}"><img src="{{ asset('vertical/images/blog-side5.jpg') }}" alt="" /></a></li>
                            <li><a href="{{ route('single-blog') }}"><img src="{{ asset('vertical/images/blog-side6.jpg') }}" alt="" /></a></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
