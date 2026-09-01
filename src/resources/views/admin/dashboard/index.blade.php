@extends('layout.admin')

@section('page-title', 'Dashboard')

@section('content')

<div class="row mt-3">
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-primary">
            <div class="inner">
                <h3>{{ $stats['totalPedidos'] }}</h3>
                <p>Total de Pedidos</p>
            </div>
            <i class="bi bi-receipt small-box-icon"></i>
            <a href="{{ route('admin.pedidos.index') }}" class="small-box-footer link-light">
                Ver todos <i class="bi bi-link-45deg"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-warning">
            <div class="inner">
                <h3>{{ $stats['pedidosPendentes'] }}</h3>
                <p>Pedidos Pendentes</p>
            </div>
            <i class="bi bi-hourglass-split small-box-icon"></i>
            <a href="{{ route('admin.pedidos.index', ['status' => \App\Models\Order::STATUS_PENDENTE]) }}" class="small-box-footer link-dark">
                Ver todos <i class="bi bi-link-45deg"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-success">
            <div class="inner">
                <h3>{{ $stats['totalProdutos'] }}</h3>
                <p>Produtos Cadastrados</p>
            </div>
            <i class="bi bi-box-seam-fill small-box-icon"></i>
            <a href="{{ route('admin.produtos.index') }}" class="small-box-footer link-light">
                Ver todos <i class="bi bi-link-45deg"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-info">
            <div class="inner">
                <h3>{{ $stats['totalClientes'] }}</h3>
                <p>Clientes Cadastrados</p>
            </div>
            <i class="bi bi-people-fill small-box-icon"></i>
            <a href="{{ route('admin.clientes.index') }}" class="small-box-footer link-light">
                Ver todos <i class="bi bi-link-45deg"></i>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Últimos Pedidos</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped m-0">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ultimosPedidos as $pedido)
                        <tr>
                            <td>{{ $pedido->numero_pedido }}</td>
                            <td>{{ $pedido->nome }} {{ $pedido->sobrenome }}</td>
                            <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                            <td>R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ \App\Support\OrderStatus::badgeClass($pedido->status) }}">
                                    {{ \App\Support\OrderStatus::label($pedido->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.pedidos.show', $pedido) }}" class="link-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Nenhum pedido ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
