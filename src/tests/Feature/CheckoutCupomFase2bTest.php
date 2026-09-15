<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Fase 2b: rota de validar cupom (preview, com rate limit) e a aplicação
 * real em /checkout/finalizar -- revalidação sob lockForUpdate() na mesma
 * transação do pedido, incremento de usos só quando o cupom é de fato
 * aplicado.
 */
class CheckoutCupomFase2bTest extends TestCase
{
    use RefreshDatabase;

    public function test_rota_de_validar_cupom_retorna_desconto_para_codigo_valido(): void
    {
        $produto = $this->criarProduto(200.00);
        Coupon::create(['codigo' => 'DEZ', 'tipo' => Coupon::TIPO_PERCENTUAL, 'valor' => 10.00, 'ativo' => true]);

        $response = $this->postJson(route('checkout.cupom'), [
            'codigo' => 'dez',
            'items' => [['id' => $produto->id, 'cor' => null, 'tamanho' => null, 'qty' => 1]],
        ]);

        $response->assertOk();
        $response->assertJson([
            'valido' => true,
            'mensagem' => null,
            'subtotal' => 200.00,
            'desconto' => 20.00,
            'frete' => 0.0,
            'total' => 180.00,
        ]);
    }

    public function test_rota_de_validar_cupom_retorna_mensagem_especifica_para_codigo_invalido(): void
    {
        $produto = $this->criarProduto(80.00);

        $response = $this->postJson(route('checkout.cupom'), [
            'codigo' => 'NAOEXISTE',
            'items' => [['id' => $produto->id, 'cor' => null, 'tamanho' => null, 'qty' => 1]],
        ]);

        $response->assertOk();
        $response->assertJson([
            'valido' => false,
            'mensagem' => 'Cupom inválido.',
            'desconto' => 0.0,
        ]);
    }

    public function test_rota_de_validar_cupom_nao_grava_nada_no_banco(): void
    {
        $produto = $this->criarProduto(200.00);
        $cupom = Coupon::create(['codigo' => 'DEZ', 'tipo' => Coupon::TIPO_PERCENTUAL, 'valor' => 10.00, 'ativo' => true]);

        $this->postJson(route('checkout.cupom'), [
            'codigo' => 'DEZ',
            'items' => [['id' => $produto->id, 'cor' => null, 'tamanho' => null, 'qty' => 1]],
        ])->assertOk();

        $this->assertSame(0, $cupom->fresh()->usos, 'preview não pode incrementar usos');
        $this->assertSame(0, Order::count(), 'preview não pode criar pedido');
    }

    public function test_rate_limit_da_rota_de_validar_cupom_responde_429_ao_ser_estourado(): void
    {
        $produto = $this->criarProduto(80.00);
        $payload = [
            'codigo' => 'QUALQUER',
            'items' => [['id' => $produto->id, 'cor' => null, 'tamanho' => null, 'qty' => 1]],
        ];

        // Limite configurado na rota: throttle:10,1 (10 por minuto).
        for ($i = 0; $i < 10; $i++) {
            $this->postJson(route('checkout.cupom'), $payload)->assertOk();
        }

        $this->postJson(route('checkout.cupom'), $payload)->assertStatus(429);
    }

    public function test_finalizar_com_cupom_valido_grava_codigo_e_valor_e_incrementa_usos(): void
    {
        $produto = $this->criarProduto(200.00);
        $cupom = Coupon::create(['codigo' => 'DEZ', 'tipo' => Coupon::TIPO_PERCENTUAL, 'valor' => 10.00, 'ativo' => true]);

        $response = $this->postJson(route('checkout.finalizar'), $this->payloadFinalizar($produto, 'dez'));
        $response->assertOk();

        $pedido = Order::where('numero_pedido', $response->json('numero_pedido'))->firstOrFail();

        $this->assertSame('DEZ', $pedido->cupom);
        $this->assertEquals(20.00, (float) $pedido->desconto);
        $this->assertEquals(180.00, (float) $pedido->total);
        $this->assertSame(1, $cupom->fresh()->usos);
    }

    public function test_finalizar_usa_lockforupdate_na_leitura_do_cupom(): void
    {
        $produto = $this->criarProduto(80.00);
        $cupom = Coupon::create(['codigo' => 'DEZ', 'tipo' => Coupon::TIPO_FIXO, 'valor' => 5.00, 'ativo' => true]);

        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });

        $this->postJson(route('checkout.finalizar'), $this->payloadFinalizar($produto, 'DEZ'))->assertOk();

        $queryCupom = collect($queries)->first(
            fn ($sql) => str_contains(strtolower($sql), 'from `coupons`') || str_contains(strtolower($sql), 'from coupons')
        );

        $this->assertNotNull($queryCupom, 'nenhuma query SELECT em coupons foi capturada');
        $this->assertStringContainsStringIgnoringCase('for update', $queryCupom);
    }

    public function test_finalizar_rele_usos_do_banco_no_momento_do_lock_nao_usa_valor_em_cache(): void
    {
        $produto = $this->criarProduto(80.00);
        $cupom = Coupon::create([
            'codigo' => 'ULTIMO', 'tipo' => Coupon::TIPO_FIXO, 'valor' => 10.00, 'ativo' => true,
            'uso_maximo' => 1, 'usos' => 0,
        ]);

        // Simula: entre o cliente digitar/validar o cupom (que veria usos=0
        // e aprovaria) e o clique em finalizar, outro pedido já consumiu a
        // única vaga -- update direto no banco, sem passar pelo Eloquent do
        // teste, pra garantir que não é o MESMO objeto PHP sendo reciclado.
        DB::table('coupons')->where('id', $cupom->id)->update(['usos' => 1]);

        $response = $this->postJson(route('checkout.finalizar'), $this->payloadFinalizar($produto, 'ULTIMO'));
        $response->assertOk();

        $pedido = Order::where('numero_pedido', $response->json('numero_pedido'))->firstOrFail();

        // Se finalizar() tivesse usado um Coupon já carregado/cacheado em
        // vez de reler sob lock no momento do commit, isso aplicaria o
        // desconto -- errado, a vaga já tinha sido consumida.
        $this->assertSame(0.0, (float) $pedido->desconto);
        $this->assertNull($pedido->cupom);
        $this->assertSame(1, $cupom->fresh()->usos, 'usos não pode passar de uso_maximo');
    }

    public function test_usos_nao_ultrapassa_uso_maximo_com_pedidos_verdadeiramente_concorrentes(): void
    {
        $this->markTestIncomplete(
            'RefreshDatabase envolve cada teste numa transação não commitada -- uma '.
            'segunda conexão MySQL de verdade (simulando um segundo pedido concorrente '.
            'de fato) não enxergaria nem o cupom criado no setup deste teste, então não '.
            'dá pra provar a corrida de dentro do PHPUnit sem contornar o isolamento '.
            'transacional do qual o resto da suíte depende. O que ESTÁ provado aqui: a '.
            'query usa lockForUpdate() (test_finalizar_usa_lockforupdate_na_leitura_do_cupom) '.
            'e a revalidação sempre relê usos fresco do banco no momento do lock, não um '.
            'valor em cache (test_finalizar_rele_usos_do_banco_no_momento_do_lock_nao_usa_valor_em_cache). '.
            'Verificação de concorrência de verdade exigiria dois processos PHP '.
            'independentes contra o banco, fora do wrapper transacional do PHPUnit -- '.
            'viável como script manual, não como teste automatizado desta suíte. '.
            'Verificado manualmente em 15/09/2026, em dev: duas requisições simultâneas a '.
            '/checkout/finalizar (Promise.all no console do navegador, mesmo payload), '.
            'cupom BEMVINDO10 com uso_maximo = 1. Resultado: dois pedidos criados, '.
            'contador em 1/1, e apenas um com desconto -- CS-WSXKCUGC (R$ 127,84, com os '.
            '20%) e CS-JHWLODO4 (R$ 159,80, preço cheio). O lockForUpdate segurou a '.
            'segunda transação como esperado.'
        );
    }

    private function criarProduto(float $preco): Product
    {
        return Product::create([
            'nome' => 'Produto Teste',
            'slug' => 'produto-teste-'.uniqid(),
            'descricao' => null,
            'preco' => $preco,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);
    }

    private function payloadFinalizar(Product $produto, ?string $cupom): array
    {
        return [
            'nome' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'forma_pagamento' => 'pix',
            'cupom' => $cupom,
            'endereco_entrega' => [
                'cep' => '00000-000', 'rua' => 'Rua Teste', 'numero' => '1',
                'complemento' => null, 'bairro' => 'Centro', 'cidade' => 'Cidade Teste',
                'uf' => 'SP', 'referencia' => null,
            ],
            'items' => [
                ['id' => $produto->id, 'cor' => null, 'tamanho' => null, 'qty' => 1],
            ],
        ];
    }
}
