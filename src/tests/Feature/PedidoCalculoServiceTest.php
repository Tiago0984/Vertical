<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use App\Services\CupomValidador;
use App\Services\PedidoCalculoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Extrair o cálculo do CheckoutController pra um service próprio é o que
 * torna a regra do frete grátis testável diretamente -- sem precisar de
 * campo de cupom no checkout (fase 2b) pra provar que ela funciona.
 */
class PedidoCalculoServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sem_cupom_subtotal_frete_e_total_corretos(): void
    {
        $produto = $this->criarProduto(80.00);

        $calculo = $this->service()->calcular($this->itens($produto, 1));

        $this->assertEquals(80.00, $calculo['subtotal']);
        $this->assertEquals(0.0, $calculo['desconto']);
        $this->assertEquals(19.90, $calculo['frete']);
        $this->assertEquals(99.90, $calculo['total']);
        $this->assertNull($calculo['cupom']);
        $this->assertNull($calculo['cupom_status']);
    }

    public function test_cupom_percentual_aplica_desconto_arredondado_a_duas_casas(): void
    {
        $produto = $this->criarProduto(33.33);
        $this->criarCupom('DEZ', Coupon::TIPO_PERCENTUAL, 10.00);

        // subtotal 99,99 -- 10% = 9,999 -> arredonda pra 10,00.
        $calculo = $this->service()->calcular($this->itens($produto, 3), 'DEZ');

        $this->assertEquals(99.99, $calculo['subtotal']);
        $this->assertEquals(10.00, $calculo['desconto']);
        $this->assertEquals(
            round($calculo['subtotal'] - $calculo['desconto'] + $calculo['frete'], 2),
            $calculo['total']
        );
    }

    public function test_cupom_fixo_menor_que_subtotal_aplica_o_valor_cheio(): void
    {
        $produto = $this->criarProduto(200.00);
        $this->criarCupom('FIXO30', Coupon::TIPO_FIXO, 30.00);

        $calculo = $this->service()->calcular($this->itens($produto, 1), 'FIXO30');

        $this->assertEquals(200.00, $calculo['subtotal']);
        $this->assertEquals(30.00, $calculo['desconto']);
        $this->assertEquals(0.0, $calculo['frete']); // subtotal cheio >= 150
        $this->assertEquals(170.00, $calculo['total']);
    }

    public function test_cupom_fixo_maior_que_subtotal_e_limitado_ao_subtotal_total_nunca_negativo(): void
    {
        $produto = $this->criarProduto(50.00);
        $this->criarCupom('FIXO500', Coupon::TIPO_FIXO, 500.00);

        $calculo = $this->service()->calcular($this->itens($produto, 1), 'FIXO500');

        $this->assertEquals(50.00, $calculo['subtotal']);
        $this->assertEquals(50.00, $calculo['desconto'], 'desconto limitado ao subtotal');
        $this->assertEquals(19.90, $calculo['frete']); // subtotal cheio (50) < 150
        $this->assertEquals(19.90, $calculo['total']); // 50 - 50 + 19.90, nunca negativo
    }

    public function test_frete_gratis_preservado_quando_desconto_derruba_subtotal_abaixo_de_150(): void
    {
        // subtotal cheio 200 (>= 150, frete grátis) -- cupom fixo de 100
        // derruba o valor "efetivo" pra 100 (< 150). Frete tem que
        // continuar grátis: foi decidido pelo subtotal CHEIO, antes do
        // desconto -- um cupom não pode fazer o cliente perder o frete
        // grátis que já tinha.
        $produto = $this->criarProduto(200.00);
        $this->criarCupom('FRETE100', Coupon::TIPO_FIXO, 100.00);

        $calculo = $this->service()->calcular($this->itens($produto, 1), 'FRETE100');

        $this->assertEquals(200.00, $calculo['subtotal']);
        $this->assertEquals(100.00, $calculo['desconto']);
        $this->assertEquals(0.0, $calculo['frete'], 'frete grátis não pode sumir por causa do desconto');
        $this->assertEquals(100.00, $calculo['total']);
    }

    public function test_codigo_invalido_nao_aplica_desconto_e_retorna_o_motivo(): void
    {
        $produto = $this->criarProduto(80.00);

        $calculo = $this->service()->calcular($this->itens($produto, 1), 'NAOEXISTE');

        $this->assertEquals(0.0, $calculo['desconto']);
        $this->assertEquals(99.90, $calculo['total']);
        $this->assertSame(CupomValidador::NAO_EXISTE, $calculo['cupom_status']);
        $this->assertSame('Cupom inválido.', $calculo['cupom_mensagem']);
        $this->assertNull($calculo['cupom']);
    }

    public function test_invariante_subtotal_menos_desconto_mais_frete_fecha_com_total_com_e_sem_cupom(): void
    {
        $produto = $this->criarProduto(90.00);
        $this->criarCupom('INV15', Coupon::TIPO_PERCENTUAL, 15.00);

        foreach ([null, 'INV15'] as $codigo) {
            $calculo = $this->service()->calcular($this->itens($produto, 2), $codigo);

            $this->assertEquals(
                round($calculo['subtotal'] - $calculo['desconto'] + $calculo['frete'], 2),
                $calculo['total'],
                'invariante quebrou com código: '.($codigo ?? 'null')
            );
        }
    }

    private function service(): PedidoCalculoService
    {
        return new PedidoCalculoService(new CupomValidador());
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

    private function criarCupom(string $codigo, string $tipo, float $valor, array $overrides = []): Coupon
    {
        return Coupon::create(array_merge([
            'codigo' => $codigo,
            'tipo' => $tipo,
            'valor' => $valor,
            'ativo' => true,
        ], $overrides));
    }

    /**
     * @return array<int, array{id: int, cor: null, tamanho: null, qty: int}>
     */
    private function itens(Product $produto, int $qty): array
    {
        return [
            ['id' => $produto->id, 'cor' => null, 'tamanho' => null, 'qty' => $qty],
        ];
    }
}
