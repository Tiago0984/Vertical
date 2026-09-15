<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 2c: CRUD de cupons no /admin, seguindo o padrão de Produtos/Categorias.
 * Duas regras específicas: código travado depois de usado, exclusão só com
 * usos = 0 (cupom já usado se desativa, não se apaga).
 */
class AdminCouponCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cria_cupom(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.cupons.store'), [
            'codigo' => 'novo10',
            'tipo' => Coupon::TIPO_PERCENTUAL,
            'valor' => 10,
            'ativo' => '1',
        ]);

        $response->assertRedirect(route('admin.cupons.index'));

        $cupom = Coupon::where('codigo', 'NOVO10')->firstOrFail();
        $this->assertEquals(Coupon::TIPO_PERCENTUAL, $cupom->tipo);
        $this->assertEquals(10.00, (float) $cupom->valor);
        $this->assertTrue($cupom->ativo);
    }

    public function test_admin_edita_cupom_sem_uso(): void
    {
        $admin = $this->admin();
        $cupom = $this->criarCupom(['codigo' => 'EDITAVEL', 'valor' => 10.00]);

        $this->actingAs($admin)->put(route('admin.cupons.update', $cupom), [
            'codigo' => 'novocodigo',
            'tipo' => Coupon::TIPO_FIXO,
            'valor' => 25.00,
            'ativo' => '1',
        ])->assertRedirect(route('admin.cupons.index'));

        $cupom->refresh();
        $this->assertSame('NOVOCODIGO', $cupom->codigo);
        $this->assertEquals(Coupon::TIPO_FIXO, $cupom->tipo);
        $this->assertEquals(25.00, (float) $cupom->valor);
    }

    public function test_codigo_nao_pode_ser_alterado_apos_uso_mesmo_via_post_direto(): void
    {
        $admin = $this->admin();
        $cupom = $this->criarCupom(['codigo' => 'JAUSADO', 'usos' => 3]);

        $this->actingAs($admin)->put(route('admin.cupons.update', $cupom), [
            'codigo' => 'TENTATIVA',
            'tipo' => $cupom->tipo,
            'valor' => 15.00,
            'ativo' => '1',
        ])->assertRedirect(route('admin.cupons.index'));

        $cupom->refresh();
        $this->assertSame('JAUSADO', $cupom->codigo, 'código não pode mudar depois de usado, mesmo tentando via POST direto');
        $this->assertEquals(15.00, (float) $cupom->valor, 'outros campos continuam editáveis normalmente');
    }

    public function test_exclusao_bloqueada_quando_usos_maior_que_zero(): void
    {
        $admin = $this->admin();
        $cupom = $this->criarCupom(['usos' => 1]);

        $response = $this->actingAs($admin)->delete(route('admin.cupons.destroy', $cupom));

        $response->assertSessionHasErrors('cupom');
        $this->assertNotNull($cupom->fresh(), 'cupom já usado não pode ser excluído');
    }

    public function test_exclusao_permitida_quando_usos_igual_a_zero(): void
    {
        $admin = $this->admin();
        $cupom = $this->criarCupom(['usos' => 0]);

        $response = $this->actingAs($admin)->delete(route('admin.cupons.destroy', $cupom));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertNull(Coupon::find($cupom->id));
    }

    public function test_toggle_ativo_alterna_o_estado(): void
    {
        $admin = $this->admin();
        $cupom = $this->criarCupom(['ativo' => true]);

        $this->actingAs($admin)->patch(route('admin.cupons.toggle-ativo', $cupom));
        $this->assertFalse($cupom->fresh()->ativo);

        $this->actingAs($admin)->patch(route('admin.cupons.toggle-ativo', $cupom));
        $this->assertTrue($cupom->fresh()->ativo);
    }

    public function test_validacao_rejeita_percentual_fora_de_1_a_100(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.cupons.store'), [
            'codigo' => 'PCT0', 'tipo' => Coupon::TIPO_PERCENTUAL, 'valor' => 0,
        ])->assertSessionHasErrors('valor');

        $this->actingAs($admin)->post(route('admin.cupons.store'), [
            'codigo' => 'PCT101', 'tipo' => Coupon::TIPO_PERCENTUAL, 'valor' => 101,
        ])->assertSessionHasErrors('valor');

        $this->assertSame(0, Coupon::whereIn('codigo', ['PCT0', 'PCT101'])->count());
    }

    public function test_validacao_rejeita_codigo_duplicado_ignorando_caixa(): void
    {
        $admin = $this->admin();
        $this->criarCupom(['codigo' => 'UNICO']);

        $this->actingAs($admin)->post(route('admin.cupons.store'), [
            'codigo' => 'unico', // mesma forma normalizada de 'UNICO'
            'tipo' => Coupon::TIPO_FIXO,
            'valor' => 5.00,
        ])->assertSessionHasErrors('codigo');

        $this->assertSame(1, Coupon::where('codigo', 'UNICO')->count());
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
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
}
