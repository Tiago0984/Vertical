<div id="cart-drawer-overlay" class="cart-drawer-overlay"></div>

<aside id="cart-drawer" class="cart-drawer" aria-hidden="true">
    <div class="cart-drawer-header">
        <h4><i class="fa fa-shopping-bag"></i> Carrinho</h4>
        <button type="button" class="cart-drawer-close" id="cart-drawer-close" aria-label="Fechar carrinho">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <div class="cart-drawer-shipping" id="cart-drawer-shipping">
        <p class="cart-drawer-shipping-msg" id="cart-drawer-shipping-msg"></p>
        <div class="cart-drawer-shipping-bar">
            <div class="cart-drawer-shipping-bar-fill" id="cart-drawer-shipping-fill"></div>
        </div>
    </div>

    <div class="cart-drawer-items" id="cart-drawer-items">
        {{-- Preenchido via JS (cart.js) --}}
    </div>

    <div class="cart-drawer-empty" id="cart-drawer-empty">
        <p class="cart-drawer-empty-title">Seu carrinho está vazio</p>
        <p class="cart-drawer-empty-msg">Explore nossas coleções e ganhe 10% OFF na sua primeira compra utilizando o cupom <strong>BEMVINDO10</strong></p>
        <button type="button" class="cart-drawer-empty-continue js-cart-open-continue">Continuar comprando</button>
    </div>

    <div class="cart-drawer-footer" id="cart-drawer-footer">
        <div class="cart-drawer-total-row">
            <span>Subtotal</span>
            <strong id="cart-drawer-subtotal">R$ 0,00</strong>
        </div>
        <a href="{{ route('checkout') }}" class="cart-drawer-checkout-btn">FINALIZAR COMPRA</a>
        <button type="button" class="cart-drawer-continue-btn" id="cart-drawer-continue">Continuar comprando</button>
    </div>
</aside>
