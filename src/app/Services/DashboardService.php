<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\EstoqueStatus;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * KPIs do dashboard administrativo.
 *
 * Todo método recebe o intervalo explicitamente ($inicio/$fim) -- o service
 * não guarda período nenhum, quem decide o default (ex.: últimos 30 dias)
 * é o controller/tela que chama. Isso evita ter que mudar a assinatura de
 * tudo aqui quando o filtro de período mudar de lugar na UI.
 *
 * Três regras valem pra TODA query de faturamento/receita neste service:
 * 1. status = pago sempre -- orders.status também tem pendente_pagamento e
 *    cancelado, que não são receita.
 * 2. orders.total inclui frete; orders.subtotal não. Métricas de "faturamento"
 *    usam total; nunca somam subtotal achando que é a mesma coisa.
 * 3. Não existe paid_at -- created_at é a única data (data do PEDIDO, não do
 *    pagamento), e é o que toda série temporal usa.
 *
 * E uma armadilha: orders.user_id é nullable (checkout aceita convidado).
 * Qualquer agrupamento "por cliente" usa email (obrigatório em orders),
 * nunca user_id -- senão todo pedido de convidado some da contagem.
 *
 * RÓTULOS (isto é reclamação nº 1 de quem olha dashboard, então é fixo,
 * não um detalhe de estilo): toda chave "faturamento" retornada por este
 * service vem de orders.total (com frete) -- a UI DEVE rotular como
 * "Faturamento (com frete)". O ranking de produtos (baseado em
 * order_items.subtotal, sem frete) usa a chave "receita" e a UI DEVE
 * rotular como "Receita de produtos". Os dois números nunca vão bater
 * entre si, e isso é esperado -- não é bug, é frete não entrando na conta
 * de produto.
 */
class DashboardService
{
    public const ESTOQUE_REPOR = 'repor';

    public const ESTOQUE_ATENCAO = 'atencao';

    public const ESTOQUE_OK = 'ok';

    /** estoque_minimo = 0 (default da migration) -- ninguém configurou ainda, não é alerta. */
    public const ESTOQUE_NAO_CONFIGURADO = 'nao_configurado';

    /**
     * Contadores e totais do período: pedidos por status, faturamento,
     * ticket médio e clientes únicos pagantes.
     */
    public function resumo(Carbon $inicio, Carbon $fim): array
    {
        $baseQuery = fn () => Order::whereBetween('created_at', [$inicio, $fim]);

        $totalPedidos = $baseQuery()->count();
        $pedidosPagos = $baseQuery()->where('status', Order::STATUS_PAGO)->count();
        $pedidosCancelados = $baseQuery()->where('status', Order::STATUS_CANCELADO)->count();
        $pedidosPendentes = $baseQuery()->where('status', Order::STATUS_PENDENTE)->count();

        $faturamento = (float) $baseQuery()->where('status', Order::STATUS_PAGO)->sum('total');
        $ticketMedio = $pedidosPagos > 0 ? $faturamento / $pedidosPagos : 0.0;

        // "cliente" = email (orders.user_id é nullable, convidado não tem).
        $clientesUnicosPagantes = $baseQuery()
            ->where('status', Order::STATUS_PAGO)
            ->distinct()
            ->count('email');

        return [
            'periodo' => [
                'inicio' => $inicio->toDateString(),
                'fim' => $fim->toDateString(),
            ],
            'total_pedidos' => $totalPedidos,
            'pedidos_pagos' => $pedidosPagos,
            'pedidos_cancelados' => $pedidosCancelados,
            'pedidos_pendentes' => $pedidosPendentes,
            // Rótulo obrigatório na UI: "Faturamento (com frete)" -- ver docblock da classe.
            'faturamento' => round($faturamento, 2),
            'ticket_medio' => round($ticketMedio, 2),
            'clientes_unicos_pagantes' => $clientesUnicosPagantes,
        ];
    }

    /**
     * Série de faturamento por DIA, com todo dia do intervalo presente
     * (mesmo com faturamento zero) -- um GROUP BY simples omitiria dias sem
     * venda, e o gráfico "encolheria" o eixo X em vez de mostrar o buraco.
     *
     * @return Collection<int, array{data: string, faturamento: float, pedidos: int}>
     */
    public function vendasPorDia(Carbon $inicio, Carbon $fim): Collection
    {
        $porDia = Order::where('status', Order::STATUS_PAGO)
            ->whereBetween('created_at', [$inicio, $fim])
            ->selectRaw('DATE(created_at) as dia, SUM(total) as faturamento, COUNT(*) as pedidos')
            ->groupBy('dia')
            ->get()
            ->keyBy('dia');

        $periodo = CarbonPeriod::create($inicio->copy()->startOfDay(), $fim->copy()->startOfDay());

        return collect($periodo)->map(function (Carbon $dia) use ($porDia) {
            $chave = $dia->toDateString();
            $linha = $porDia->get($chave);

            return [
                'data' => $chave,
                'faturamento' => round((float) ($linha->faturamento ?? 0), 2),
                'pedidos' => (int) ($linha->pedidos ?? 0),
            ];
        })->values();
    }

    /**
     * Mesma ideia de vendasPorDia(), mas agrupado por MÊS -- é o que expõe
     * o mês sem nenhuma venda (ex.: março/2026 no seeder de demonstração):
     * sem o preenchimento explícito, esse mês simplesmente não apareceria
     * no resultado do GROUP BY, e o gráfico pularia de fevereiro pra abril
     * sem nenhum indício visual de que faltou alguma coisa.
     *
     * @return Collection<int, array{mes: string, faturamento: float, pedidos: int}>
     */
    public function vendasPorMes(Carbon $inicio, Carbon $fim): Collection
    {
        $porMes = Order::where('status', Order::STATUS_PAGO)
            ->whereBetween('created_at', [$inicio, $fim])
            ->selectRaw($this->mesExpressaoSql('created_at')." as mes, SUM(total) as faturamento, COUNT(*) as pedidos")
            ->groupBy('mes')
            ->get()
            ->keyBy('mes');

        $periodo = CarbonPeriod::create(
            $inicio->copy()->startOfMonth(),
            '1 month',
            $fim->copy()->startOfMonth()
        );

        return collect($periodo)->map(function (Carbon $mes) use ($porMes) {
            $chave = $mes->format('Y-m');
            $linha = $porMes->get($chave);

            return [
                'mes' => $chave,
                'faturamento' => round((float) ($linha->faturamento ?? 0), 2),
                'pedidos' => (int) ($linha->pedidos ?? 0),
            ];
        })->values();
    }

    /**
     * Status de estoque por produto + cobertura em meses (estoque ÷ média
     * mensal vendida, calculada de order_items/pedidos pagos -- nunca de um
     * campo digitado, que ficaria desatualizado).
     *
     * SEM PARÂMETRO DE PERÍODO DE PROPÓSITO: estoque é estado ATUAL, não uma
     * métrica que varia com o filtro de período do dashboard. A cobertura
     * sempre olha pra uma janela fixa de dias corridos (config
     * dashboard.estoque.janela_cobertura_dias, default 90) contados de
     * "agora" pra trás -- não pro período que a tela estiver mostrando.
     * Antes deste método aceitava $inicio/$fim e a média de venda mudava
     * junto com o filtro: "últimos 7 dias" extrapolava uma semana pra uma
     * média mensal, e o MESMO produto passava a "ter 4 meses de estoque" ou
     * "0,5 mês" só dependendo do que o usuário tinha clicado.
     *
     * ARMADILHA tratada de propósito: a migration criou estoque/estoque_minimo
     * com default 0. Sem essa checagem, todo produto que ninguém configurou
     * ainda cairia em "Repor" (0 <= 0) no dia do deploy -- alerta falso em
     * massa. estoque_minimo = 0 vira status NAO_CONFIGURADO, fora da
     * contagem de alertas (quem contar "repor"+"atencao" já exclui sozinho).
     *
     * @return array{
     *   cobertura_baseada_em_dias: int,
     *   produtos: Collection<int, array{
     *     id: int, nome: string, estoque: int, estoque_minimo: int,
     *     custo: ?float, status: string, media_mensal_vendida: float,
     *     cobertura_meses: ?float
     *   }>
     * }
     */
    public function estoque(): array
    {
        $janelaCoberturaDias = (int) config('dashboard.estoque.janela_cobertura_dias');

        $fimJanela = Carbon::now();
        $inicioJanela = Carbon::now()->subDays($janelaCoberturaDias);
        $mesesNaJanela = $janelaCoberturaDias / 30;

        $vendidoPorProduto = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', Order::STATUS_PAGO)
            ->whereBetween('orders.created_at', [$inicioJanela, $fimJanela])
            ->whereNotNull('order_items.product_id')
            ->groupBy('order_items.product_id')
            ->selectRaw('order_items.product_id, SUM(order_items.quantidade) as qtd_vendida')
            ->pluck('qtd_vendida', 'product_id');

        $produtos = Product::orderBy('nome')->get()->map(function (Product $produto) use ($vendidoPorProduto, $mesesNaJanela) {
            $estoque = (int) $produto->estoque;
            $estoqueMinimo = (int) $produto->estoque_minimo;
            $status = EstoqueStatus::calcular($estoque, $estoqueMinimo);

            $qtdVendida = (int) ($vendidoPorProduto[$produto->id] ?? 0);
            $mediaMensalVendida = round($qtdVendida / $mesesNaJanela, 2);

            // sem venda na janela -> "meses de cobertura" não existe (não é
            // infinito, é indefinido: não dá pra prever quando vai acabar
            // vendendo a essa taxa porque não há taxa nenhuma pra extrapolar).
            $coberturaMeses = $mediaMensalVendida > 0
                ? round($estoque / $mediaMensalVendida, 1)
                : null;

            return [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'estoque' => $estoque,
                'estoque_minimo' => $estoqueMinimo,
                'custo' => $produto->custo !== null ? (float) $produto->custo : null,
                'status' => $status,
                'media_mensal_vendida' => $mediaMensalVendida,
                'cobertura_meses' => $coberturaMeses,
            ];
        })->values();

        return [
            // rótulo explícito na SAÍDA, não só em comentário -- a UI deve
            // exibir algo como "cobertura baseada nos últimos 90 dias".
            'cobertura_baseada_em_dias' => $janelaCoberturaDias,
            'produtos' => $produtos,
        ];
    }

    /**
     * Ranking de produtos por receita (order_items.subtotal, SEM frete --
     * rótulo obrigatório na UI: "Receita de produtos", ver docblock da
     * classe). Origem é order_items, não products: um produto excluído do
     * catálogo continua tendo histórico de venda.
     *
     * order_items.product_id é nullOnDelete -- produto excluído vira
     * product_id NULL no histórico, mas o nome sobrevive congelado em
     * order_items.nome (snapshot do momento da compra). O COALESCE(nome do
     * catálogo, nome congelado) garante que a linha continue aparecendo no
     * ranking com um nome; sem isso ela sumiria e SUM(receita) do ranking
     * pararia de bater com o faturamento total do período.
     *
     * Também por isso o GROUP BY inclui a coluna "produto" (o COALESCE), não
     * só product_id: MySQL trata todo NULL como um único grupo, então dois
     * produtos DIFERENTES já excluídos (ambos com product_id NULL) seriam
     * somados juntos num "produto fantasma" se agrupássemos só por
     * product_id -- o nome congelado de cada um separa os grupos de novo.
     *
     * % do total e posição no ranking são apresentação, não entram aqui --
     * calcule na view a partir do SUM(receita) já retornado por esta lista.
     *
     * @return Collection<int, array{product_id: ?int, produto: string, qtd_vendida: int, receita: float}>
     */
    public function rankingProdutos(Carbon $inicio, Carbon $fim): Collection
    {
        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', Order::STATUS_PAGO)
            ->whereBetween('orders.created_at', [$inicio, $fim])
            ->selectRaw(
                'order_items.product_id as product_id, '
                .'COALESCE(products.nome, order_items.nome) as produto, '
                .'SUM(order_items.quantidade) as qtd_vendida, '
                .'SUM(order_items.subtotal) as receita'
            )
            ->groupBy('order_items.product_id', 'produto')
            ->orderByDesc('receita')
            ->get()
            ->map(fn ($linha) => [
                'product_id' => $linha->product_id !== null ? (int) $linha->product_id : null,
                'produto' => $linha->produto,
                'qtd_vendida' => (int) $linha->qtd_vendida,
                'receita' => round((float) $linha->receita, 2),
            ]);
    }

    /**
     * Série mensal de receita, CMV e lucro bruto -- e a defesa que não é
     * opcional: em produção custo é NULL nos 16 produtos hoje. Com
     * COALESCE(custo, 0) sem mais nada, a margem daria 100% em tudo -- pior
     * que o alerta falso de estoque, porque "repor tudo" salta aos olhos e
     * alguém investiga, enquanto "100% de margem" parece ótimo e ninguém
     * questiona. Número errado com cara de certo é pior que número ausente.
     *
     * Por isso todo retorno inclui 'cobertura_custo' (quantos produtos
     * vendidos no período têm custo cadastrado) e, se a cobertura ficar
     * abaixo do mínimo configurável (dashboard.financeiro.cobertura_custo_
     * minima, default 80%), cmv/lucro_bruto voltam NULL com
     * 'margem_confiavel' => false -- a tela deve mostrar o aviso, não o
     * número.
     *
     * Custo operacional fica de fora por padrão (não existe tabela de
     * despesas, e inventar percentual fixo aqui repetiria o erro da
     * planilha original, onde CMV+operacional chutados faziam a margem dar
     * sempre ~40% e o gráfico virar uma linha reta que não informava nada).
     * Só entra se o chamador passar $percentualCustoOperacionalEstimado
     * explicitamente -- aí sim vira lucro_liquido_estimado, rotulado como
     * estimativa. Sem o parâmetro, essas chaves saem null.
     *
     * @return array{
     *   margem_confiavel: bool,
     *   cobertura_custo: array{
     *     produtos_vendidos: int, produtos_com_custo: int,
     *     produtos_sem_custo: int, percentual: float, minimo_exigido: float
     *   },
     *   serie_mensal: Collection<int, array{
     *     mes: string, receita: float, cmv: ?float, lucro_bruto: ?float,
     *     custo_operacional_estimado: ?float, lucro_liquido_estimado: ?float
     *   }>
     * }
     */
    public function financeiro(Carbon $inicio, Carbon $fim, ?float $percentualCustoOperacionalEstimado = null): array
    {
        $coberturaMinima = (float) config('dashboard.financeiro.cobertura_custo_minima');

        $itensVendidosPorProduto = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', Order::STATUS_PAGO)
            ->whereBetween('orders.created_at', [$inicio, $fim])
            ->selectRaw('order_items.product_id, COALESCE(products.nome, order_items.nome) as produto, MAX(products.custo) as custo')
            ->groupBy('order_items.product_id', 'produto')
            ->get();

        $produtosVendidos = $itensVendidosPorProduto->count();
        $produtosComCusto = $itensVendidosPorProduto->filter(fn ($p) => $p->custo !== null)->count();
        $produtosSemCusto = $produtosVendidos - $produtosComCusto;
        $coberturaCusto = $produtosVendidos > 0 ? round($produtosComCusto / $produtosVendidos, 4) : 0.0;

        // sem produto vendido no período também não é "confiável" -- não há
        // nada pra calcular margem a partir de zero histórico.
        $margemConfiavel = $produtosVendidos > 0 && $coberturaCusto >= $coberturaMinima;

        $porMes = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.status', Order::STATUS_PAGO)
            ->whereBetween('orders.created_at', [$inicio, $fim])
            ->selectRaw(
                $this->mesExpressaoSql('orders.created_at').' as mes, '
                .'SUM(order_items.subtotal) as receita, '
                .'SUM(order_items.quantidade * COALESCE(products.custo, 0)) as cmv'
            )
            ->groupBy('mes')
            ->get()
            ->keyBy('mes');

        $periodo = CarbonPeriod::create(
            $inicio->copy()->startOfMonth(),
            '1 month',
            $fim->copy()->startOfMonth()
        );

        $serieMensal = collect($periodo)->map(function (Carbon $mes) use ($porMes, $margemConfiavel, $percentualCustoOperacionalEstimado) {
            $chave = $mes->format('Y-m');
            $linha = $porMes->get($chave);

            // rótulo obrigatório na UI: "Receita de produtos" (sem frete) --
            // mesma regra de rotulagem do ranking, esta chave nunca inclui frete.
            $receita = round((float) ($linha->receita ?? 0), 2);

            // sem cobertura de custo suficiente: NULL explícito, não 0 nem
            // "100%" -- a tela mostra o aviso de dado não confiável, nunca
            // um número que parece certo e não é.
            $cmv = $margemConfiavel ? round((float) ($linha->cmv ?? 0), 2) : null;
            $lucroBruto = $margemConfiavel ? round($receita - $cmv, 2) : null;

            $custoOperacionalEstimado = null;
            $lucroLiquidoEstimado = null;
            if ($margemConfiavel && $percentualCustoOperacionalEstimado !== null) {
                $custoOperacionalEstimado = round($receita * $percentualCustoOperacionalEstimado, 2);
                $lucroLiquidoEstimado = round($lucroBruto - $custoOperacionalEstimado, 2);
            }

            return [
                'mes' => $chave,
                'receita' => $receita,
                'cmv' => $cmv,
                'lucro_bruto' => $lucroBruto,
                // só preenchido se o chamador passou a estimativa explicitamente.
                'custo_operacional_estimado' => $custoOperacionalEstimado,
                'lucro_liquido_estimado' => $lucroLiquidoEstimado,
            ];
        })->values();

        return [
            'margem_confiavel' => $margemConfiavel,
            'cobertura_custo' => [
                'produtos_vendidos' => $produtosVendidos,
                'produtos_com_custo' => $produtosComCusto,
                'produtos_sem_custo' => $produtosSemCusto,
                'percentual' => $coberturaCusto,
                'minimo_exigido' => $coberturaMinima,
            ],
            'serie_mensal' => $serieMensal,
        ];
    }

    /**
     * Compartilhado entre vendasPorMes() e financeiro() -- os testes rodam
     * em MySQL de verdade (phpunit.xml), o mesmo motor de produção, então
     * não existe caminho alternativo aqui: um único DATE_FORMAT().
     */
    private function mesExpressaoSql(string $coluna): string
    {
        return "DATE_FORMAT({$coluna}, '%Y-%m')";
    }

    /**
     * Clientes que compraram no período, agrupados por e-mail -- NUNCA por
     * user_id. O checkout aceita convidado (orders.user_id nullable);
     * agrupar por user_id descartaria silenciosamente todo pedido sem
     * cadastro. Agrupar por e-mail também é o que junta corretamente o
     * mesmo cliente que comprou uma vez como convidado e outra logado.
     *
     * LOWER(email) normaliza maiúsculas/minúsculas -- Ana@x.com e ana@x.com
     * são a mesma pessoa e, sem isso, virariam duas linhas separadas.
     *
     * total_gasto usa orders.total (com frete, mesma convenção de "quanto o
     * cliente pagou" das demais métricas deste service).
     *
     * @return Collection<int, array{
     *   email: string, nome: string, pedidos: int, total_gasto: float,
     *   ticket_medio: float, ultimo_pedido: string, segmento: string
     * }>
     */
    public function clientes(Carbon $inicio, Carbon $fim): Collection
    {
        return Order::query()
            ->where('status', Order::STATUS_PAGO)
            ->whereBetween('created_at', [$inicio, $fim])
            ->selectRaw(
                'LOWER(email) as email, MAX(nome) as nome, COUNT(*) as pedidos, '
                .'SUM(total) as total_gasto, AVG(total) as ticket_medio, '
                .'MAX(created_at) as ultimo_pedido'
            )
            ->groupBy('email')
            ->orderByDesc('total_gasto')
            ->get()
            ->map(function ($linha) {
                $totalGasto = round((float) $linha->total_gasto, 2);

                return [
                    'email' => $linha->email,
                    'nome' => $linha->nome,
                    'pedidos' => (int) $linha->pedidos,
                    'total_gasto' => $totalGasto,
                    'ticket_medio' => round((float) $linha->ticket_medio, 2),
                    'ultimo_pedido' => $linha->ultimo_pedido,
                    'segmento' => $this->segmentoCliente($totalGasto),
                ];
            });
    }

    /**
     * Segmento a partir de total_gasto e dos limiares em
     * dashboard.clientes.segmentos (maior pro menor) -- data-driven em vez
     * de if/elseif encadeado, pra adicionar/ajustar segmento sem tocar
     * nesta lógica, e sem número mágico espalhado pelo código.
     */
    private function segmentoCliente(float $totalGasto): string
    {
        $segmentos = config('dashboard.clientes.segmentos');

        foreach ($segmentos as $nome => $limiar) {
            if ($totalGasto >= (float) $limiar) {
                return $nome;
            }
        }

        return array_key_last($segmentos);
    }
}
