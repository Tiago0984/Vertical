<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * cart_items mantém a coluna `cor` e o default '' (sentinela de "sem
 * variação" na chave única `(user_id, product_id, cor, tamanho)`) mesmo
 * depois do seletor de cor sair da página do produto -- o front-end
 * simplesmente para de mandar valor, e o controller já tratava a ausência
 * como '' antes disso.
 */
class CartSemCorTest extends TestCase
{
    use RefreshDatabase;

    public function test_upsert_sem_cor_usa_string_vazia_e_respeita_chave_unica(): void
    {
        $user = User::factory()->create();
        $produto = $this->criarProduto();

        $this->actingAs($user)->postJson(route('cart.upsert'), [
            'id' => $produto->id,
            'tamanho' => 'M',
            'qty' => 1,
        ])->assertOk();

        // Segunda chamada pro MESMO produto/tamanho, cor de novo ausente --
        // tem que casar com a mesma linha (cor '' nas duas), não duplicar.
        $this->actingAs($user)->postJson(route('cart.upsert'), [
            'id' => $produto->id,
            'tamanho' => 'M',
            'qty' => 3,
        ])->assertOk();

        $itens = CartItem::where('user_id', $user->id)->where('product_id', $produto->id)->get();

        $this->assertCount(1, $itens, 'sem cor, a chave única não pode deixar duplicar a linha');
        $this->assertSame('', $itens->first()->cor);
        $this->assertSame(3, $itens->first()->quantidade);
    }

    public function test_destroy_sem_cor_remove_o_item_certo(): void
    {
        $user = User::factory()->create();
        $produto = $this->criarProduto();

        $this->actingAs($user)->postJson(route('cart.upsert'), [
            'id' => $produto->id,
            'tamanho' => 'G',
            'qty' => 2,
        ])->assertOk();

        $this->actingAs($user)->deleteJson(route('cart.destroy'), [
            'id' => $produto->id,
            'tamanho' => 'G',
        ])->assertOk();

        $this->assertSame(0, CartItem::where('user_id', $user->id)->count());
    }

    private function criarProduto(): Product
    {
        return Product::create([
            'nome' => 'Produto Teste Carrinho',
            'slug' => 'produto-teste-carrinho-'.uniqid(),
            'descricao' => null,
            'preco' => 50.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);
    }
}
