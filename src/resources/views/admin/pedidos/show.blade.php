@extends('layout.admin')

@section('page-title', 'Pedido #' . $pedido->numero_pedido)

@section('content')

<div class="row mt-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Itens do Pedido</h3>
            </div>
            <div class="card-body p-0">
                @php
                    // Cor é atributo do produto hoje (não mais um seletor no checkout) --
                    // pedido novo grava cor null em todo item. A coluna só aparece pra
                    // pedido antigo, que tem cor de verdade gravada; pedido novo não deve
                    // mostrar uma coluna cheia de traços.
                    $temCor = $pedido->items->contains(fn ($item) => filled($item->cor));
                @endphp
                <div class="table-responsive">
                    <table class="table table-striped m-0">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                @if ($temCor)
                                    <th>Cor</th>
                                @endif
                                <th>Tamanho</th>
                                <th>Qtd.</th>
                                <th>Preço</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pedido->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->nome }}
                                        @unless ($item->product)
                                            <span class="badge text-bg-secondary">produto removido</span>
                                        @endunless
                                    </td>
                                    @if ($temCor)
                                        <td>{{ $item->cor ?? '—' }}</td>
                                    @endif
                                    <td>{{ $item->tamanho ?? '—' }}</td>
                                    <td>{{ $item->quantidade }}</td>
                                    <td>R$ {{ number_format($item->preco, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <table class="table table-borderless w-auto mb-0">
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end">R$ {{ number_format($pedido->subtotal, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Frete</td>
                            <td class="text-end">R$ {{ number_format($pedido->frete, 2, ',', '.') }}</td>
                        </tr>
                        @if ($pedido->cupom)
                            <tr>
                                <td>Cupom</td>
                                <td class="text-end">{{ $pedido->cupom }}</td>
                            </tr>
                        @endif
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-end">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Endereço de Entrega</h3>
            </div>
            <div class="card-body">
                @php $end = $pedido->endereco_entrega; @endphp
                {{ $end['rua'] ?? '' }}, {{ $end['numero'] ?? '' }}
                @if (! empty($end['complemento'])) — {{ $end['complemento'] }} @endif
                <br>
                {{ $end['bairro'] ?? '' }} — {{ $end['cidade'] ?? '' }}/{{ $end['uf'] ?? '' }}
                <br>
                CEP: {{ $end['cep'] ?? '' }}
                @if (! empty($end['referencia']))
                    <br>Referência: {{ $end['referencia'] }}
                @endif
            </div>
        </div>

        @if ($pedido->endereco_faturamento)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Endereço de Faturamento</h3>
                </div>
                <div class="card-body">
                    @php $fat = $pedido->endereco_faturamento; @endphp
                    {{ $fat['rua'] ?? '' }}, {{ $fat['numero'] ?? '' }}
                    @if (! empty($fat['complemento'])) — {{ $fat['complemento'] }} @endif
                    <br>
                    {{ $fat['bairro'] ?? '' }} — {{ $fat['cidade'] ?? '' }}/{{ $fat['uf'] ?? '' }}
                    <br>
                    CEP: {{ $fat['cep'] ?? '' }}
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Status</h3>
            </div>
            <div class="card-body">
                <p>
                    <span class="badge {{ \App\Support\OrderStatus::badgeClass($pedido->status) }}">
                        {{ \App\Support\OrderStatus::label($pedido->status) }}
                    </span>
                </p>
                <form method="POST" action="{{ route('admin.pedidos.status', $pedido) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-2">
                        <select name="status" class="form-select">
                            @foreach (\App\Models\Order::STATUSES as $status)
                                <option value="{{ $status }}" {{ $pedido->status === $status ? 'selected' : '' }}>
                                    {{ \App\Support\OrderStatus::label($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Atualizar Status</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Cliente</h3>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $pedido->nome }} {{ $pedido->sobrenome }}</strong></p>
                <p class="mb-1">{{ $pedido->email }}</p>
                <p class="mb-1">{{ $pedido->telefone }}</p>
                <p class="mb-0">Pagamento: {{ ucfirst($pedido->forma_pagamento) }}</p>
                @if ($pedido->user)
                    <a href="{{ route('admin.clientes.show', $pedido->user) }}" class="btn btn-sm btn-outline-secondary mt-2">
                        Ver perfil do cliente
                    </a>
                @else
                    <span class="badge text-bg-secondary mt-2">compra sem cadastro</span>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
