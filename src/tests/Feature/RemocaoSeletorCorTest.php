<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cor é atributo do PRODUTO, não variação escolhida no checkout -- cada cor
 * já é um item próprio do catálogo. O seletor de cor da página do produto
 * permitia pedir uma cor que o produto específico não é; foi removido.
 * Pedido novo grava order_items.cor = null sempre; pedido antigo (com cor
 * de verdade gravada) continua exibindo o que foi vendido.
 */
class RemocaoSeletorCorTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_do_produto_nao_renderiza_seletor_de_cor(): void
    {
        $produto = $this->criarProduto();

        $response = $this->get(route('produto', $produto->slug));

        $response->assertOk();
        $response->assertDontSee('color-dot', false);
        $response->assertDontSee('cor-selecionada', false);
        $response->assertDontSee('selecionarCor', false);
    }

    public function test_finalizar_sem_cor_grava_order_items_cor_null(): void
    {
        $produto = $this->criarProduto(100.00);

        // '' é o que o carrinho manda hoje quando nenhuma cor foi escolhida
        // (cart.js usa string vazia como sentinela de "sem variação" na
        // chave única de cart_items) -- não é o mesmo que omitir o campo.
        $response = $this->postJson(route('checkout.finalizar'), $this->payloadFinalizar($produto, ''));
        $response->assertOk();

        $pedido = Order::where('numero_pedido', $response->json('numero_pedido'))->firstOrFail();
        $item = $pedido->items()->firstOrFail();

        $this->assertNull($item->cor, 'string vazia enviada pelo carrinho deve virar null em order_items.cor');
    }

    public function test_finalizar_com_cor_omitida_tambem_grava_null(): void
    {
        $produto = $this->criarProduto(100.00);

        $response = $this->postJson(route('checkout.finalizar'), $this->payloadFinalizar($produto, null));
        $response->assertOk();

        $pedido = Order::where('numero_pedido', $response->json('numero_pedido'))->firstOrFail();
        $this->assertNull($pedido->items()->firstOrFail()->cor);
    }

    public function test_pedido_show_admin_nao_mostra_coluna_cor_quando_nenhum_item_tem_cor(): void
    {
        $admin = $this->admin();
        $pedido = $this->criarPedidoComItem(cor: null);

        $response = $this->actingAs($admin)->get(route('admin.pedidos.show', $pedido));

        $response->assertOk();
        $response->assertDontSee('<th>Cor</th>', false);
    }

    public function test_pedido_show_admin_mostra_coluna_cor_para_pedido_antigo_com_cor(): void
    {
        $admin = $this->admin();
        // Simula um pedido de antes da remoção do seletor: cor gravada de verdade.
        $pedido = $this->criarPedidoComItem(cor: 'Azul');

        $response = $this->actingAs($admin)->get(route('admin.pedidos.show', $pedido));

        $response->assertOk();
        $response->assertSee('<th>Cor</th>', false);
        $response->assertSee('Azul');
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    private function criarProduto(float $preco = 100.00): Product
    {
        return Product::create([
            'nome' => 'Camiseta Teste',
            'slug' => 'camiseta-teste-'.uniqid(),
            'descricao' => null,
            'preco' => $preco,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);
    }

    private function criarPedidoComItem(?string $cor): Order
    {
        $produto = $this->criarProduto();

        $order = Order::create([
            'user_id' => null,
            'numero_pedido' => 'TEST-'.strtoupper(uniqid()),
            'nome' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'subtotal' => $produto->preco,
            'desconto' => 0,
            'frete' => 0,
            'total' => $produto->preco,
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

        $order->items()->create([
            'product_id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => $produto->preco,
            'cor' => $cor,
            'tamanho' => 'M',
            'quantidade' => 1,
            'subtotal' => $produto->preco,
        ]);

        return $order;
    }

    private function payloadFinalizar(Product $produto, ?string $cor): array
    {
        return [
            'nome' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'forma_pagamento' => 'pix',
            'endereco_entrega' => [
                'cep' => '00000-000', 'rua' => 'Rua Teste', 'numero' => '1',
                'complemento' => null, 'bairro' => 'Centro', 'cidade' => 'Cidade Teste',
                'uf' => 'SP', 'referencia' => null,
            ],
            'items' => [
                ['id' => $produto->id, 'cor' => $cor, 'tamanho' => 'M', 'qty' => 1],
            ],
        ];
    }
}
