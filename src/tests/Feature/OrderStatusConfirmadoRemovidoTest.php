<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 'confirmado' foi removido de Order::STATUSES por ser um rascunho sem uso
 * definido: nada no fluxo real escrevia esse valor, só existia como opção
 * manual no dropdown do admin, mas o DashboardService/HomeController (que
 * filtram só status='pago' pra receita/ranking) tratavam um pedido
 * "confirmado" como se nunca tivesse sido pago -- a receita CAÍA quando o
 * admin confirmava o pedido.
 */
class OrderStatusConfirmadoRemovidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_rejeita_status_confirmado_e_nao_altera_o_pedido(): void
    {
        $admin = $this->criarAdmin();
        $produto = $this->criarProduto();
        $pedido = $this->criarPedido($produto, Order::STATUS_PENDENTE);

        $response = $this->actingAs($admin)->patch(route('admin.pedidos.status', $pedido), [
            'status' => 'confirmado',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertEquals(Order::STATUS_PENDENTE, $pedido->fresh()->status);
    }

    public function test_admin_continua_aceitando_os_tres_status_validos(): void
    {
        $admin = $this->criarAdmin();
        $produto = $this->criarProduto();
        $pedido = $this->criarPedido($produto, Order::STATUS_PENDENTE);

        foreach (Order::STATUSES as $status) {
            $this->actingAs($admin)
                ->patch(route('admin.pedidos.status', $pedido), ['status' => $status])
                ->assertSessionDoesntHaveErrors();

            $this->assertEquals($status, $pedido->fresh()->status);
        }
    }

    public function test_migration_normaliza_pedido_confirmado_pre_existente_para_pago(): void
    {
        $produto = $this->criarProduto();

        // Inserido direto com a string literal 'confirmado' -- a constante
        // não existe mais, mas o dado legado pode existir (coluna é string
        // livre, não enum).
        $pedidoConfirmado = $this->criarPedido($produto, 'confirmado');
        $pedidoJaPago = $this->criarPedido($produto, Order::STATUS_PAGO);
        $pedidoPendente = $this->criarPedido($produto, Order::STATUS_PENDENTE);

        $migration = require database_path('migrations/2026_09_12_120000_normalize_status_confirmado_para_pago.php');
        $migration->up();

        $this->assertEquals(Order::STATUS_PAGO, $pedidoConfirmado->fresh()->status);
        $this->assertEquals(Order::STATUS_PAGO, $pedidoJaPago->fresh()->status);
        $this->assertEquals(Order::STATUS_PENDENTE, $pedidoPendente->fresh()->status);
    }

    private function criarAdmin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    private function criarProduto(): Product
    {
        return Product::create([
            'nome' => 'Produto Teste',
            'slug' => 'produto-teste-'.uniqid(),
            'descricao' => null,
            'preco' => 100.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);
    }

    private function criarPedido(Product $produto, string $status): Order
    {
        $order = Order::create([
            'user_id' => null,
            'numero_pedido' => 'TEST-'.strtoupper(uniqid()),
            'nome' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'subtotal' => 100.00,
            'frete' => 19.90,
            'total' => 119.90,
            'cupom' => null,
            'forma_pagamento' => Order::PAGAMENTO_PIX,
            'status' => $status,
            'endereco_entrega' => [
                'cep' => '00000-000', 'rua' => 'Rua Teste', 'numero' => '1',
                'complemento' => null, 'bairro' => 'Centro', 'cidade' => 'Cidade Teste',
                'uf' => 'SP', 'referencia' => null,
            ],
            'endereco_faturamento' => null,
        ]);

        $order->items()->create([
            'product_id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => $produto->preco,
            'cor' => null,
            'tamanho' => null,
            'quantidade' => 1,
            'subtotal' => $produto->preco,
        ]);

        return $order;
    }
}
