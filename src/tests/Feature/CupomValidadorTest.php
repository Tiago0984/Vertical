<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Services\CupomValidador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Um teste por motivo de recusa -- "cupom expirado" e "cupom inválido" não
 * são a mesma coisa pra quem está comprando (ver CupomValidador).
 */
class CupomValidadorTest extends TestCase
{
    use RefreshDatabase;

    public function test_cupom_que_nao_existe_e_recusado(): void
    {
        $resultado = (new CupomValidador())->validar('NAOEXISTE', 100.00);

        $this->assertSame(CupomValidador::NAO_EXISTE, $resultado['status']);
        $this->assertSame('Cupom inválido.', $resultado['mensagem']);
        $this->assertNull($resultado['cupom']);
    }

    public function test_cupom_inativo_e_recusado(): void
    {
        Coupon::create(['codigo' => 'INATIVO10', 'tipo' => Coupon::TIPO_FIXO, 'valor' => 10.00, 'ativo' => false]);

        $resultado = (new CupomValidador())->validar('inativo10', 100.00);

        $this->assertSame(CupomValidador::INATIVO, $resultado['status']);
        $this->assertSame('Cupom inválido.', $resultado['mensagem']);
    }

    public function test_cupom_fora_da_janela_de_validade_e_recusado(): void
    {
        Coupon::create([
            'codigo' => 'FUTURO10', 'tipo' => Coupon::TIPO_FIXO, 'valor' => 10.00, 'ativo' => true,
            'inicio_em' => Carbon::now()->addDay(),
        ]);
        Coupon::create([
            'codigo' => 'PASSADO10', 'tipo' => Coupon::TIPO_FIXO, 'valor' => 10.00, 'ativo' => true,
            'fim_em' => Carbon::now()->subDay(),
        ]);

        $validador = new CupomValidador();

        foreach (['FUTURO10', 'PASSADO10'] as $codigo) {
            $resultado = $validador->validar($codigo, 100.00);
            $this->assertSame(CupomValidador::FORA_DA_VALIDADE, $resultado['status'], "código {$codigo}");
            $this->assertSame('Cupom expirado.', $resultado['mensagem']);
        }
    }

    public function test_cupom_abaixo_do_minimo_de_compra_e_recusado_com_valor_que_falta(): void
    {
        Coupon::create([
            'codigo' => 'MIN200', 'tipo' => Coupon::TIPO_FIXO, 'valor' => 10.00, 'ativo' => true,
            'minimo_compra' => 200.00,
        ]);

        // minimo_compra é comparado ao subtotal ANTES do desconto -- 150 < 200,
        // faltam 50,00. A mensagem diz o valor, não só que não atinge.
        $resultado = (new CupomValidador())->validar('MIN200', 150.00);

        $this->assertSame(CupomValidador::ABAIXO_DO_MINIMO, $resultado['status']);
        $this->assertSame('Faltam R$ 50,00 para usar este cupom.', $resultado['mensagem']);
    }

    public function test_cupom_com_uso_maximo_esgotado_e_recusado(): void
    {
        Coupon::create([
            'codigo' => 'LIMITADO', 'tipo' => Coupon::TIPO_FIXO, 'valor' => 10.00, 'ativo' => true,
            'uso_maximo' => 5, 'usos' => 5,
        ]);

        $resultado = (new CupomValidador())->validar('LIMITADO', 100.00);

        $this->assertSame(CupomValidador::ESGOTADO, $resultado['status']);
        $this->assertSame('Cupom esgotado.', $resultado['mensagem']);
    }

    public function test_cupom_valido_retorna_o_cupom(): void
    {
        $cupom = Coupon::create([
            'codigo' => 'valido10', 'tipo' => Coupon::TIPO_PERCENTUAL, 'valor' => 10.00, 'ativo' => true,
        ]);

        $resultado = (new CupomValidador())->validar('VALIDO10', 100.00);

        $this->assertSame(CupomValidador::VALIDO, $resultado['status']);
        $this->assertNull($resultado['mensagem']);
        $this->assertTrue($resultado['cupom']->is($cupom));
    }

    public function test_codigo_e_normalizado_para_maiuscula_na_comparacao(): void
    {
        // salvo como 'promo' -- o model normaliza pra 'PROMO' ao gravar.
        Coupon::create(['codigo' => 'promo', 'tipo' => Coupon::TIPO_FIXO, 'valor' => 5.00, 'ativo' => true]);

        $resultado = (new CupomValidador())->validar('PrOmO', 100.00);

        $this->assertSame(CupomValidador::VALIDO, $resultado['status']);
    }
}
