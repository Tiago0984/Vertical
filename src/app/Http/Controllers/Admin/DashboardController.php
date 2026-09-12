<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    private const PERIODOS_VALIDOS = ['30d', 'mes', '12m', 'custom'];

    public function index(Request $request, DashboardService $service)
    {
        [$periodo, $inicio, $fim] = $this->resolverPeriodo($request);

        $resumo = $service->resumo($inicio, $fim);
        $ranking = $service->rankingProdutos($inicio, $fim);
        $financeiro = $service->financeiro($inicio, $fim);
        $clientes = $service->clientes($inicio, $fim);

        // estoque não recebe período de propósito -- é estado atual, a
        // cobertura usa uma janela fixa própria (ver DashboardService::estoque()).
        $estoque = $service->estoque();

        // "vendas por mês" sobre uma janela de 30 dias mostraria 1-2 pontos só;
        // dia a dia pra períodos curtos, mês a mês pra períodos longos.
        $granularidadeDiaria = $inicio->diffInDays($fim) <= 60;
        $vendas = $granularidadeDiaria
            ? $service->vendasPorDia($inicio, $fim)
            : $service->vendasPorMes($inicio, $fim);

        // Série inteira zerada (ex.: produção sem nenhum pedido pago ainda) --
        // desenhar um gráfico de R$0 a R$5 com grade fingindo escala é o
        // mesmo problema do "0 de 0 produtos" do financeiro: parece dado
        // real e não é. Decisão feita aqui, não depois de já ter renderizado.
        $graficoVendasVazio = $vendas->sum('faturamento') <= 0.0;

        // ApexCharts com xaxis.type 'datetime' deriva os ticks sozinho a
        // partir de pares [timestamp_ms, valor] na série -- funciona bem.
        // Mas com xaxis.type 'category' ele NÃO deriva rótulo nenhum desses
        // pares (cai pra índice numérico 1,2,3... e ainda repete algum) --
        // categoria precisa vir em xaxis.categories à parte, por isso as
        // duas granularidades têm formato de série diferente aqui.
        if ($granularidadeDiaria) {
            $vendasSerie = $vendas->map(fn (array $linha) => [
                Carbon::createFromFormat('Y-m-d', $linha['data'], 'UTC')->getTimestamp() * 1000,
                $linha['faturamento'],
            ])->values();
            $vendasCategorias = null;
        } else {
            // Rótulo 'mm/aaaa' formatado aqui, não no JS.
            $vendasCategorias = $vendas->map(
                fn (array $linha) => Carbon::createFromFormat('Y-m', $linha['mes'], 'UTC')->format('m/Y')
            )->values();
            $vendasSerie = $vendas->pluck('faturamento')->values();
        }

        return view('admin.dashboard.index', [
            'periodo' => $periodo,
            'inicio' => $inicio,
            'fim' => $fim,
            'resumo' => $resumo,
            'ranking' => $ranking,
            'financeiro' => $financeiro,
            'clientes' => $clientes,
            'estoque' => $estoque,
            'estoqueContagem' => $estoque['produtos']->countBy('status'),
            'vendasGranularidade' => $granularidadeDiaria ? 'dia' : 'mes',
            'vendasSerie' => $vendasSerie,
            'vendasCategorias' => $vendasCategorias,
            'graficoVendasVazio' => $graficoVendasVazio,
            'receitaProdutos' => round($ranking->sum('receita'), 2),
            // zona de estado atual -- fora do alcance do filtro de período.
            'totalProdutos' => Product::count(),
            'totalContas' => User::where('is_admin', false)->count(),
        ]);
    }

    /**
     * @return array{0: string, 1: Carbon, 2: Carbon}
     */
    private function resolverPeriodo(Request $request): array
    {
        $periodo = $request->input('periodo', '30d');
        if (! in_array($periodo, self::PERIODOS_VALIDOS, true)) {
            $periodo = '30d';
        }

        $fim = Carbon::now();

        if ($periodo === 'custom') {
            if ($request->filled('inicio') && $request->filled('fim')) {
                try {
                    $inicioCustom = Carbon::parse($request->input('inicio'))->startOfDay();
                    $fimCustom = Carbon::parse($request->input('fim'))->endOfDay();

                    if ($inicioCustom->greaterThan($fimCustom)) {
                        [$inicioCustom, $fimCustom] = [$fimCustom->copy()->startOfDay(), $inicioCustom->copy()->endOfDay()];
                    }

                    return [$periodo, $inicioCustom, $fimCustom];
                } catch (\Exception) {
                    // data inválida digitada na URL -- cai pro padrão em vez de 500.
                    $periodo = '30d';
                }
            } else {
                $periodo = '30d';
            }
        }

        $inicio = match ($periodo) {
            'mes' => Carbon::now()->startOfMonth(),
            '12m' => Carbon::now()->subMonths(11)->startOfMonth(),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };

        return [$periodo, $inicio, $fim];
    }
}
