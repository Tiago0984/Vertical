@extends('layout.site')

@section('content')

<div class="breadcumb_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="bread_box">
                    <ul class="breadcumb">
                        <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                        <li><a href="{{ route('conta.index') }}">Minha Conta <span>|</span></a></li>
                        <li class="active"><a href="#">Histórico de Pedidos</a></li>
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
                        <i class="fa fa-list-alt" style="color:#000;"></i> Histórico de Pedidos
                    </div>

                    @if ($pedidos->isEmpty())
                        <p style="color:#999; font-size:13px; margin-top:10px;">Você ainda não fez nenhum pedido.</p>
                        <a href="{{ route('loja') }}" class="btn-next" style="text-decoration:none; display:inline-block; margin-top:10px;">Ir para a loja</a>
                    @else
                        <div class="conta-tabela-pedidos">
                            @foreach ($pedidos as $pedido)
                                <div class="conta-pedido-linha">
                                    <div>
                                        <strong>#{{ $pedido->numero_pedido }}</strong>
                                        <span class="conta-pedido-data">{{ $pedido->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <span class="conta-status-badge conta-status-{{ $pedido->status }}">
                                        {{ \App\Support\OrderStatus::label($pedido->status) }}
                                    </span>
                                    <span class="conta-pedido-total">R$ {{ number_format($pedido->total, 2, ',', '.') }}</span>
                                    <a href="{{ route('conta.pedidos.show', $pedido) }}" class="conta-pedido-ver">Ver detalhes</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
