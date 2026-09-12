<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Cobre duas correções de apresentação do gráfico "Vendas por dia/mês" e do
 * bloco Financeiro no dashboard (/admin) -- nenhuma mexe no DashboardService,
 * são só decisões de PHP sobre o que a view recebe e renderiza.
 *
 * A) xaxis.type explícito ('datetime' pra granularidade diária, 'category'
 *    pra mensal) em vez de deixar o ApexCharts auto-detectar -- e série
 *    inteira zerada não desenha eixo Y fingindo escala, mostra mensagem.
 * B) "0 de 0 produtos com custo" não é alerta de dado em falta, é ausência
 *    de venda -- mensagem neutra, sem alert-danger.
 */
class DashboardVendasChartTest extends TestCase
{
    use RefreshDatabase;

    private const FRETE = 19.90;

    public function test_serie_nao_tem_rotulo_duplicado_nos_4_periodos_do_filtro(): void
    {
        $admin = $this->criarAdmin();
        $produto = $this->criarProduto();

        // Pedidos espalhados o suficiente pra ter dado em qualquer uma das
        // janelas testadas (7/30 dias caem em granularidade diária, 90/365
        // em mensal -- diffInDays <= 60 decide isso no controller).
        foreach ([1, 10, 50, 100, 200, 380] as $diasAtras) {
            $this->criarPedidoPago($produto, Carbon::now()->subDays($diasAtras), qty: 2);
        }

        foreach ([7, 30, 90, 365] as $dias) {
            $inicio = Carbon::now()->subDays($dias)->toDateString();
            $fim = Carbon::now()->toDateString();

            $response = $this->actingAs($admin)->get("/admin?periodo=custom&inicio={$inicio}&fim={$fim}");
            $response->assertOk();

            $granularidade = $response->viewData('vendasGranularidade');

            if ($granularidade === 'dia') {
                $timestamps = collect($response->viewData('vendasSerie'))->map(fn ($par) => $par[0]);
                $this->assertEquals(
                    $timestamps->count(),
                    $timestamps->unique()->count(),
                    "timestamp duplicado na série diária do período de {$dias} dias"
                );
            } else {
                $categorias = $response->viewData('vendasCategorias');
                $this->assertEquals(
                    $categorias->count(),
                    $categorias->unique()->count(),
                    "categoria 'mm/aaaa' duplicada na série mensal do período de {$dias} dias"
                );
            }
        }
    }

    public function test_serie_toda_zerada_nao_renderiza_grafico_e_mostra_mensagem_neutra(): void
    {
        $admin = $this->criarAdmin();
        $this->criarProduto(); // catálogo existe, mas nenhum pedido pago

        $response = $this->actingAs($admin)->get('/admin?periodo=30d');
        $response->assertOk();

        $this->assertTrue($response->viewData('graficoVendasVazio'));

        $html = $response->getContent();
        $this->assertStringNotContainsString('id="grafico-vendas"', $html);
        $this->assertStringNotContainsString('apexcharts', $html);
        $this->assertStringContainsString('Sem vendas no período.', $html);
    }

    public function test_financeiro_mostra_mensagem_neutra_quando_nao_ha_venda_no_periodo(): void
    {
        $admin = $this->criarAdmin();
        $this->criarProduto(); // sem custo, sem venda

        $response = $this->actingAs($admin)->get('/admin?periodo=30d');
        $response->assertOk();

        $this->assertSame(0, $response->viewData('financeiro')['cobertura_custo']['produtos_vendidos']);

        $html = $response->getContent();
        $this->assertStringContainsString('Sem vendas no período.', $html);
        $this->assertStringNotContainsString('Cadastre o custo dos produtos em falta', $html);
        $this->assertStringNotContainsString('alert-danger', $html);
    }

    public function test_financeiro_mantem_alerta_vermelho_quando_cobertura_de_custo_e_insuficiente(): void
    {
        $admin = $this->criarAdmin();

        $comCusto = $this->criarProduto(custo: 30.00);
        $semCusto1 = $this->criarProduto(custo: null);
        $semCusto2 = $this->criarProduto(custo: null);
        $semCusto3 = $this->criarProduto(custo: null);

        // 1 de 4 produtos vendidos com custo = 25% de cobertura, bem abaixo
        // do mínimo default (80%) -- margem_confiavel continua false, e
        // agora precisa continuar vermelho (só o caso "0 vendas" virou neutro).
        foreach ([$comCusto, $semCusto1, $semCusto2, $semCusto3] as $produto) {
            $this->criarPedidoPago($produto, Carbon::now()->subDays(1), qty: 1);
        }

        $response = $this->actingAs($admin)->get('/admin?periodo=30d');
        $response->assertOk();

        $financeiro = $response->viewData('financeiro');
        $this->assertGreaterThan(0, $financeiro['cobertura_custo']['produtos_vendidos']);
        $this->assertFalse($financeiro['margem_confiavel']);

        $html = $response->getContent();
        $this->assertStringContainsString('alert-danger', $html);
        $this->assertStringContainsString('Margem não confiável', $html);
        $this->assertStringNotContainsString('Sem vendas no período.', $html);
    }

    private function criarAdmin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    private function criarProduto(?float $custo = null): Product
    {
        return Product::create([
            'nome' => 'Produto Teste',
            'slug' => 'produto-teste-'.uniqid(),
            'descricao' => null,
            'preco' => 100.00,
            'custo' => $custo,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);
    }

    private function criarPedidoPago(Product $produto, Carbon $criadoEm, int $qty): Order
    {
        $subtotal = round($produto->preco * $qty, 2);

        $order = Order::create([
            'user_id' => null,
            'numero_pedido' => 'TEST-'.strtoupper(uniqid()),
            'nome' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'subtotal' => $subtotal,
            'frete' => self::FRETE,
            'total' => $subtotal + self::FRETE,
            'cupom' => null,
            'forma_pagamento' => Order::PAGAMENTO_PIX,
            'status' => Order::STATUS_PAGO,
            'endereco_entrega' => [
                'cep' => '00000-000', 'rua' => 'Rua Teste', 'numero' => '1',
                'complemento' => null, 'bairro' => 'Centro', 'cidade' => 'Cidade Teste',
                'uf' => 'SP', 'referencia' => null,
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
