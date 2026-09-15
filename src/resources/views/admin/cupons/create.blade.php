@extends('layout.admin')

@section('page-title', 'Novo Cupom')

@section('content')

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Novo Cupom</h3>
    </div>
    <form method="POST" action="{{ route('admin.cupons.store') }}">
        @csrf
        <div class="card-body">
            @include('admin.cupons._form')
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('admin.cupons.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

@endsection
