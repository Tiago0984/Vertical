<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Transforma em teste automático a reconciliação que foi provada na mão
 * durante o desenvolvimento do DashboardService:
 *
 *   SUM(receita do ranking) + SUM(frete dos pedidos pagos) = faturamento do resumo()
 *
 * Esse invariante quebra no dia em que orders.cupom passar a descontar de
 * verdade sem existir uma coluna orders.desconto (ver comentário no
 * CheckoutController) -- o comentário depende de alguém ler antes de mexer;
 * este teste não depende de ninguém, é o canário automático do gap.
 *
 * Cenário fixo dentro do próprio teste (não usa DashboardDemoSeeder, que é
 * aleatório -- um teste não pode passar ou falhar por sorte de seed).
 */
class DashboardConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private const FRETE = 19.90;

    public function test_ranking_mais_frete_fecha_com_faturamento_do_resumo(): void
    {
        $service = new DashboardService();
        $produto = $this->criarProduto();

        // Janeiro: 2 pagos + 1 cancelado + 1 pendente (os 2 últimos não podem
        // entrar em nenhuma soma). Fevereiro: nada, de propósito. Março: 1 pago.
        $this->criarPedido($produto, Carbon::create(2025, 1, 10), Order::STATUS_PAGO, qty: 2);        // subtotal 200,00
        $this->criarPedido($produto, Carbon::create(2025, 1, 20), Order::STATUS_PAGO, qty: 1);        // subtotal 100,00
        $this->criarPedido($produto, Carbon::create(2025, 1, 15), Order::STATUS_CANCELADO, qty: 5);   // subtotal 500,00 -- não deve contar
        $this->criarPedido($produto, Carbon::create(2025, 1, 25), Order::STATUS_PENDENTE, qty: 3);    // subtotal 300,00 -- não deve contar
        $this->criarPedido($produto, Carbon::create(2025, 3, 5), Order::STATUS_PAGO, qty: 1);         // subtotal 100,00

        $inicio = Carbon::create(2025, 1, 1)->startOfDay();
        $fim = Carbon::create(2025, 3, 31)->endOfDay();

        $resumo = $service->resumo($inicio, $fim);
        $ranking = $service->rankingProdutos($inicio, $fim);
        $freteTotalPago = Order::where('status', Order::STATUS_PAGO)
            ->whereBetween('created_at', [$inicio, $fim])
            ->sum('frete');

        $somaReceita = round($ranking->sum('receita'), 2);

        $this->assertEquals(
            $resumo['faturamento'],
            round($somaReceita + (float) $freteTotalPago, 2),
            'ranking.receita + frete dos pedidos pagos deve fechar exatamente com resumo.faturamento'
        );

        // faturamento esperado: só os 3 pedidos pagos (219.90 + 119.90 + 119.90)
        $this->assertEquals(459.70, $resumo['faturamento']);
        $this->assertEquals(400.00, $somaReceita);
        $this->assertEquals(59.70, round((float) $freteTotalPago, 2));
    }

    public function test_pedidos_cancelado_e_pendente_nao_entram_em_nenhuma_soma(): void
    {
        $service = new DashboardService();
        $produto = $this->criarProduto();

        $this->criarPedido($produto, Carbon::create(2025, 1, 10), Order::STATUS_PAGO, qty: 1);       // subtotal 100,00 -- única linha que deve contar
        $this->criarPedido($produto, Carbon::create(2025, 1, 12), Order::STATUS_CANCELADO, qty: 50); // valor gigante de propósito
        $this->criarPedido($produto, Carbon::create(2025, 1, 14), Order::STATUS_PENDENTE, qty: 50);  // valor gigante de propósito

        $inicio = Carbon::create(2025, 1, 1)->startOfDay();
        $fim = Carbon::create(2025, 1, 31)->endOfDay();

        $resumo = $service->resumo($inicio, $fim);
        $ranking = $service->rankingProdutos($inicio, $fim);

        $this->assertEquals(3, $resumo['total_pedidos']);
        $this->assertEquals(1, $resumo['pedidos_pagos']);
        $this->assertEquals(1, $resumo['pedidos_cancelados']);
        $this->assertEquals(1, $resumo['pedidos_pendentes']);

        // se cancelado/pendente vazassem pra soma, isso passaria de 119.90 pra
        // algo na casa de 5000+ (50 unidades a R$100) -- a asserção exata pega isso.
        $this->assertEquals(119.90, $resumo['faturamento']);
        $this->assertEquals(1, $ranking->count());
        $this->assertEquals(100.00, $ranking->first()['receita']);
        $this->assertEquals(1, $ranking->first()['qtd_vendida']);
    }

    public function test_mes_sem_venda_aparece_com_faturamento_zero_em_vez_de_sumir(): void
    {
        $service = new DashboardService();
        $produto = $this->criarProduto();

        $this->criarPedido($produto, Carbon::create(2025, 1, 10), Order::STATUS_PAGO, qty: 1);
        // fevereiro: nada de propósito
        $this->criarPedido($produto, Carbon::create(2025, 3, 5), Order::STATUS_PAGO, qty: 1);

        $inicio = Carbon::create(2025, 1, 1)->startOfDay();
        $fim = Carbon::create(2025, 3, 31)->endOfDay();

        $porMes = $service->vendasPorMes($inicio, $fim)->keyBy('mes');

        $this->assertCount(3, $porMes, 'os 3 meses do período devem aparecer, mesmo o vazio');
        $this->assertTrue($porMes->has('2025-02'), 'fevereiro não pode sumir do array só por não ter venda');
        $this->assertEquals(0.0, $porMes->get('2025-02')['faturamento']);
        $this->assertEquals(0, $porMes->get('2025-02')['pedidos']);

        $this->assertEquals(119.90, $porMes->get('2025-01')['faturamento']);
        $this->assertEquals(119.90, $porMes->get('2025-03')['faturamento']);
    }

    private function criarProduto(): Product
    {
        return Product::create([
            'nome' => 'Produto Teste Dashboard',
            'slug' => 'produto-teste-dashboard-'.uniqid(),
            'descricao' => null,
            'preco' => 100.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);
    }

    private function criarPedido(Product $produto, Carbon $criadoEm, string $status, int $qty): Order
    {
        $subtotal = round($produto->preco * $qty, 2);
        $frete = $status === Order::STATUS_PAGO ? self::FRETE : 0;
        $total = $subtotal + $frete;

        $order = Order::create([
            'user_id' => null,
            'numero_pedido' => 'TEST-'.strtoupper(uniqid()),
            'nome' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $total,
            'cupom' => null,
            'forma_pagamento' => Order::PAGAMENTO_PIX,
            'status' => $status,
            'endereco_entrega' => [
                'cep' => '00000-000',
                'rua' => 'Rua Teste',
                'numero' => '1',
                'complemento' => null,
                'bairro' => 'Centro',
                'cidade' => 'Cidade Teste',
                'uf' => 'SP',
                'referencia' => null,
            ],
            'endereco_faturamento' => null,
        ]);

        $order->created_at = $criadoEm;
        $order->updated_at = $criadoEm;
        $order->save();

        $item = $order->items()->create([
            'product_id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => $produto->preco,
            'cor' => null,
            'tamanho' => null,
            'quantidade' => $qty,
            'subtotal' => $subtotal,
        ]);
        $item->created_at = $criadoEm;
        $item->updated_at = $criadoEm;
        $item->save();

        return $order;
    }
}
