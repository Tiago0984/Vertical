@extends('layout.admin')

@section('page-title', 'Produtos')

@section('content')

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title mb-0">Produtos</h3>
        <a href="{{ route('admin.produtos.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Novo Produto
        </a>
    </div>

    <div class="card-body border-bottom">
        <form method="GET" action="{{ route('admin.produtos.index') }}" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por nome..."
                       value="{{ request('busca') }}">
            </div>
            <div class="col-md-4">
                <select name="categoria_id" class="form-select">
                    <option value="">Todas as categorias</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ (string) request('categoria_id') === (string) $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nome }}
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
                        <th></th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($produtos as $produto)
                        <tr>
                            <td>
                                <img src="{{ asset($produto->imagem) }}" alt="{{ $produto->nome }}"
                                     class="rounded" style="width:48px;height:48px;object-fit:cover;">
                            </td>
                            <td>{{ $produto->nome }}</td>
                            <td>{{ $produto->category?->nome ?? '—' }}</td>
                            <td>
                                R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                @if ($produto->preco_promocional)
                                    <br>
                                    <span class="text-decoration-line-through text-secondary">
                                        R$ {{ number_format($produto->preco_promocional, 2, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($produto->is_novo)
                                    <span class="badge text-bg-info">Novo</span>
                                @endif
                                @if ($produto->is_promocao)
                                    <span class="badge text-bg-danger">Promoção</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.produtos.edit', $produto) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.produtos.destroy', $produto) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Excluir o produto &quot;{{ $produto->nome }}&quot;? Essa ação não pode ser desfeita.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Nenhum produto encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($produtos->hasPages())
        <div class="card-footer">
            {{ $produtos->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
