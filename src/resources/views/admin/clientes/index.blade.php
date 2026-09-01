@extends('layout.admin')

@section('page-title', 'Clientes')

@section('content')

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Clientes</h3>
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('admin.clientes.index') }}" class="row g-2">
            <div class="col-md-8">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou e-mail..."
                       value="{{ request('busca') }}">
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
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Pedidos</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td>{{ $cliente->name }} {{ $cliente->sobrenome }}</td>
                            <td>{{ $cliente->email }}</td>
                            <td>{{ $cliente->telefone ?? '—' }}</td>
                            <td>{{ $cliente->orders_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.clientes.show', $cliente) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-4">Nenhum cliente encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($clientes->hasPages())
        <div class="card-footer">
            {{ $clientes->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
