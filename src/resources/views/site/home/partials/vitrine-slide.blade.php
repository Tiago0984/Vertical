{{-- Recebe $produto (App\Models\Product). Markup/classes fixos -- o bxSlider
     (partials/script.blade.php) depende de .slide e dos elementos internos. --}}
<div class="slide">
    <div class="t_to_b_content">
        <div class="t_to_b_img">
            <img src="{{ asset($produto->imagem) }}" alt="{{ $produto->nome }}" />
        </div>
        <div class="t_to_b_text">
            <a href="{{ route('produto', $produto->slug) }}"><p>{{ $produto->nome }}</p></a>
            <div class="t_to_b_dollr"><span>R$ {{ number_format($produto->preco, 2, ',', '.') }}</span></div>
            @if ($produto->is_promocao && $produto->preco_promocional)
                <div class="t_to_b_del"><del>R$ {{ number_format($produto->preco_promocional, 2, ',', '.') }}</del></div>
            @endif
        </div>
    </div>
</div>
