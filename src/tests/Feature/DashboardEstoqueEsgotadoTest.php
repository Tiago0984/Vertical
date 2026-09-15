<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * "Esgotado" é card próprio na zona de estado atual do dashboard, separado
 * de "Repor" -- Repor é abaixo do mínimo mas ainda vendável, Esgotado é
 * venda perdida agora. Um produto com estoque 0 sai da contagem de Repor e
 * entra na de Esgotado, mesmo quando tinha estoque_minimo configurado.
 */
class DashboardEstoqueEsgotadoTest extends TestCase
{
    use RefreshDatabase;

    public function test_contagem_de_esgotados_e_certa_e_produto_sai_de_repor(): void
    {
        // estoque 0 -- hoje contaria como "Repor" (0 <= 10); depois do ajuste
        // deve migrar pra "Esgotado" e sair da contagem de Repor.
        $this->criarProduto(estoque: 0, estoqueMinimo: 10);
        // estoque 3 -- continua "Repor" normalmente (abaixo do mínimo, mas > 0).
        $this->criarProduto(estoque: 3, estoqueMinimo: 10);

        $service = new DashboardService();
        $contagem = $service->estoque()['produtos']->countBy('status');

        $this->assertSame(1, $contagem->get(DashboardService::ESTOQUE_ESGOTADO, 0));
        $this->assertSame(1, $contagem->get(DashboardService::ESTOQUE_REPOR, 0));
    }

    public function test_card_de_esgotados_aparece_no_dashboard(): void
    {
        $admin = $this->admin();
        $this->criarProduto(estoque: 0, estoqueMinimo: 0);

        $response = $this->actingAs($admin)->get('/admin?periodo=30d');

        $response->assertOk();
        $response->assertSee('Produtos esgotados');
    }

    public function test_listagem_de_produtos_do_admin_mostra_esgotado(): void
    {
        $admin = $this->admin();
        $this->criarProduto(estoque: 0, estoqueMinimo: 0, nome: 'Camiseta Esgotada');

        $response = $this->actingAs($admin)->get(route('admin.produtos.index'));

        $response->assertOk();
        $response->assertSee('Esgotado');
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    private function criarProduto(int $estoque, int $estoqueMinimo, string $nome = 'Produto Teste Estoque'): Product
    {
        return Product::create([
            'nome' => $nome,
            'slug' => 'produto-teste-estoque-'.uniqid(),
            'descricao' => null,
            'preco' => 100.00,
            'estoque' => $estoque,
            'estoque_minimo' => $estoqueMinimo,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);
    }
}
