<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Fase 9B: os campos de estoque/estoque_minimo/custo chegaram na fase 2
 * (migration + fillable) e no dashboard na fase 8, mas nunca no formulário
 * do CRUD -- sem isso, produção fica com painel mostrando "Não configurado"
 * e nenhuma forma de configurar nada pela tela, só UPDATE direto no banco.
 */
class AdminProductCrudTest extends TestCase
{
    use RefreshDatabase;

    /** Caminhos relativos das imagens de teste, apagados do disco no tearDown. */
    private array $imagensCriadas = [];

    protected function tearDown(): void
    {
        foreach ($this->imagensCriadas as $caminho) {
            $absoluto = public_path($caminho);
            if (File::exists($absoluto)) {
                File::delete($absoluto);
            }
        }

        parent::tearDown();
    }

    public function test_criar_produto_persiste_custo_estoque_e_estoque_minimo(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.produtos.store'), [
            'nome' => 'Produto Teste CRUD',
            'preco' => 100.00,
            'custo' => 40.00,
            'estoque' => 15,
            'estoque_minimo' => 5,
            'imagem' => UploadedFile::fake()->image('produto.png'),
        ]);

        $response->assertRedirect(route('admin.produtos.index'));

        $produto = Product::where('nome', 'Produto Teste CRUD')->firstOrFail();
        $this->imagensCriadas[] = $produto->imagem;

        $this->assertEquals(40.00, (float) $produto->custo);
        $this->assertEquals(15, $produto->estoque);
        $this->assertEquals(5, $produto->estoque_minimo);
    }

    public function test_editar_produto_altera_custo_estoque_e_estoque_minimo(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.produtos.store'), [
            'nome' => 'Produto Original',
            'preco' => 50.00,
            'custo' => 10.00,
            'estoque' => 2,
            'estoque_minimo' => 1,
            'imagem' => UploadedFile::fake()->image('produto.png'),
        ]);
        $produto = Product::where('nome', 'Produto Original')->firstOrFail();
        $this->imagensCriadas[] = $produto->imagem;

        $this->actingAs($admin)->put(route('admin.produtos.update', $produto), [
            'nome' => 'Produto Original',
            'preco' => 50.00,
            'custo' => 22.50,
            'estoque' => 30,
            'estoque_minimo' => 10,
        ])->assertRedirect(route('admin.produtos.index'));

        $produto->refresh();
        $this->assertEquals(22.50, (float) $produto->custo);
        $this->assertEquals(30, $produto->estoque);
        $this->assertEquals(10, $produto->estoque_minimo);
    }

    public function test_validacao_rejeita_negativo_em_estoque_e_estoque_minimo(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.produtos.store'), [
            'nome' => 'Produto Estoque Negativo',
            'preco' => 50.00,
            'estoque' => -1,
            'estoque_minimo' => 0,
            'imagem' => UploadedFile::fake()->image('produto.png'),
        ])->assertSessionHasErrors('estoque');
        $this->assertDatabaseMissing('products', ['nome' => 'Produto Estoque Negativo']);

        $this->actingAs($admin)->post(route('admin.produtos.store'), [
            'nome' => 'Produto Estoque Minimo Negativo',
            'preco' => 50.00,
            'estoque' => 0,
            'estoque_minimo' => -5,
            'imagem' => UploadedFile::fake()->image('produto.png'),
        ])->assertSessionHasErrors('estoque_minimo');
        $this->assertDatabaseMissing('products', ['nome' => 'Produto Estoque Minimo Negativo']);
    }

    public function test_custo_vazio_grava_null_em_vez_de_zero(): void
    {
        $admin = $this->admin();

        // Criar sem informar custo -- tem que gravar NULL, não 0 (0 é "custa
        // zero" e entraria como "com custo cadastrado" na cobertura do
        // financeiro(), mascarando a defesa da fase 6D).
        $this->actingAs($admin)->post(route('admin.produtos.store'), [
            'nome' => 'Produto Sem Custo',
            'preco' => 50.00,
            'estoque' => 0,
            'estoque_minimo' => 0,
            'imagem' => UploadedFile::fake()->image('produto.png'),
        ])->assertRedirect(route('admin.produtos.index'));

        $produtoNovo = Product::where('nome', 'Produto Sem Custo')->firstOrFail();
        $this->imagensCriadas[] = $produtoNovo->imagem;
        $this->assertNull($produtoNovo->custo);

        // Editar um produto que TINHA custo, apagando o campo -- também tem
        // que virar NULL, não silenciosamente continuar com o valor antigo
        // nem cair pra 0.
        $this->actingAs($admin)->post(route('admin.produtos.store'), [
            'nome' => 'Produto Com Custo',
            'preco' => 50.00,
            'custo' => 15.00,
            'estoque' => 0,
            'estoque_minimo' => 0,
            'imagem' => UploadedFile::fake()->image('produto.png'),
        ]);
        $produtoExistente = Product::where('nome', 'Produto Com Custo')->firstOrFail();
        $this->imagensCriadas[] = $produtoExistente->imagem;

        $this->actingAs($admin)->put(route('admin.produtos.update', $produtoExistente), [
            'nome' => 'Produto Com Custo',
            'preco' => 50.00,
            // custo ausente de propósito
            'estoque' => 0,
            'estoque_minimo' => 0,
        ]);

        $produtoExistente->refresh();
        $this->assertNull($produtoExistente->custo);
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->is_admin = true;
        $user->save();

        return $user;
    }
}
