@extends('layout.admin')

@section('page-title', 'Nova Categoria')

@section('content')

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Nova Categoria</h3>
    </div>
    <form method="POST" action="{{ route('admin.categorias.store') }}">
        @csrf
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
