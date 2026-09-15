@extends('layout.admin')

@section('page-title', 'Cupons')

@section('content')

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Cupons</h3>
        <a href="{{ route('admin.cupons.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Novo Cupom
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped m-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Validade</th>
                        <th>Usos</th>
                        <th>Estado</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cupons as $cupom)
                        <tr>
                            <td><code>{{ $cupom->codigo }}</code></td>
                            <td>{{ ucfirst($cupom->tipo) }}</td>
                            <td>
                                {{ $cupom->tipo === \App\Models\Coupon::TIPO_PERCENTUAL ? number_format($cupom->valor, 0, ',', '.').'%' : 'R$ '.number_format($cupom->valor, 2, ',', '.') }}
                            </td>
                            <td>
                                @if (! $cupom->inicio_em && ! $cupom->fim_em)
                                    <span class="text-secondary">Sem limite</span>
                                @else
                                    {{ $cupom->inicio_em?->format('d/m/Y') ?? '—' }} a {{ $cupom->fim_em?->format('d/m/Y') ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $cupom->usos }}{{ $cupom->uso_maximo !== null ? ' / '.$cupom->uso_maximo : '' }}</td>
                            <td>
                                <span class="badge {{ \App\Support\CouponStatus::badgeClass($cupom) }}">
                                    {{ \App\Support\CouponStatus::label($cupom) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <form action="{{ route('admin.cupons.toggle-ativo', $cupom) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        {{ $cupom->ativo ? 'Desativar' : 'Ativar' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.cupons.edit', $cupom) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if ($cupom->usos === 0)
                                    <form action="{{ route('admin.cupons.destroy', $cupom) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Excluir o cupom &quot;{{ $cupom->codigo }}&quot;? Essa ação não pode ser desfeita.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-danger" disabled
                                            title="Cupons já usados não podem ser excluídos -- desative em vez de apagar.">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-4">Nenhum cupom cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($cupons->hasPages())
        <div class="card-footer">
            {{ $cupons->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
