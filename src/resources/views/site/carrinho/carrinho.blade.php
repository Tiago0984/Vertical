@extends('layout.site')

@section('content')

{{-- Breadcrumb --}}
<div class="breadcumb_area">
    <div class="container">
        <div class="bread_box">
            <ul class="breadcumb">
                <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                <li class="active"><a href="#">Carrinho</a></li>
            </ul>
        </div>
    </div>
</div>

{{-- Conteúdo do Carrinho --}}
<section class="cart-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="cart-title">
                    <i class="fa fa-shopping-cart" style="color:#000; margin-right:10px;"></i>
                    Meu Carrinho
                    <span id="cart-count" style="font-size:14px; font-weight:400; color:#aaa; margin-left:8px;">(3 itens)</span>
                </h2>
                <div class="multi_line" style="margin-bottom:28px;"></div>
            </div>
        </div>

        <div class="row">
            {{-- Tabela de itens --}}
            <div class="col-md-8 col-sm-12 col-xs-12">

                <div class="table-responsive">
                    <table class="cart-table" id="cart-table">
                        <thead>
                            <tr>
                                <th style="width:40%;">Produto</th>
                                <th>Tamanho</th>
                                <th>Cor</th>
                                <th>Preço</th>
                                <th>Qtd.</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">

                            {{-- Item 1 --}}
                            <tr data-price="69.90" data-id="1">
                                <td data-label="Produto">
                                    <div class="cart-product-info">
                                        <img src="{{ asset('vertical/images/t_item2.jpg') }}" alt="" class="cart-product-img" />
                                        <div>
                                            <a href="{{ route('produto') }}" class="cart-product-name">Camiseta Estampada Street Art</a>
                                            <span class="cart-product-sku">SKU: CSA-001</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Tamanho"><span class="cart-badge cart-badge-size">M</span></td>
                                <td data-label="Cor">
                                    <span class="cart-badge cart-badge-color" style="display:flex; align-items:center; gap:5px; justify-content:center;">
                                        <span style="width:14px; height:14px; background:#1a1a1a; border-radius:50%; display:inline-block; border:1px solid #ccc;"></span>Preta
                                    </span>
                                </td>
                                <td data-label="Preço"><span class="cart-price">R$69,90</span></td>
                                <td data-label="Qtd.">
                                    <div class="qty-ctrl">
                                        <button type="button" onclick="alterarQtd(this,-1)">−</button>
                                        <input type="number" value="1" min="1" max="99" onchange="recalcular()" class="cart-qty" />
                                        <button type="button" onclick="alterarQtd(this,1)">+</button>
                                    </div>
                                </td>
                                <td data-label="Total"><span class="cart-total item-total">R$69,90</span></td>
                                <td data-label="">
                                    <button class="btn-remove" onclick="removerItem(this)" title="Remover">
                                        <i class="fa fa-times-circle"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- Item 2 --}}
                            <tr data-price="49.90" data-id="2">
                                <td data-label="Produto">
                                    <div class="cart-product-info">
                                        <img src="{{ asset('vertical/images/t_item1.jpg') }}" alt="" class="cart-product-img" />
                                        <div>
                                            <a href="{{ route('produto') }}" class="cart-product-name">Camiseta Básica Branca</a>
                                            <span class="cart-product-sku">SKU: CBB-003</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Tamanho"><span class="cart-badge cart-badge-size">G</span></td>
                                <td data-label="Cor">
                                    <span class="cart-badge cart-badge-color" style="display:flex; align-items:center; gap:5px; justify-content:center;">
                                        <span style="width:14px; height:14px; background:#FFFFFF; border-radius:50%; display:inline-block; border:1px solid #ccc;"></span>Branca
                                    </span>
                                </td>
                                <td data-label="Preço"><span class="cart-price">R$49,90</span></td>
                                <td data-label="Qtd.">
                                    <div class="qty-ctrl">
                                        <button type="button" onclick="alterarQtd(this,-1)">−</button>
                                        <input type="number" value="2" min="1" max="99" onchange="recalcular()" class="cart-qty" />
                                        <button type="button" onclick="alterarQtd(this,1)">+</button>
                                    </div>
                                </td>
                                <td data-label="Total"><span class="cart-total item-total">R$99,80</span></td>
                                <td data-label="">
                                    <button class="btn-remove" onclick="removerItem(this)" title="Remover">
                                        <i class="fa fa-times-circle"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- Item 3 --}}
                            <tr data-price="59.90" data-id="3">
                                <td data-label="Produto">
                                    <div class="cart-product-info">
                                        <img src="{{ asset('vertical/images/t_item4.jpg') }}" alt="" class="cart-product-img" />
                                        <div>
                                            <a href="{{ route('produto') }}" class="cart-product-name">Camiseta Dry-Fit Sport</a>
                                            <span class="cart-product-sku">SKU: CDS-007</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Tamanho"><span class="cart-badge cart-badge-size">P</span></td>
                                <td data-label="Cor">
                                    <span class="cart-badge cart-badge-color" style="display:flex; align-items:center; gap:5px; justify-content:center;">
                                        <span style="width:14px; height:14px; background:#1565C0; border-radius:50%; display:inline-block; border:1px solid #ccc;"></span>Azul
                                    </span>
                                </td>
                                <td data-label="Preço"><span class="cart-price">R$59,90</span></td>
                                <td data-label="Qtd.">
                                    <div class="qty-ctrl">
                                        <button type="button" onclick="alterarQtd(this,-1)">−</button>
                                        <input type="number" value="1" min="1" max="99" onchange="recalcular()" class="cart-qty" />
                                        <button type="button" onclick="alterarQtd(this,1)">+</button>
                                    </div>
                                </td>
                                <td data-label="Total"><span class="cart-total item-total">R$59,90</span></td>
                                <td data-label="">
                                    <button class="btn-remove" onclick="removerItem(this)" title="Remover">
                                        <i class="fa fa-times-circle"></i>
                                    </button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                {{-- Ações e Cupom --}}
                <div class="coupon-box" style="margin-top:28px;">
                    <i class="fa fa-tag" style="color:#000; font-size:18px;"></i>
                    <input type="text" id="cupom-input" placeholder="Código do cupom (ex: BEMVINDO10)" maxlength="20" />
                    <button class="btn-coupon" onclick="aplicarCupom()">
                        <i class="fa fa-check" style="margin-right:6px;"></i>APLICAR
                    </button>
                </div>
                <div class="coupon-applied" id="cupom-ok">
                    <i class="fa fa-check-circle"></i>
                    Cupom <strong id="cupom-nome"></strong> aplicado! Desconto de <strong id="cupom-valor"></strong>.
                </div>

                <div class="cart-actions">
                    <a href="{{ route('loja') }}" class="btn-action">
                        <i class="fa fa-arrow-left"></i> Continuar comprando
                    </a>
                    <button class="btn-action" onclick="limparCarrinho()">
                        <i class="fa fa-trash"></i> Limpar carrinho
                    </button>
                </div>

            </div>

            {{-- Resumo do pedido --}}
            <div class="col-md-4 col-sm-12 col-xs-12">
                <div class="order-summary">
                    <h4><i class="fa fa-file-text-o" style="margin-right:8px;"></i>Resumo do Pedido</h4>

                    <div class="summary-row">
                        <span class="label">Subtotal</span>
                        <span class="value" id="resumo-subtotal">R$229,60</span>
                    </div>

                    <div class="summary-row frete-gratis" id="resumo-frete-row">
                        <span class="label">
                            <i class="fa fa-truck" style="color:#4caf50; margin-right:4px;"></i>Frete
                        </span>
                        <span class="value" id="resumo-frete">GRÁTIS</span>
                    </div>

                    <div class="summary-row desconto" id="resumo-desc-row" style="display:none;">
                        <span class="label">
                            <i class="fa fa-tag" style="margin-right:4px;"></i>Desconto
                        </span>
                        <span class="value" id="resumo-desconto">— R$0,00</span>
                    </div>

                    <div class="summary-row total">
                        <span class="label">Total</span>
                        <span class="value" id="resumo-total">R$229,60</span>
                    </div>

                    <p class="summary-note">
                        <i class="fa fa-lock" style="margin-right:4px;"></i>Compra 100% segura
                    </p>

                    <a href="{{ route('checkout') }}" class="btn-checkout">
                        <i class="fa fa-lock" style="margin-right:8px;"></i>FINALIZAR COMPRA
                    </a>

                    <div style="margin-top:18px; text-align:center;">
                        <p style="font-size:11px; color:#aaa; margin-bottom:8px;">Pagamentos aceitos:</p>
                        <div style="display:flex; gap:6px; justify-content:center; flex-wrap:wrap;">
                            <span style="background:#1a1f71; color:#fff; font-weight:700; font-size:10px; padding:4px 8px; border-radius:3px;">VISA</span>
                            <span style="background:#eb001b; color:#fff; font-weight:700; font-size:10px; padding:4px 8px; border-radius:3px; letter-spacing:0.5px;">MC</span>
                            <span style="background:#00b0ea; color:#fff; font-weight:700; font-size:10px; padding:4px 8px; border-radius:3px;">PIX</span>
                            <span style="background:#555; color:#fff; font-weight:700; font-size:10px; padding:4px 8px; border-radius:3px;">BOLETO</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /row --}}
    </div>
</section>

<script>
/* ── Cupons válidos ── */
const CUPONS = {
    'BEMVINDO10': { pct: 10, label: '10%' },
    'VERAO20':    { pct: 20, label: '20%' },
    'FRETEGRATIS': { pct: 0, frete: true, label: 'frete grátis' },
};
let cupomAtivo = null;

function formatBRL(v) {
    return 'R$' + v.toFixed(2).replace('.', ',');
}

function recalcular() {
    let subtotal = 0;
    document.querySelectorAll('#cart-items tr').forEach(tr => {
        const price  = parseFloat(tr.dataset.price) || 0;
        const qty    = parseInt(tr.querySelector('.cart-qty')?.value) || 1;
        const total  = price * qty;
        subtotal    += total;
        const cell   = tr.querySelector('.item-total');
        if (cell) cell.textContent = formatBRL(total);
    });

    const freteLivre = subtotal >= 150;
    let desconto = 0;
    if (cupomAtivo) {
        desconto = subtotal * (cupomAtivo.pct / 100);
    }
    const frete = (freteLivre || cupomAtivo?.frete) ? 0 : 19.90;
    const total = subtotal - desconto + frete;

    document.getElementById('resumo-subtotal').textContent = formatBRL(subtotal);

    const freteRow = document.getElementById('resumo-frete-row');
    const freteEl  = document.getElementById('resumo-frete');
    if (frete === 0) {
        freteRow.className = 'summary-row frete-gratis';
        freteEl.textContent = 'GRÁTIS';
    } else {
        freteRow.className = 'summary-row';
        freteEl.textContent = formatBRL(frete);
    }

    const descRow = document.getElementById('resumo-desc-row');
    if (desconto > 0) {
        descRow.style.display = 'flex';
        document.getElementById('resumo-desconto').textContent = '— ' + formatBRL(desconto);
    } else {
        descRow.style.display = 'none';
    }

    document.getElementById('resumo-total').textContent = formatBRL(total);

    const count = document.querySelectorAll('#cart-items tr').length;
    document.getElementById('cart-count').textContent = '(' + count + ' ' + (count === 1 ? 'item' : 'itens') + ')';
}

function alterarQtd(btn, delta) {
    const input = btn.parentElement.querySelector('input');
    const val   = Math.max(1, parseInt(input.value) + delta);
    input.value = val;
    recalcular();
}

function removerItem(btn) {
    const tr = btn.closest('tr');
    tr.style.transition = 'opacity 0.3s';
    tr.style.opacity    = '0';
    setTimeout(() => { tr.remove(); recalcular(); }, 300);
}

function limparCarrinho() {
    if (!confirm('Deseja remover todos os itens do carrinho?')) return;
    document.querySelectorAll('#cart-items tr').forEach(tr => tr.remove());
    recalcular();
}

function aplicarCupom() {
    const codigo = document.getElementById('cupom-input').value.trim().toUpperCase();
    if (!codigo) { alert('Digite um código de cupom.'); return; }
    const cupom = CUPONS[codigo];
    if (!cupom) { alert('Cupom inválido ou expirado.'); return; }
    cupomAtivo = cupom;
    document.getElementById('cupom-ok').style.display = 'block';
    document.getElementById('cupom-nome').textContent  = codigo;
    document.getElementById('cupom-valor').textContent = cupom.label;
    recalcular();
}

document.addEventListener('DOMContentLoaded', recalcular);
</script>

@endsection
