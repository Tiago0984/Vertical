@extends('layout.admin')

@section('page-title', 'Dashboard')

@section('content')

{{--
    Duas zonas visualmente separadas (decisão da fase 8D):
    - Zona de período: tudo que muda com o filtro (resumo, vendas, ranking,
      financeiro, clientes) fica DENTRO do card que contém o filtro.
    - Zona de estado atual: produtos/contas cadastradas e estoque ficam FORA,
      sempre com o mesmo valor independente do filtro selecionado.
--}}

<div class="card border-primary-subtle mb-4">
    <div class="card-header bg-primary-subtle">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="row gy-2 gx-3 align-items-end" id="form-periodo">
            <div class="col-auto">
                <label for="periodo" class="form-label mb-0 small text-secondary">Período</label>
                <select name="periodo" id="periodo" class="form-select form-select-sm" onchange="
                    document.getElementById('campos-periodo-custom').classList.toggle('d-none', this.value !== 'custom');
                    if (this.value !== 'custom') { this.form.submit(); }
                ">
                    <option value="30d" {{ $periodo === '30d' ? 'selected' : '' }}>Últimos 30 dias</option>
                    <option value="mes" {{ $periodo === 'mes' ? 'selected' : '' }}>Este mês</option>
                    <option value="12m" {{ $periodo === '12m' ? 'selected' : '' }}>Últimos 12 meses</option>
                    <option value="custom" {{ $periodo === 'custom' ? 'selected' : '' }}>Intervalo personalizado</option>
                </select>
            </div>
            <div id="campos-periodo-custom" class="col-auto d-flex gap-2 {{ $periodo === 'custom' ? '' : 'd-none' }}">
                <div>
                    <label for="inicio" class="form-label mb-0 small text-secondary">De</label>
                    <input type="date" name="inicio" id="inicio" class="form-control form-control-sm" value="{{ $inicio->toDateString() }}">
                </div>
                <div>
                    <label for="fim" class="form-label mb-0 small text-secondary">Até</label>
                    <input type="date" name="fim" id="fim" class="form-control form-control-sm" value="{{ $fim->toDateString() }}">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
            </div>
            <div class="col-auto ms-auto text-secondary small">
                Exibindo <strong>{{ $inicio->format('d/m/Y') }}</strong> até <strong>{{ $fim->format('d/m/Y') }}</strong>
            </div>
        </form>
    </div>

    <div class="card-body">

        {{-- ===== Cards do período ===== --}}
        <div class="row g-3">
            <div class="col-lg-3 col-md-4 col-6">
                @include('admin.partials.stat-card', [
                    'label' => 'Pedidos no total',
                    'value' => $resumo['total_pedidos'],
                    'href' => route('admin.pedidos.index'),
                ])
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                @include('admin.partials.stat-card', [
                    'label' => 'Pedidos pagos',
                    'value' => $resumo['pedidos_pagos'],
                ])
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                @include('admin.partials.stat-card', [
                    'label' => 'Pedidos pendentes',
                    'value' => $resumo['pedidos_pendentes'],
                    'tone' => $resumo['pedidos_pendentes'] > 0 ? 'warning' : 'neutro',
                    'href' => route('admin.pedidos.index', ['status' => \App\Models\Order::STATUS_PENDENTE]),
                ])
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                @include('admin.partials.stat-card', [
                    'label' => 'Ticket médio',
                    'value' => 'R$ '.number_format($resumo['ticket_medio'], 2, ',', '.'),
                ])
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                @include('admin.partials.stat-card', [
                    'label' => 'Faturamento',
                    'value' => 'R$ '.number_format($resumo['faturamento'], 2, ',', '.'),
                    'sublabel' => 'com frete',
                ])
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                @include('admin.partials.stat-card', [
                    'label' => 'Receita de produtos',
                    'value' => 'R$ '.number_format($receitaProdutos, 2, ',', '.'),
                    'sublabel' => 'sem frete',
                ])
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                @include('admin.partials.stat-card', [
                    'label' => 'Clientes que compraram',
                    'value' => $resumo['clientes_unicos_pagantes'],
                    'sublabel' => 'compraram no período',
                ])
            </div>
        </div>

        {{-- ===== Gráfico de vendas ===== --}}
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Vendas {{ $vendasGranularidade === 'dia' ? 'por dia' : 'por mês' }}</h3>
            </div>
            <div class="card-body">
                <div id="grafico-vendas"></div>
            </div>
        </div>

        {{-- ===== Ranking de produtos ===== --}}
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Ranking de Produtos</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped m-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produto</th>
                                <th>Qtd. vendida</th>
                                <th>Receita de produtos</th>
                                <th>% do total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ranking->take(10) as $i => $linha)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        {{ $linha['produto'] }}
                                        @unless ($linha['product_id'])
                                            <span class="badge text-bg-secondary">excluído do catálogo</span>
                                        @endunless
                                    </td>
                                    <td>{{ $linha['qtd_vendida'] }}</td>
                                    <td>R$ {{ number_format($linha['receita'], 2, ',', '.') }}</td>
                                    <td>{{ $receitaProdutos > 0 ? number_format($linha['receita'] / $receitaProdutos * 100, 1, ',', '.') : '0,0' }}%</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary py-4">Nenhuma venda no período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===== Financeiro ===== --}}
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Financeiro</h3>
            </div>
            <div class="card-body">
                @if (! $financeiro['margem_confiavel'])
                    <div class="alert alert-danger">
                        <strong>Margem não confiável neste período.</strong>
                        Só {{ $financeiro['cobertura_custo']['produtos_com_custo'] }} de
                        {{ $financeiro['cobertura_custo']['produtos_vendidos'] }} produtos vendidos têm custo
                        cadastrado ({{ number_format($financeiro['cobertura_custo']['percentual'] * 100, 1, ',', '.') }}%,
                        abaixo do mínimo de {{ number_format($financeiro['cobertura_custo']['minimo_exigido'] * 100, 0, ',', '.') }}%
                        exigido). Cadastre o custo dos produtos em falta pra liberar o cálculo de margem.
                    </div>
                @else
                    <p class="text-secondary small mb-3">
                        Cobertura de custo: {{ $financeiro['cobertura_custo']['produtos_com_custo'] }} de
                        {{ $financeiro['cobertura_custo']['produtos_vendidos'] }} produtos vendidos
                        ({{ number_format($financeiro['cobertura_custo']['percentual'] * 100, 1, ',', '.') }}%).
                    </p>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped m-0">
                        <thead>
                            <tr>
                                <th>Mês</th>
                                <th>Receita de produtos</th>
                                <th>CMV</th>
                                <th>Lucro bruto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($financeiro['serie_mensal'] as $linha)
                                <tr>
                                    <td>{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $linha['mes'])->translatedFormat('M/Y') }}</td>
                                    <td>R$ {{ number_format($linha['receita'], 2, ',', '.') }}</td>
                                    <td>{{ $linha['cmv'] !== null ? 'R$ '.number_format($linha['cmv'], 2, ',', '.') : '—' }}</td>
                                    <td>{{ $linha['lucro_bruto'] !== null ? 'R$ '.number_format($linha['lucro_bruto'], 2, ',', '.') : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===== Clientes ===== --}}
        <div class="card mt-4 mb-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Clientes que compraram no período</h3>
                <a href="{{ route('admin.clientes.index') }}" class="btn btn-outline-secondary btn-sm">Ver contas cadastradas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped m-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>E-mail</th>
                                <th>Pedidos</th>
                                <th>Total gasto</th>
                                <th>Ticket médio</th>
                                <th>Segmento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($clientes->take(10) as $cliente)
                                <tr>
                                    <td>{{ $cliente['nome'] }}</td>
                                    <td>{{ $cliente['email'] }}</td>
                                    <td>{{ $cliente['pedidos'] }}</td>
                                    <td>R$ {{ number_format($cliente['total_gasto'], 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format($cliente['ticket_medio'], 2, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ match ($cliente['segmento']) {
                                            'VIP' => 'text-bg-dark',
                                            'Recorrente' => 'text-bg-info',
                                            default => 'text-bg-light text-dark',
                                        } }}">{{ $cliente['segmento'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">Nenhum cliente comprou no período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ===== ZONA DE ESTADO ATUAL (fora do filtro de período) ===== --}}
<h6 class="text-uppercase text-secondary small mb-2">
    <i class="bi bi-clock-history"></i> Estado atual — não muda com o filtro acima
</h6>

<div class="row g-3 mb-3">
    <div class="col-lg-3 col-md-4 col-6">
        @include('admin.partials.stat-card', [
            'label' => 'Produtos Cadastrados',
            'value' => $totalProdutos,
            'href' => route('admin.produtos.index'),
        ])
    </div>
    <div class="col-lg-3 col-md-4 col-6">
        @include('admin.partials.stat-card', [
            'label' => 'Contas cadastradas',
            'value' => $totalContas,
            'sublabel' => 'usuários com cadastro no site',
            'href' => route('admin.clientes.index'),
        ])
    </div>
    <div class="col-lg-3 col-md-4 col-6">
        @include('admin.partials.stat-card', [
            'label' => 'Produtos para repor',
            'value' => $estoqueContagem->get(\App\Services\DashboardService::ESTOQUE_REPOR, 0),
            'tone' => $estoqueContagem->get(\App\Services\DashboardService::ESTOQUE_REPOR, 0) > 0 ? 'danger' : 'neutro',
        ])
    </div>
    <div class="col-lg-3 col-md-4 col-6">
        @include('admin.partials.stat-card', [
            'label' => 'Produtos em atenção',
            'value' => $estoqueContagem->get(\App\Services\DashboardService::ESTOQUE_ATENCAO, 0),
            'tone' => $estoqueContagem->get(\App\Services\DashboardService::ESTOQUE_ATENCAO, 0) > 0 ? 'warning' : 'neutro',
        ])
    </div>
</div>

<div class="card mb-0">
    <div class="card-header">
        <h3 class="card-title">Estoque</h3>
        <span class="text-secondary small">cobertura baseada nos últimos {{ $estoque['cobertura_baseada_em_dias'] }} dias</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped m-0">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Estoque</th>
                        <th>Mínimo</th>
                        <th>Status</th>
                        <th>Média vendida/mês</th>
                        <th>Cobertura</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($estoque['produtos'] as $produto)
                        <tr>
                            <td>{{ $produto['nome'] }}</td>
                            <td>{{ $produto['estoque'] }}</td>
                            <td>{{ $produto['estoque_minimo'] }}</td>
                            <td>
                                <span class="badge {{ \App\Support\EstoqueStatus::badgeClass($produto['status']) }}">
                                    {{ \App\Support\EstoqueStatus::label($produto['status']) }}
                                </span>
                            </td>
                            <td>{{ number_format($produto['media_mensal_vendida'], 2, ',', '.') }}</td>
                            <td>{{ $produto['cobertura_meses'] !== null ? number_format($produto['cobertura_meses'], 1, ',', '.').' meses' : '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Nenhum produto cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js" crossorigin="anonymous"></script>
<script>
    const vendasCategorias = @json($vendas->pluck($vendasGranularidade === 'dia' ? 'data' : 'mes'));
    const vendasFaturamento = @json($vendas->pluck('faturamento'));

    new ApexCharts(document.querySelector('#grafico-vendas'), {
        series: [{ name: 'Faturamento (com frete)', data: vendasFaturamento }],
        chart: { type: 'area', height: 280, toolbar: { show: false } },
        colors: ['#0d6efd'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        xaxis: { categories: vendasCategorias },
        yaxis: {
            labels: {
                formatter: (v) => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 0 }),
            },
        },
        tooltip: {
            y: {
                formatter: (v) => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 }),
            },
        },
    }).render();
</script>
@endsection
