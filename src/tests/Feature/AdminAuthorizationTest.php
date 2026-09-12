<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Antes de expor número de faturamento numa tela, confirma que /admin está
 * de fato protegida por is_admin, não só por estar logado -- sem esse
 * teste, qualquer regressão no middleware (ex.: alguém trocar 'admin' por
 * 'auth' sem querer) passaria despercebida até um cliente comum acessar o
 * dashboard financeiro da loja.
 */
class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_sem_is_admin_recebe_403_no_painel(): void
    {
        $user = User::factory()->create();
        $user->is_admin = false; // is_admin não é mass-assignable de propósito (fase 5A)
        $user->save();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_usuario_com_is_admin_acessa_o_painel(): void
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
    }

    public function test_visitante_nao_autenticado_e_redirecionado_para_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('login'));
    }
}
