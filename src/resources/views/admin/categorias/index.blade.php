@extends('layout.admin')

@section('page-title', 'Categorias')

@section('content')

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Categorias</h3>
        <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Nova Categoria
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped m-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Produtos</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categorias as $categoria)
                        <tr>
                            <td>{{ $categoria->nome }}</td>
                            <td>{{ $categoria->products_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.categorias.edit', $categoria) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Excluir a categoria &quot;{{ $categoria->nome }}&quot;? {{ $categoria->products_count }} produto(s) ficarão sem categoria.');">
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
                            <td colspan="3" class="text-center text-secondary py-4">Nenhuma categoria cadastrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($categorias->hasPages())
        <div class="card-footer">
            {{ $categorias->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
