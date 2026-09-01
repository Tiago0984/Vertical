@extends('layout.admin')

@section('page-title', 'Pedidos')

@section('content')

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Pedidos</h3>
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('admin.pedidos.index') }}" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por número do pedido ou e-mail..."
                       value="{{ request('busca') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Todos os status</option>
                    @foreach (\App\Models\Order::STATUSES as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                            {{ \App\Support\OrderStatus::label($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-secondary w-100">Filtrar</button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped m-0">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>E-mail</th>
                        <th>Data</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pedidos as $pedido)
                        <tr>
                            <td>{{ $pedido->numero_pedido }}</td>
                            <td>{{ $pedido->nome }} {{ $pedido->sobrenome }}</td>
                            <td>{{ $pedido->email }}</td>
                            <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                            <td>R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ \App\Support\OrderStatus::badgeClass($pedido->status) }}">
                                    {{ \App\Support\OrderStatus::label($pedido->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.pedidos.show', $pedido) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-4">Nenhum pedido encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($pedidos->hasPages())
        <div class="card-footer">
            {{ $pedidos->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
