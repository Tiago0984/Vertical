<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Fase 3 (dashboard): orders.desconto e orders.frete dos pedidos pagos
 * sustentam a linha de conciliação do card Financeiro --
 *
 *   receita de produtos (sem frete) - descontos concedidos + frete = faturamento
 *
 * -- e "Desempenho por cupom" agrupa orders.cupom dos pedidos pagos do
 * período. Sem isso, o primeiro pedido com desconto faz a receita de
 * produtos (bruta) ficar maior que o faturamento sem nada na tela
 * explicando por quê.
 */
class DashboardCupomTest extends TestCase
{
    use RefreshDatabase;

    private const FRETE = 19.90;

    public function test_desconto_total_pago_soma_so_pedidos_pagos_dentro_do_periodo(): void
    {
        $service = new DashboardService();
        $produto = $this->criarProduto();

        $this->criarPedido($produto, Carbon::create(2025, 1, 10), Order::STATUS_PAGO, desconto: 20.00, cupom: 'DEZ');
        $this->criarPedido($produto, Carbon::create(2025, 1, 12), Order::STATUS_CANCELADO, desconto: 999.00, cupom: 'DEZ');
        $this->criarPedido($produto, Carbon::create(2025, 1, 14), Order::STATUS_PENDENTE, desconto: 999.00, cupom: 'DEZ');
        $this->criarPedido($produto, Carbon::create(2024, 12, 31), Order::STATUS_PAGO, desconto: 999.00, cupom: 'DEZ');

        $inicio = Carbon::create(2025, 1, 1)->startOfDay();
        $fim = Carbon::create(2025, 1, 31)->endOfDay();

        $resumo = $service->resumo($inicio, $fim);

        $this->assertEquals(20.00, $resumo['desconto_total_pago']);
    }

    public function test_conciliacao_financeira_fecha_com_pedidos_com_e_sem_desconto(): void
    {
        $service = new DashboardService();
        $produto = $this->criarProduto();

        $this->criarPedido($produto, Carbon::create(2025, 1, 10), Order::STATUS_PAGO, qty: 1, desconto: 0.0);
        $this->criarPedido($produto, Carbon::create(2025, 1, 15), Order::STATUS_PAGO, qty: 2, desconto: 20.00, cupom: 'DEZ');

        $inicio = Carbon::create(2025, 1, 1)->startOfDay();
        $fim = Carbon::create(2025, 1, 31)->endOfDay();

        $resumo = $service->resumo($inicio, $fim);
        $ranking = $service->rankingProdutos($inicio, $fim);
        $receitaProdutos = round($ranking->sum('receita'), 2);

        $this->assertEquals(
            $resumo['faturamento'],
            round($receitaProdutos - $resumo['desconto_total_pago'] + $resumo['frete_total_pago'], 2),
            'receita de produtos - descontos concedidos + frete deve fechar exatamente com faturamento'
        );

        // subtotais: 100 + 200 = 300; desconto: 20; frete: 19.90 * 2 pedidos pagos = 39.80
        $this->assertEquals(300.00, $receitaProdutos);
        $this->assertEquals(20.00, $resumo['desconto_total_pago']);
        $this->assertEquals(39.80, $resumo['frete_total_pago']);
        $this->assertEquals(319.80, $resumo['faturamento']);
    }

    public function test_desempenho_por_cupom_agrupa_ignora_pedido_nao_pago_e_pedido_sem_cupom(): void
    {
        $service = new DashboardService();
        $produto = $this->criarProduto();

        $this->criarPedido($produto, Carbon::create(2025, 1, 10), Order::STATUS_PAGO, desconto: 10.00, cupom: 'DEZ');
        $this->criarPedido($produto, Carbon::create(2025, 1, 12), Order::STATUS_PAGO, desconto: 10.00, cupom: 'DEZ');
        $this->criarPedido($produto, Carbon::create(2025, 1, 14), Order::STATUS_PAGO, desconto: 0.0, cupom: null);
        $this->criarPedido($produto, Carbon::create(2025, 1, 16), Order::STATUS_CANCELADO, qty: 5, desconto: 999.00, cupom: 'DEZ');

        $inicio = Carbon::create(2025, 1, 1)->startOfDay();
        $fim = Carbon::create(2025, 1, 31)->endOfDay();

        $desempenho = $service->desempenhoPorCupom($inicio, $fim);

        $this->assertCount(1, $desempenho, 'só o cupom DEZ deve aparecer -- pedido sem cupom e pedido cancelado ficam de fora');
        $this->assertSame('DEZ', $desempenho->first()['codigo']);
        $this->assertSame(2, $desempenho->first()['pedidos']);
        $this->assertEquals(20.00, $desempenho->first()['desconto_total']);
    }

    public function test_periodo_sem_cupom_usado_mostra_mensagem_em_vez_de_tabela_vazia(): void
    {
        $admin = $this->admin();
        $produto = $this->criarProduto();
        $this->criarPedido($produto, Carbon::now(), Order::STATUS_PAGO, desconto: 0.0, cupom: null);

        $response = $this->actingAs($admin)->get('/admin?periodo=30d');

        $response->assertOk();
        $response->assertSee('Nenhum cupom usado no período.');
    }

    /**
     * Zeros numa identidade contábil parecem cálculo conferido, e não são --
     * sem pedido pago no período, a conciliação some junto com a tabela de
     * CMV/lucro (mesma condição: produtos_vendidos === 0), e só a mensagem
     * "Sem vendas no período." aparece.
     */
    public function test_periodo_sem_venda_paga_nao_renderiza_conciliacao_mostra_mensagem(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get('/admin?periodo=30d');

        $response->assertOk();
        $response->assertSee('Sem vendas no período.');
        $response->assertDontSee('Receita de produtos (sem frete) R$', false);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    private function criarProduto(): Product
    {
        return Product::create([
            'nome' => 'Produto Teste Dashboard Cupom',
            'slug' => 'produto-teste-dashboard-cupom-'.uniqid(),
            'descricao' => null,
            'preco' => 100.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);
    }

    private function criarPedido(
        Product $produto,
        Carbon $criadoEm,
        string $status,
        int $qty = 1,
        float $desconto = 0.0,
        ?string $cupom = null
    ): Order {
        $subtotal = round($produto->preco * $qty, 2);
        $frete = $status === Order::STATUS_PAGO ? self::FRETE : 0;
        $total = $subtotal - $desconto + $frete;

        $order = Order::create([
            'user_id' => null,
            'numero_pedido' => 'TEST-'.strtoupper(uniqid()),
            'nome' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'frete' => $frete,
            'total' => $total,
            'cupom' => $cupom,
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
