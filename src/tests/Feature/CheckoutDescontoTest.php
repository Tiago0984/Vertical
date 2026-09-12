<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 1 da fundação de desconto: orders.desconto existe e o
 * CheckoutController já calcula total = subtotal - desconto + frete, mas
 * ainda não há cupom nenhum (fase 2) -- desconto é sempre 0.00 por enquanto.
 * Este teste garante que a fiação está certa hoje, pronta pro valor deixar
 * de ser hardcoded quando o cupom existir.
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

    public function test_frete_gratis_preservado_quando_desconto_derruba_subtotal_abaixo_do_minimo(): void
    {
        $this->markTestIncomplete(
            'Fase 2: sem campo de cupom, não há como um desconto não-zero entrar pelo '.
            'checkout. Quando entrar, este teste prova a regra: o frete grátis é '.
            'decidido pelo subtotal ANTES do desconto — um cupom não pode fazer o '.
            'cliente perder o frete grátis que já tinha.'
        );
    }
}
