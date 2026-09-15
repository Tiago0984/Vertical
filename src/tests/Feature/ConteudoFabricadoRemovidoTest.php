<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase A: o site ainda não está no ar, e estamos tirando dele tudo que
 * promete ao cliente algo que o sistema não cumpre -- prova social
 * fabricada (estrelas fixas, "(42 avaliações)", resenhas com nome
 * inventado, depoimentos de template não traduzido, contador de "clientes
 * satisfeitos") e a feature de blog inteira (sem model, sem posts reais,
 * com comentários de leitor inventados). Espaço vazio é melhor que elogio
 * inventado -- sem substituto foi colocado no lugar.
 */
class ConteudoFabricadoRemovidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_do_produto_nao_mostra_avaliacao_fabricada(): void
    {
        $produto = $this->criarProduto();

        $response = $this->get(route('produto', $produto->slug));

        $response->assertOk();
        $response->assertDontSee('avaliaç', false);
        $response->assertDontSee('Maria S.');
        $response->assertDontSee('João P.');
        $response->assertDontSee('fa-star', false);
    }

    public function test_home_nao_mostra_depoimento_nem_contador_de_clientes_fabricado(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        // "SAN MICHLE" / "DIRECTOR OF EXAMPLE LTD" é texto de template não
        // editado -- prova cabal de que nunca foi depoimento real.
        $response->assertDontSee('SAN MICHLE');
        $response->assertDontSee('clientes satisfeitos');
        $response->assertDontSee('trendy cloth designs', true);
    }

    public function test_rotas_de_blog_nao_existem_mais(): void
    {
        $this->get('/blog')->assertNotFound();
        $this->get('/blog/post/qualquer-coisa')->assertNotFound();
    }

    public function test_header_e_footer_nao_linkam_mais_para_blog(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Blog de Moda');
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
