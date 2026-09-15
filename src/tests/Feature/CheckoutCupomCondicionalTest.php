<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Campo de cupom no checkout só aparece quando existe pelo menos um cupom
 * "utilizável agora" (ativo, dentro da validade, com uso disponível) --
 * campo de cupom vazio na tela derruba conversão. Esconder é decisão de UI:
 * a rota de validar cupom e finalizar() continuam aceitando cupom
 * normalmente mesmo com o campo escondido, inclusive se o cache do campo
 * estiver desatualizado.
 */
class CheckoutCupomCondicionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_sem_nenhum_cupom_cadastrado_nao_renderiza_campo(): void
    {
        $response = $this->get(route('checkout'));

        $response->assertOk();
        $response->assertDontSee('id="cupom-input"', false);
    }

    public function test_checkout_com_cupom_utilizavel_renderiza_campo(): void
    {
        $this->criarCupom(['codigo' => 'ATIVO10', 'ativo' => true]);

        $response = $this->get(route('checkout'));

        $response->assertOk();
        $response->assertSee('id="cupom-input"', false);
    }

    public function test_cupom_esgotado_nao_faz_campo_aparecer(): void
    {
        $this->criarCupom(['codigo' => 'ESGOTADO', 'uso_maximo' => 1, 'usos' => 1]);

        $response = $this->get(route('checkout'));

        $response->assertDontSee('id="cupom-input"', false);
    }

    public function test_cupom_fora_da_validade_nao_faz_campo_aparecer(): void
    {
        $this->criarCupom(['codigo' => 'EXPIRADO', 'fim_em' => Carbon::now()->subDay()]);
        $this->criarCupom(['codigo' => 'AGENDADO', 'inicio_em' => Carbon::now()->addDay()]);

        $response = $this->get(route('checkout'));

        $response->assertDontSee('id="cupom-input"', false);
    }

    public function test_cupom_inativo_nao_faz_campo_aparecer(): void
    {
        $this->criarCupom(['codigo' => 'DESLIGADO', 'ativo' => false]);

        $response = $this->get(route('checkout'));

        $response->assertDontSee('id="cupom-input"', false);
    }

    public function test_criar_cupom_invalida_cache_proximo_checkout_ja_reflete(): void
    {
        // Esquenta o cache como "nenhum cupom utilizável" (nada cadastrado ainda).
        $this->get(route('checkout'))->assertDontSee('id="cupom-input"', false);

        $this->criarCupom(['codigo' => 'NOVO', 'ativo' => true]);

        $this->get(route('checkout'))->assertSee('id="cupom-input"', false);
    }

    public function test_desativar_cupom_invalida_cache(): void
    {
        $cupom = $this->criarCupom(['codigo' => 'ATIVO', 'ativo' => true]);
        $this->get(route('checkout'))->assertSee('id="cupom-input"', false);

        $cupom->ativo = false;
        $cupom->save();

        $this->get(route('checkout'))->assertDontSee('id="cupom-input"', false);
    }

    public function test_excluir_cupom_invalida_cache(): void
    {
        $cupom = $this->criarCupom(['codigo' => 'APAGAR', 'ativo' => true]);
        $this->get(route('checkout'))->assertSee('id="cupom-input"', false);

        $cupom->delete();

        $this->get(route('checkout'))->assertDontSee('id="cupom-input"', false);
    }

    public function test_finalizar_aceita_cupom_mesmo_com_cache_do_campo_desatualizado(): void
    {
        $produto = $this->criarProduto(200.00);

        // Esquenta o cache como "nenhum cupom utilizável" e força esse valor
        // de volta depois de criar o cupom, simulando uma janela onde o
        // cache ainda não expirou -- finalizar() nunca deve depender dele.
        $this->get(route('checkout'))->assertDontSee('id="cupom-input"', false);
        Coupon::create(['codigo' => 'ESCONDIDO', 'tipo' => Coupon::TIPO_PERCENTUAL, 'valor' => 10.00, 'ativo' => true]);
        Cache::put(Coupon::CACHE_KEY_UTILIZAVEL_AGORA, false, now()->addMinutes(5));

        $response = $this->postJson(route('checkout.finalizar'), $this->payloadFinalizar($produto, 'ESCONDIDO'));
        $response->assertOk();

        $pedido = Order::where('numero_pedido', $response->json('numero_pedido'))->firstOrFail();
        $this->assertSame('ESCONDIDO', $pedido->cupom);
        $this->assertEquals(20.00, (float) $pedido->desconto);
    }

    private function criarCupom(array $overrides = []): Coupon
    {
        return Coupon::create(array_merge([
            'codigo' => 'TESTE'.uniqid(),
            'tipo' => Coupon::TIPO_PERCENTUAL,
            'valor' => 10.00,
            'ativo' => true,
        ], $overrides));
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
