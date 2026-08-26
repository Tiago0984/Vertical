@extends('layout.site')

@section('content')

<div class="breadcumb_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="bread_box">
                    <ul class="breadcumb">
                        <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                        <li class="active"><a href="#">Minha Conta</a></li>
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
                    <div class="checkout-card-title">
                        <i class="fa fa-th-large" style="color:#000;"></i> Olá, {{ explode(' ', auth()->user()->name)[0] }}
                    </div>
                    <p style="color:#777; font-size:14px;">Aqui você acompanha seus pedidos, endereços e dados da conta.</p>

                    <div class="row" style="margin-top:20px;">
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <a href="{{ route('conta.pedidos.index') }}" class="conta-resumo-card">
                                <i class="fa fa-list-alt"></i>
                                <strong>{{ $totalPedidos }}</strong>
                                <span>{{ $totalPedidos === 1 ? 'pedido' : 'pedidos' }}</span>
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <a href="{{ route('conta.enderecos.index') }}" class="conta-resumo-card">
                                <i class="fa fa-map-marker"></i>
                                <strong>{{ $totalEnderecos }}</strong>
                                <span>{{ $totalEnderecos === 1 ? 'endereço' : 'endereços' }}</span>
                            </a>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <a href="{{ route('favoritos') }}" class="conta-resumo-card">
                                <i class="fa fa-heart-o"></i>
                                <strong>{{ $totalFavoritos }}</strong>
                                <span>{{ $totalFavoritos === 1 ? 'favorito' : 'favoritos' }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="checkout-card">
                    <div class="checkout-card-title">
                        <i class="fa fa-clock-o" style="color:#000;"></i> Último Pedido
                    </div>

                    @if ($ultimoPedido)
                        <div class="conta-pedido-linha">
                            <div>
                                <strong>#{{ $ultimoPedido->numero_pedido }}</strong>
                                <span class="conta-pedido-data">{{ $ultimoPedido->created_at->format('d/m/Y') }}</span>
                            </div>
                            <span class="conta-status-badge conta-status-{{ $ultimoPedido->status }}">
                                {{ \App\Support\OrderStatus::label($ultimoPedido->status) }}
                            </span>
                            <span class="conta-pedido-total">R$ {{ number_format($ultimoPedido->total, 2, ',', '.') }}</span>
                            <a href="{{ route('conta.pedidos.show', $ultimoPedido) }}" class="conta-pedido-ver">Ver detalhes</a>
                        </div>
                    @else
                        <p style="color:#999; font-size:13px;">Você ainda não fez nenhum pedido.</p>
                        <a href="{{ route('loja') }}" class="btn-next" style="text-decoration:none; display:inline-block; margin-top:10px;">Ir para a loja</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
