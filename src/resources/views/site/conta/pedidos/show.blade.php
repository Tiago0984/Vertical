@extends('layout.site')

@section('content')

@php
    $formaPagamentoLabel = match ($pedido->forma_pagamento) {
        \App\Models\Order::PAGAMENTO_CARTAO => 'Cartão de Crédito',
        \App\Models\Order::PAGAMENTO_PIX => 'PIX',
        \App\Models\Order::PAGAMENTO_BOLETO => 'Boleto Bancário',
        default => $pedido->forma_pagamento,
    };
    $enderecoEntrega = $pedido->endereco_entrega;
@endphp

<div class="breadcumb_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="bread_box">
                    <ul class="breadcumb">
                        <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                        <li><a href="{{ route('conta.index') }}">Minha Conta <span>|</span></a></li>
                        <li><a href="{{ route('conta.pedidos.index') }}">Pedidos <span>|</span></a></li>
                        <li class="active"><a href="#">#{{ $pedido->numero_pedido }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="checkout-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-12">
                @include('partials.conta-sidebar')
            </div>

            <div class="col-md-9 col-sm-8 col-xs-12">

                <div class="checkout-card">
                    <div class="checkout-card-title" style="display:flex; align-items:center; justify-content:space-between;">
                        <span><i class="fa fa-list-alt" style="color:#000;"></i> Pedido #{{ $pedido->numero_pedido }}</span>
                        <span class="conta-status-badge conta-status-{{ $pedido->status }}">
                            {{ \App\Support\OrderStatus::label($pedido->status) }}
                        </span>
                    </div>
                    <p style="color:#999; font-size:13px;">Realizado em {{ $pedido->created_at->format('d/m/Y \à\s H:i') }}</p>

                    <div class="conta-pedido-itens">
                        @foreach ($pedido->items as $item)
                            <div class="conta-pedido-item">
                                <img src="{{ $item->product ? asset($item->product->imagem) : asset('vertical/images/t_item1.jpg') }}" alt="" />
                                <div class="conta-pedido-item-info">
                                    <div class="conta-pedido-item-nome">{{ $item->nome }}</div>
                                    <div class="conta-pedido-item-meta">
                                        @if ($item->tamanho) Tam: {{ $item->tamanho }} @endif
                                        @if ($item->cor) · Cor: {{ $item->cor }} @endif
                                        · Qtd: {{ $item->quantidade }}
                                    </div>
                                </div>
                                <div class="conta-pedido-item-preco">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="conta-pedido-totais">
                        <div><span>Subtotal</span><span>R$ {{ number_format($pedido->subtotal, 2, ',', '.') }}</span></div>
                        <div><span>Frete</span><span>{{ $pedido->frete > 0 ? 'R$ '.number_format($pedido->frete, 2, ',', '.') : 'GRÁTIS' }}</span></div>
                        <div class="conta-pedido-total-final"><span>Total</span><span>R$ {{ number_format($pedido->total, 2, ',', '.') }}</span></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="checkout-card">
                            <div class="checkout-card-title">
                                <i class="fa fa-map-marker" style="color:#000;"></i> Endereço de Entrega
                            </div>
                            <p style="font-size:13px; color:#555; line-height:1.8;">
                                {{ $pedido->nome }} {{ $pedido->sobrenome }}<br>
                                {{ $enderecoEntrega['rua'] ?? '' }}, {{ $enderecoEntrega['numero'] ?? '' }}
                                @if (! empty($enderecoEntrega['complemento'])) — {{ $enderecoEntrega['complemento'] }} @endif
                                <br>
                                {{ $enderecoEntrega['bairro'] ?? '' }} — {{ $enderecoEntrega['cidade'] ?? '' }}/{{ $enderecoEntrega['uf'] ?? '' }}<br>
                                CEP {{ $enderecoEntrega['cep'] ?? '' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkout-card">
                            <div class="checkout-card-title">
                                <i class="fa fa-credit-card" style="color:#000;"></i> Pagamento
                            </div>
                            <p style="font-size:13px; color:#555; line-height:1.8;">
                                Forma de pagamento: <strong>{{ $formaPagamentoLabel }}</strong><br>
                                E-mail: {{ $pedido->email }}<br>
                                Telefone: {{ $pedido->telefone }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
