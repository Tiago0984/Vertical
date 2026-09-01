@extends('layout.admin')

@section('page-title', 'Editar Produto')

@section('content')

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Editar Produto — {{ $produto->nome }}</h3>
    </div>
    <form method="POST" action="{{ route('admin.produtos.update', $produto) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            @include('admin.produtos._form')
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('admin.produtos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

@endsection
