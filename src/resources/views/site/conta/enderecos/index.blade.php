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
                        <li class="active"><a href="#">Endereços</a></li>
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

                @if (session('status'))
                    <div class="conta-alert-sucesso">{{ session('status') }}</div>
                @endif

                <div class="checkout-card">
                    <div class="checkout-card-title" style="display:flex; align-items:center; justify-content:space-between;">
                        <span><i class="fa fa-map-marker" style="color:#000;"></i> Meus Endereços</span>
                        <a href="{{ route('conta.enderecos.create') }}" class="btn-next" style="text-decoration:none; padding:8px 18px; font-size:12px;">
                            <i class="fa fa-plus" style="margin-right:6px;"></i>Novo Endereço
                        </a>
                    </div>

                    @if ($enderecos->isEmpty())
                        <p style="color:#999; font-size:13px; margin-top:10px;">Você ainda não cadastrou nenhum endereço.</p>
                    @else
                        <div class="row" style="margin-top:10px;">
                            @foreach ($enderecos as $endereco)
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="conta-endereco-card">
                                        @if ($endereco->is_padrao)
                                            <span class="conta-endereco-padrao">Padrão</span>
                                        @endif
                                        <strong>{{ $endereco->nome }} {{ $endereco->sobrenome }}</strong>
                                        <p>
                                            {{ $endereco->endereco }}, {{ $endereco->numero }}
                                            @if ($endereco->complemento) — {{ $endereco->complemento }} @endif
                                            <br>
                                            {{ $endereco->bairro }} — {{ $endereco->cidade }}/{{ $endereco->estado }}
                                            <br>
                                            CEP {{ $endereco->cep }}
                                            @if ($endereco->telefone) <br>{{ $endereco->telefone }} @endif
                                        </p>
                                        <div class="conta-endereco-acoes">
                                            <a href="{{ route('conta.enderecos.edit', $endereco) }}">Editar</a>

                                            @unless ($endereco->is_padrao)
                                                <form method="POST" action="{{ route('conta.enderecos.padrao', $endereco) }}" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="conta-link-btn">Definir como padrão</button>
                                                </form>
                                            @endunless

                                            <form method="POST" action="{{ route('conta.enderecos.destroy', $endereco) }}"
                                                  style="display:inline;" onsubmit="return confirm('Remover este endereço?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="conta-link-btn conta-link-btn-danger">Remover</button>
                                            </form>
                                        </div>
                                    </div>
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
