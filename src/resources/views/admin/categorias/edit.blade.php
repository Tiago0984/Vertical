@extends('layout.admin')

@section('page-title', 'Editar Categoria')

@section('content')

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Editar Categoria — {{ $categoria->nome }}</h3>
    </div>
    <form method="POST" action="{{ route('admin.categorias.update', $categoria) }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            @include('admin.categorias._form')
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('admin.categorias.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

@endsection
