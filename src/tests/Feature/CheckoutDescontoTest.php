<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 1: orders.desconto existe e o CheckoutController calcula
 * total = subtotal - desconto + frete -- sem campo de cupom no checkout
 * ainda (fase 2b), desconto sai sempre 0.00.
 *
 * Fase 2a: o cálculo passou para PedidoCalculoService (ver
 * PedidoCalculoServiceTest para os testes de desconto de verdade,
 * percentual/fixo e a regra do frete grátis preservado). Aqui ficam as
 * garantias de fiação do controller: desconto vindo do cliente é ignorado,
 * e um pedido já criado com cupom não muda se o cupom for editado depois.
 */
class CheckoutDescontoTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_grava_desconto_zero_e_total_fecha_com_subtotal_menos_desconto_mais_frete(): void
    {
        $produto = Product::create([
            'nome' => 'Produto Teste',
            'slug' => 'produto-teste-'.uniqid(),
            'descricao' => null,
            'preco' => 80.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);

        // subtotal 80,00 < 150 -- frete pago (19,90).
        $response = $this->postJson(route('checkout.finalizar'), [
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
                ['id' => $produto->id, 'cor' => null, 'tamanho' => null, 'qty' => 1],
            ],
        ]);

        $response->assertOk();

        $pedido = Order::where('numero_pedido', $response->json('numero_pedido'))->firstOrFail();

        $this->assertSame(0.0, (float) $pedido->desconto);
        $this->assertEquals(80.00, (float) $pedido->subtotal);
        $this->assertEquals(19.90, (float) $pedido->frete);
        $this->assertEquals(99.90, (float) $pedido->total);

        $this->assertEquals(
            round((float) $pedido->subtotal - (float) $pedido->desconto + (float) $pedido->frete, 2),
            (float) $pedido->total
        );
    }

    public function test_desconto_tem_default_zero_no_banco_quando_omitido(): void
    {
        $produto = Product::create([
            'nome' => 'Produto Teste',
            'slug' => 'produto-teste-'.uniqid(),
            'descricao' => null,
            'preco' => 50.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);

        // Insere sem passar 'desconto' -- confere que a coluna é NOT NULL
        // DEFAULT 0 de verdade no banco, não só uma convenção da aplicação.
        $id = \Illuminate\Support\Facades\DB::table('orders')->insertGetId([
            'numero_pedido' => 'TEST-'.strtoupper(uniqid()),
            'nome' => 'Cliente', 'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'subtotal' => 50.00,
            'frete' => 19.90,
            'total' => 69.90,
            'forma_pagamento' => 'pix',
            'status' => Order::STATUS_PENDENTE,
            'endereco_entrega' => json_encode(['cep' => '00000-000', 'rua' => 'Rua Teste', 'numero' => '1']),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $pedido = Order::findOrFail($id);
        $this->assertSame(0.0, (float) $pedido->desconto);
    }

    public function test_desconto_enviado_no_payload_pelo_cliente_e_ignorado(): void
    {
        $produto = Product::create([
            'nome' => 'Produto Teste',
            'slug' => 'produto-teste-'.uniqid(),
            'descricao' => null,
            'preco' => 80.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);

        // Tentativa de fraude: cliente manda 'desconto' tentando zerar o
        // pedido. O servidor recalcula tudo a partir do banco -- esse campo
        // nem está nas regras de validate(), nunca chega em $data.
        $response = $this->postJson(route('checkout.finalizar'), [
            'nome' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'forma_pagamento' => 'pix',
            'desconto' => 9999.00,
            'total' => 0.01,
            'endereco_entrega' => [
                'cep' => '00000-000', 'rua' => 'Rua Teste', 'numero' => '1',
                'complemento' => null, 'bairro' => 'Centro', 'cidade' => 'Cidade Teste',
                'uf' => 'SP', 'referencia' => null,
            ],
            'items' => [
                ['id' => $produto->id, 'cor' => null, 'tamanho' => null, 'qty' => 1],
            ],
        ]);

        $response->assertOk();

        $pedido = Order::where('numero_pedido', $response->json('numero_pedido'))->firstOrFail();

        $this->assertSame(0.0, (float) $pedido->desconto);
        $this->assertEquals(99.90, (float) $pedido->total);
    }

    public function test_pedido_antigo_nao_muda_quando_cupom_e_editado_ou_desativado_depois(): void
    {
        $cupom = Coupon::create([
            'codigo' => 'ANTIGO10',
            'tipo' => Coupon::TIPO_FIXO,
            'valor' => 10.00,
            'ativo' => true,
        ]);

        $produto = Product::create([
            'nome' => 'Produto Teste',
            'slug' => 'produto-teste-'.uniqid(),
            'descricao' => null,
            'preco' => 80.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);

        // Pedido criado direto (sem UI ainda), gravando código + valor
        // congelados -- exatamente o que a fase 2b vai fazer de verdade.
        $pedido = Order::create([
            'user_id' => null,
            'numero_pedido' => 'TEST-'.strtoupper(uniqid()),
            'nome' => 'Cliente', 'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'subtotal' => 80.00,
            'desconto' => 10.00,
            'frete' => 19.90,
            'total' => 89.90,
            'cupom' => $cupom->codigo,
            'forma_pagamento' => Order::PAGAMENTO_PIX,
            'status' => Order::STATUS_PAGO,
            'endereco_entrega' => [
                'cep' => '00000-000', 'rua' => 'Rua Teste', 'numero' => '1',
                'complemento' => null, 'bairro' => 'Centro', 'cidade' => 'Cidade Teste',
                'uf' => 'SP', 'referencia' => null,
            ],
            'endereco_faturamento' => null,
        ]);
        $pedido->items()->create([
            'product_id' => $produto->id, 'nome' => $produto->nome, 'preco' => $produto->preco,
            'cor' => null, 'tamanho' => null, 'quantidade' => 1, 'subtotal' => 80.00,
        ]);

        // Cupom editado (valor muda de 10 pra 50) e depois desativado.
        $cupom->update(['valor' => 50.00]);
        $cupom->update(['ativo' => false]);

        $pedido->refresh();

        $this->assertEquals(10.00, (float) $pedido->desconto, 'valor congelado no pedido não pode seguir o cupom editado');
        $this->assertEquals(89.90, (float) $pedido->total);
        $this->assertSame('ANTIGO10', $pedido->cupom);
    }
}
