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
            'vendas' => $vendas,
            'vendasGranularidade' => $granularidadeDiaria ? 'dia' : 'mes',
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
