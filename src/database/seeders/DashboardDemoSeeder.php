<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Gera pedidos ficticios dos ultimos 12 meses para validar o dashboard
 * administrativo ANTES de existir dado real de producao (orders = 0 hoje).
 *
 * O objetivo nao e dado bonito, e dado que quebra layout de proposito:
 * um mes vazio (buraco na serie temporal), pedidos de convidado sem
 * user_id, pedidos nao-pagos (pra pegar filtro de status esquecido),
 * produtos sem custo (margem que nao da pra calcular) e um produto que
 * nunca vendeu (nao pode aparecer em ranking de mais vendidos).
 *
 * So roda em ambiente local -- ver guarda no inicio de run().
 */
class DashboardDemoSeeder extends Seeder
{
    private const NUMERO_PREFIX = 'DEMO-';

    /**
     * ".test" é TLD reservado pela IANA especificamente para dado fictício
     * (RFC 2606) -- nunca resolve pra um domínio real, então a limpeza pode
     * casar por esse sufixo com certeza de nunca pegar a conta admin (ou
     * qualquer conta real) por engano.
     */
    private const DEMO_EMAIL_DOMAIN = '@exemplo.test';

    private const TOTAL_MESES = 12;

    /** Offset (meses atras, 0 = mes atual) que fica deliberadamente sem nenhum pedido. */
    private const MES_VAZIO_OFFSET = 6;

    private const PEDIDOS_POR_MES_MIN = 5;

    private const PEDIDOS_POR_MES_MAX = 12;

    private const PCT_CONVIDADO = 30;

    private const PCT_NAO_PAGO = 20;

    private const FRETE_GRATIS_A_PARTIR_DE = 150.0;

    private const FRETE_PADRAO = 19.90;

    private const QTD_CLIENTES_DEMO = 8;

    private const UFS = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS',
        'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC',
        'SP', 'SE', 'TO',
    ];

    public function run(): void
    {
        if (! app()->environment('local')) {
            throw new \RuntimeException(
                'DashboardDemoSeeder só pode rodar em ambiente local (APP_ENV=local). '
                .'Ambiente atual: '.app()->environment()
            );
        }

        $produtos = Product::orderBy('id')->get();

        if ($produtos->count() < 3) {
            $this->command->error('Precisa de pelo menos 3 produtos cadastrados para rodar este seeder.');

            return;
        }

        $this->limparExecucoesAnteriores();

        [$produtoSemVendaId, $semCustoIds] = $this->configurarProdutosDeTeste($produtos);
        $this->configurarEstoqueDeTeste($produtos);
        $produtosVendaveis = $produtos->reject(fn ($p) => $p->id === $produtoSemVendaId)->values();

        $clientesDemo = $this->criarClientesDemo();

        $mesVazio = null;
        $totalPedidos = 0;
        $totalPagos = 0;
        $totalCancelados = 0;
        $totalPendentes = 0;
        $totalConvidado = 0;

        for ($i = self::TOTAL_MESES - 1; $i >= 0; $i--) {
            $inicioMes = now()->subMonths($i)->startOfMonth();
            $fimMes = $inicioMes->copy()->endOfMonth();
            if ($fimMes->greaterThan(now())) {
                $fimMes = now()->copy();
            }

            if ($i === self::MES_VAZIO_OFFSET) {
                $mesVazio = $inicioMes->translatedFormat('F/Y');

                continue; // mes proposital sem nenhum pedido
            }

            $qtdPedidos = rand(self::PEDIDOS_POR_MES_MIN, self::PEDIDOS_POR_MES_MAX);

            for ($p = 0; $p < $qtdPedidos; $p++) {
                $criadoEm = Carbon::createFromTimestamp(
                    rand($inicioMes->timestamp, $fimMes->timestamp)
                );

                $ehConvidado = fake()->boolean(self::PCT_CONVIDADO);
                $naoPago = fake()->boolean(self::PCT_NAO_PAGO);
                $status = $naoPago
                    ? fake()->randomElement([Order::STATUS_CANCELADO, Order::STATUS_PENDENTE])
                    : Order::STATUS_PAGO;

                $cliente = $ehConvidado ? null : $clientesDemo->random();

                $this->criarPedido($produtosVendaveis, $criadoEm, $status, $cliente);

                $totalPedidos++;
                if ($status === Order::STATUS_PAGO) {
                    $totalPagos++;
                } elseif ($status === Order::STATUS_CANCELADO) {
                    $totalCancelados++;
                } else {
                    $totalPendentes++;
                }
                if ($ehConvidado) {
                    $totalConvidado++;
                }
            }
        }

        $produtoSemVenda = $produtos->firstWhere('id', $produtoSemVendaId);
        $semCustoNomes = $produtos->whereIn('id', $semCustoIds)->pluck('nome')->all();

        $this->command->info('--- DashboardDemoSeeder ---');
        $this->command->info("Pedidos gerados: {$totalPedidos}");
        $this->command->info("  Pagos: {$totalPagos}");
        $this->command->info("  Cancelados: {$totalCancelados}");
        $this->command->info("  Pendentes: {$totalPendentes}");
        $this->command->info("  Convidado (user_id null): {$totalConvidado}");
        $this->command->info("Mês vazio de propósito: {$mesVazio}");
        $this->command->info("Produto sem nenhuma venda: {$produtoSemVenda?->nome} (id {$produtoSemVendaId})");
        $this->command->info('Produtos sem custo: '.implode(', ', $semCustoNomes));
        $this->command->info('Clientes demo criados: '.$clientesDemo->count());
    }

    /**
     * Idempotente: apaga pedidos DEMO- e os clientes fictícios de execuções
     * anteriores antes de gerar dado novo, pra rodar o seeder várias vezes
     * sem acumular lixo (nem pedido duplicado, nem cliente fictício extra).
     *
     * ORDEM IMPORTA: orders.user_id é nullOnDelete. Se os usuários fossem
     * apagados antes dos pedidos, o pedido não seria removido -- só teria
     * o user_id zerado silenciosamente, virando um "pedido de convidado"
     * fantasma e corrompendo a métrica de clientes sem gerar erro nenhum.
     * Por isso: order_items -> orders -> só então os usuários.
     */
    private function limparExecucoesAnteriores(): void
    {
        $idsAntigos = Order::where('numero_pedido', 'like', self::NUMERO_PREFIX.'%')->pluck('id');
        OrderItem::whereIn('order_id', $idsAntigos)->delete();
        Order::whereIn('id', $idsAntigos)->delete();

        User::where('email', 'like', '%'.self::DEMO_EMAIL_DOMAIN)->delete();
    }

    /**
     * Escolhe 1 produto pra nunca vender e 2 pra ficar sem custo definido;
     * define um custo plausivel (35%-65% de margem) pros demais.
     *
     * @return array{0: int, 1: array<int>}
     */
    private function configurarProdutosDeTeste($produtos): array
    {
        $produtoSemVendaId = $produtos->first()->id;
        $semCustoIds = $produtos->slice(1, 2)->pluck('id')->all();

        foreach ($produtos as $produto) {
            if (in_array($produto->id, $semCustoIds, true)) {
                $produto->update(['custo' => null]);

                continue;
            }

            $margem = fake()->randomFloat(2, 0.35, 0.65);
            $produto->update(['custo' => round($produto->preco * (1 - $margem), 2)]);
        }

        return [$produtoSemVendaId, $semCustoIds];
    }

    /**
     * Espalha estoque/estoque_minimo pelos 4 status possíveis, pra dar dado
     * de verdade pra validar DashboardService::estoque(): alguns produtos
     * ficam com estoque_minimo=0 de propósito (cenário "acabou de migrar,
     * ninguém configurou ainda" -- tem que virar NAO_CONFIGURADO, não Repor).
     */
    private function configurarEstoqueDeTeste($produtos): void
    {
        $qtdNaoConfigurados = min(3, $produtos->count());

        foreach ($produtos->values() as $index => $produto) {
            if ($index < $qtdNaoConfigurados) {
                $produto->update(['estoque' => rand(0, 20), 'estoque_minimo' => 0]);

                continue;
            }

            $estoqueMinimo = rand(5, 20);
            $faixa = ($index - $qtdNaoConfigurados) % 3;
            $estoque = match ($faixa) {
                0 => rand(0, $estoqueMinimo), // Repor
                1 => rand($estoqueMinimo + 1, (int) floor($estoqueMinimo * 1.5)), // Atenção
                default => rand((int) ceil($estoqueMinimo * 1.6), $estoqueMinimo * 4), // OK
            };

            $produto->update(['estoque' => $estoque, 'estoque_minimo' => $estoqueMinimo]);
        }
    }

    /**
     * Cria os clientes fictícios usados nos pedidos "não-convidado" --
     * necessários porque orders.user_id exige um usuário real quando não é
     * convidado, e o banco local só tem a conta admin hoje. Sempre roda
     * depois de limparExecucoesAnteriores(), então não precisa se preocupar
     * em reaproveitar: o slate já está limpo neste ponto.
     */
    private function criarClientesDemo()
    {
        return collect(range(1, self::QTD_CLIENTES_DEMO))->map(function ($i) {
            return User::create([
                'name' => fake()->firstName(),
                'sobrenome' => fake()->lastName(),
                'email' => "cliente.demo{$i}".self::DEMO_EMAIL_DOMAIN,
                'telefone' => fake()->numerify('(##) 9####-####'),
                'password' => bcrypt(Str::random(32)),
            ]);
        });
    }

    private function criarPedido($produtosVendaveis, Carbon $criadoEm, string $status, ?User $cliente): void
    {
        $itensQtd = rand(1, 3);
        $produtosDoPedido = $produtosVendaveis->random(min($itensQtd, $produtosVendaveis->count()));

        $subtotal = 0;
        $itens = [];
        foreach ($produtosDoPedido as $produto) {
            $qty = rand(1, 2);
            $itemSubtotal = round($produto->preco * $qty, 2);
            $subtotal += $itemSubtotal;
            $itens[] = [
                'product_id' => $produto->id,
                'nome' => $produto->nome,
                'preco' => $produto->preco,
                'cor' => null,
                'tamanho' => fake()->randomElement(['PP', 'P', 'M', 'G', 'GG']),
                'quantidade' => $qty,
                'subtotal' => $itemSubtotal,
            ];
        }

        $frete = $subtotal >= self::FRETE_GRATIS_A_PARTIR_DE ? 0 : self::FRETE_PADRAO;
        $total = $subtotal + $frete;

        do {
            $numeroPedido = self::NUMERO_PREFIX.strtoupper(Str::random(8));
        } while (Order::where('numero_pedido', $numeroPedido)->exists());

        $nome = $cliente->name ?? fake()->firstName();
        $sobrenome = $cliente->sobrenome ?? fake()->lastName();
        $email = $cliente->email ?? fake()->unique()->safeEmail();

        $order = Order::create([
            'user_id' => $cliente?->id,
            'numero_pedido' => $numeroPedido,
            'nome' => $nome,
            'sobrenome' => $sobrenome,
            'email' => $email,
            'telefone' => $cliente->telefone ?? fake()->numerify('(##) 9####-####'),
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $total,
            'cupom' => null,
            'forma_pagamento' => fake()->randomElement([
                Order::PAGAMENTO_CARTAO, Order::PAGAMENTO_PIX, Order::PAGAMENTO_BOLETO,
            ]),
            'status' => $status,
            'endereco_entrega' => [
                'cep' => fake()->numerify('#####-###'),
                'rua' => fake()->streetName(),
                'numero' => (string) fake()->buildingNumber(),
                'complemento' => null,
                'bairro' => fake()->citySuffix(),
                'cidade' => fake()->city(),
                'uf' => fake()->randomElement(self::UFS),
                'referencia' => null,
            ],
            'endereco_faturamento' => null,
        ]);

        // create() sempre grava timestamp "agora" -- forca a data espalhada
        // pelo mes via atribuicao direta (nao passa por $fillable, entao
        // updateTimestamps() nao sobrescreve por estar "dirty").
        $order->created_at = $criadoEm;
        $order->updated_at = $criadoEm;
        $order->save();

        foreach ($itens as $item) {
            $orderItem = $order->items()->create($item);
            $orderItem->created_at = $criadoEm;
            $orderItem->updated_at = $criadoEm;
            $orderItem->save();
        }
    }
}
