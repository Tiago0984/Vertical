<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Sidebar de filtros da /loja veio do template com blocos que não filtram
 * nada de verdade -- Cores (cor não é campo de produto), Marcas (a loja só
 * tem uma) e Tamanho (não é atributo do produto nem tem estoque próprio)
 * saíram da tela. Categorias e Faixa de preço ficaram: têm dado real por
 * trás (mesmo que hoje ainda decorativos), decisão de removê-los ou
 * implementá-los de verdade fica para depois.
 */
class LojaFiltrosTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_da_loja_nao_mostra_filtro_de_cor_marca_ou_tamanho(): void
    {
        $this->criarProduto();

        $response = $this->get(route('loja'));

        $response->assertOk();
        $response->assertDontSee('CORES');
        $response->assertDontSee('MARCAS');
        $response->assertDontSee('TAMANHO');
        $response->assertDontSee('cat_color', false);
        $response->assertDontSee('cat_size', false);
        $response->assertDontSee('Vertical Basics');
    }

    public function test_pagina_da_loja_continua_mostrando_categorias_e_faixa_de_preco(): void
    {
        $this->criarProduto();

        $response = $this->get(route('loja'));

        $response->assertOk();
        $response->assertSee('CATEGORIAS');
        $response->assertSee('FAIXA DE PREÇO');
    }

    private function criarProduto(): Product
    {
        return Product::create([
            'nome' => 'Camiseta Teste',
            'slug' => 'camiseta-teste-'.uniqid(),
            'descricao' => null,
            'preco' => 100.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ]);
    }
}
