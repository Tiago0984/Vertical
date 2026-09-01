{{--
    Variante do card de produto para dentro de carrosséis Owl Carousel
    (sem o wrapper .main_cat_item, que é só pro grid em .row > .col).
    Recebe $product (App\Models\Product).
--}}
<div class="item"
     data-product-id="{{ $product->id }}"
     data-product-nome="{{ $product->nome }}"
     data-product-preco="{{ $product->preco }}"
     data-product-imagem="{{ asset($product->imagem) }}">
    <div class="item-img">
        <img src="{{ asset($product->imagem) }}" alt="{{ $product->nome }}" />
        <div class="tr-add-cart">
            <ul>
                <li><a class="fa fa-shopping-cart tr_cart" href="#" data-add-to-cart></a></li>
                <li><a class="tr_text" href="#" data-add-to-cart>ADICIONAR AO CARRINHO</a></li>
                <li><a class="fa fa-heart-o tr_heart" href="#"></a></li>
                <li><a class="fa fa-search tr_search" href="{{ route('produto', $product->slug) }}"></a></li>
            </ul>
        </div>
    </div>
    @if ($product->is_novo || $product->is_promocao)
        <div class="item-new">
            <p>{{ $product->is_novo ? 'NOVO' : 'OFERTA' }}</p>
            @if ($product->is_promocao && $product->preco_promocional)
                <span>-{{ round((($product->preco_promocional - $product->preco) / $product->preco_promocional) * 100) }}%</span>
            @endif
        </div>
    @endif
    <div class="item-sub">
        <a href="{{ route('produto', $product->slug) }}"><h5>{{ $product->nome }}</h5></a>
        <p>
            R$ {{ number_format($product->preco, 2, ',', '.') }}
            @if ($product->is_promocao && $product->preco_promocional)
                <span><del>R$ {{ number_format($product->preco_promocional, 2, ',', '.') }}</del></span>
            @endif
        </p>
    </div>
</div>
