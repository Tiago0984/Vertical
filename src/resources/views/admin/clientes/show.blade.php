@extends('layout.admin')

@section('page-title', 'Cliente — ' . $cliente->name)

@section('content')

<div class="row mt-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Dados do Cliente</h3>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $cliente->name }} {{ $cliente->sobrenome }}</strong></p>
                <p class="mb-1">{{ $cliente->email }}</p>
                <p class="mb-1">{{ $cliente->telefone ?? 'Telefone não informado' }}</p>
                <p class="mb-0">Cliente desde {{ $cliente->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        @if ($cliente->addresses->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Endereços</h3>
                </div>
                <div class="card-body">
                    @foreach ($cliente->addresses as $endereco)
                        <p class="mb-2 {{ ! $loop->last ? 'border-bottom pb-2' : '' }}">
                            {{ $endereco->endereco }}, {{ $endereco->numero }} — {{ $endereco->bairro }}<br>
                            {{ $endereco->cidade }}/{{ $endereco->estado }} — CEP {{ $endereco->cep }}
                            @if ($endereco->is_padrao)
                                <span class="badge text-bg-primary ms-1">padrão</span>
                            @endif
                        </p>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Histórico de Pedidos</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped m-0">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Data</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cliente->orders->sortByDesc('created_at') as $pedido)
                                <tr>
                                    <td>{{ $pedido->numero_pedido }}</td>
                                    <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                    <td>R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ \App\Support\OrderStatus::badgeClass($pedido->status) }}">
                                            {{ \App\Support\OrderStatus::label($pedido->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.pedidos.show', $pedido) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">Este cliente ainda não fez nenhum pedido.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
