<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Categorias, faixa de preço e ordenação da /loja passaram a filtrar/ordenar
 * de verdade (antes eram decorativos: links href="#", slider nunca ligado a
 * nada). "Mais populares" saiu do dropdown por não existir métrica; "mais
 * vendidos" usa o mesmo critério de venda do dashboard (SUM de
 * order_items.quantidade em pedidos pagos).
 */
class LojaFiltrosFuncionaisTest extends TestCase
{
    use RefreshDatabase;

    public function test_filtro_por_categoria_devolve_so_produtos_daquela_categoria(): void
    {
        $camisetas = $this->criarCategoria('Camisetas');
        $acessorios = $this->criarCategoria('Acessórios');

        $this->criarProduto(['nome' => 'Camiseta A', 'category_id' => $camisetas->id]);
        $this->criarProduto(['nome' => 'Camiseta B', 'category_id' => $camisetas->id]);
        $this->criarProduto(['nome' => 'Boné', 'category_id' => $acessorios->id]);

        $response = $this->get(route('loja', ['categoria' => $camisetas->slug]));

        $response->assertOk();
        $response->assertSee('exibindo 1–2 de 2 resultados');
        $response->assertSee('Camiseta A');
        $response->assertSee('Camiseta B');
        $response->assertDontSee('Boné');
    }

    public function test_faixa_de_preco_inclui_as_bordas_corretamente(): void
    {
        // Nomes sem uma string ser substring da outra -- assertSee/assertDontSee
        // fazem checagem por substring, "Produto 80" dentro de "Produto 80 01"
        // daria falso positivo/negativo.
        $this->criarProduto(['nome' => 'ProdutoLimiteBaixo', 'preco' => 60.00]);
        $this->criarProduto(['nome' => 'ProdutoAcimaDoBaixo', 'preco' => 60.01]);
        $this->criarProduto(['nome' => 'ProdutoLimiteAlto', 'preco' => 80.00]);
        $this->criarProduto(['nome' => 'ProdutoAcimaDoAlto', 'preco' => 80.01]);

        // "Até R$ 60" -- só o de 60.00 exatos.
        $response = $this->get(route('loja', ['preco' => 'ate-60']));
        $response->assertSee('ProdutoLimiteBaixo');
        $response->assertDontSee('ProdutoAcimaDoBaixo');
        $response->assertDontSee('ProdutoLimiteAlto');
        $response->assertDontSee('ProdutoAcimaDoAlto');

        // "R$ 60 a R$ 80" -- 60.01 entra (maior que 60), 80.00 entra (até 80).
        $response = $this->get(route('loja', ['preco' => '60-80']));
        $response->assertSee('ProdutoAcimaDoBaixo');
        $response->assertSee('ProdutoLimiteAlto');
        $response->assertDontSee('ProdutoLimiteBaixo');
        $response->assertDontSee('ProdutoAcimaDoAlto');

        // "Acima de R$ 80" -- 80.00 fica de fora, só 80.01 entra.
        $response = $this->get(route('loja', ['preco' => 'acima-80']));
        $response->assertSee('ProdutoAcimaDoAlto');
        $response->assertDontSee('ProdutoLimiteAlto');
        $response->assertDontSee('ProdutoAcimaDoBaixo');
        $response->assertDontSee('ProdutoLimiteBaixo');
    }

    public function test_ordenacao_alfabetica_e_por_preco(): void
    {
        $this->criarProduto(['nome' => 'Zebra', 'preco' => 50.00]);
        $this->criarProduto(['nome' => 'Abelha', 'preco' => 90.00]);

        $response = $this->get(route('loja', ['ordem' => 'alfabetica']));
        $ordemNoHtml = $this->extrairOrdemDosNomes($response->getContent(), ['Abelha', 'Zebra']);
        $this->assertSame(['Abelha', 'Zebra'], $ordemNoHtml);

        $response = $this->get(route('loja', ['ordem' => 'menor-preco']));
        $ordemNoHtml = $this->extrairOrdemDosNomes($response->getContent(), ['Abelha', 'Zebra']);
        $this->assertSame(['Zebra', 'Abelha'], $ordemNoHtml);

        $response = $this->get(route('loja', ['ordem' => 'maior-preco']));
        $ordemNoHtml = $this->extrairOrdemDosNomes($response->getContent(), ['Abelha', 'Zebra']);
        $this->assertSame(['Abelha', 'Zebra'], $ordemNoHtml);
    }

    public function test_ordenacao_mais_recentes_e_o_padrao(): void
    {
        $antigo = $this->criarProduto(['nome' => 'Produto Antigo']);
        $antigo->created_at = Carbon::now()->subDays(10);
        $antigo->save();

        $novo = $this->criarProduto(['nome' => 'Produto Novo']);
        $novo->created_at = Carbon::now();
        $novo->save();

        $response = $this->get(route('loja'));

        $ordemNoHtml = $this->extrairOrdemDosNomes($response->getContent(), ['Produto Antigo', 'Produto Novo']);
        $this->assertSame(['Produto Novo', 'Produto Antigo'], $ordemNoHtml);
    }

    public function test_mais_vendidos_respeita_status_pago_e_nao_descarta_produto_sem_venda(): void
    {
        $maisVendido = $this->criarProduto(['nome' => 'Mais Vendido']);
        $poucoVendido = $this->criarProduto(['nome' => 'Pouco Vendido']);
        $this->criarProduto(['nome' => 'Sem Venda']);
        $vendaCancelada = $this->criarProduto(['nome' => 'So Cancelado']);

        $this->criarPedido($maisVendido, Order::STATUS_PAGO, 10);
        $this->criarPedido($poucoVendido, Order::STATUS_PAGO, 1);
        $this->criarPedido($vendaCancelada, Order::STATUS_CANCELADO, 999); // não pode contar

        $response = $this->get(route('loja', ['ordem' => 'mais-vendidos']));
        $response->assertOk();

        $ordemNoHtml = $this->extrairOrdemDosNomes(
            $response->getContent(),
            ['Mais Vendido', 'Pouco Vendido', 'Sem Venda', 'So Cancelado']
        );

        $this->assertSame('Mais Vendido', $ordemNoHtml[0]);
        $this->assertSame('Pouco Vendido', $ordemNoHtml[1]);
        // Sem Venda e So Cancelado (venda cancelada não conta = sem venda pra
        // este critério) ficam por último, mas NENHUM dos dois pode sumir da lista.
        $this->assertContains('Sem Venda', $ordemNoHtml);
        $this->assertContains('So Cancelado', $ordemNoHtml);
        $this->assertSame(4, count($ordemNoHtml));
    }

    public function test_filtro_e_ordenacao_juntos_preservam_um_ao_outro_na_navegacao(): void
    {
        $categoria = $this->criarCategoria('Camisetas');
        $this->criarProduto(['nome' => 'Camiseta Z', 'category_id' => $categoria->id, 'preco' => 50.00]);
        $this->criarProduto(['nome' => 'Camiseta A', 'category_id' => $categoria->id, 'preco' => 90.00]);
        $this->criarProduto(['nome' => 'Fora Da Categoria']);

        $response = $this->get(route('loja', ['categoria' => $categoria->slug, 'ordem' => 'alfabetica']));

        $response->assertOk();
        $response->assertDontSee('Fora Da Categoria');

        $ordemNoHtml = $this->extrairOrdemDosNomes($response->getContent(), ['Camiseta A', 'Camiseta Z']);
        $this->assertSame(['Camiseta A', 'Camiseta Z'], $ordemNoHtml);

        // O link "menor preço" do dropdown de ordenação, mesmo trocando a
        // ordenação, tem que continuar levando o filtro de categoria junto.
        $encontrado = preg_match('/href="([^"]*ordem=menor-preco[^"]*)"/', $response->getContent(), $matches);
        $this->assertSame(1, $encontrado, 'link de ordenar por menor preço não encontrado no dropdown');
        $this->assertStringContainsString(
            'categoria='.$categoria->slug,
            $matches[1],
            'o link de reordenar tem que preservar o filtro de categoria ativo'
        );
    }

    public function test_filtro_sem_resultado_mostra_mensagem_em_vez_de_grid_vazio(): void
    {
        $this->criarProduto(['nome' => 'Único Produto', 'preco' => 40.00]);

        $response = $this->get(route('loja', ['preco' => 'acima-80']));

        $response->assertOk();
        $response->assertSee('exibindo 0 resultados');
        $response->assertSee('Nenhum produto encontrado com esse filtro.');
        $response->assertDontSee('Único Produto');
    }

    public function test_parametros_invalidos_na_query_string_nao_quebram_a_pagina(): void
    {
        $this->criarProduto();

        $this->get(route('loja', ['categoria' => 'nao-existe']))->assertOk();
        $this->get(route('loja', ['preco' => 'faixa-desconhecida']))->assertOk();
        $this->get(route('loja', ['ordem' => 'ordem-estranha']))->assertOk();
        $this->get('/loja?categoria[]=x&preco[]=y&ordem[]=z')->assertOk();
    }

    public function test_pagina_invalida_na_query_string_nao_quebra_a_pagina(): void
    {
        $this->criarProduto();

        $this->get(route('loja', ['page' => 999]))->assertOk();
        $this->get('/loja?page=abc')->assertOk();
        $this->get('/loja?page=-1')->assertOk();
        $this->get('/loja?page[]=1')->assertOk();
    }

    public function test_pagina_fora_do_alcance_mostra_mensagem_em_vez_de_grid_vazio(): void
    {
        $this->criarProduto(['nome' => 'Único Produto']);

        $response = $this->get(route('loja', ['page' => 999]));

        $response->assertOk();
        $response->assertSee('Nenhum produto encontrado com esse filtro.');
        $response->assertDontSee('Único Produto');
    }

    public function test_paginacao_preserva_os_tres_filtros_ao_ir_para_pagina_2(): void
    {
        $categoria = $this->criarCategoria('Camisetas');

        // 14 produtos na faixa 60-80, todos na mesma categoria -- com
        // ordenação alfabética dá pra saber exatamente quem cai na página 2
        // (os dois últimos em ordem alfabética: "13" e "14").
        foreach (range(1, 14) as $i) {
            $this->criarProduto([
                'nome' => 'Camiseta '.str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'category_id' => $categoria->id,
                'preco' => 65.00,
            ]);
        }

        $response = $this->get(route('loja', [
            'categoria' => $categoria->slug,
            'preco' => '60-80',
            'ordem' => 'alfabetica',
            'page' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('Camiseta 13');
        $response->assertSee('Camiseta 14');
        $response->assertDontSee('Camiseta 01');
        $response->assertSee('exibindo 13–14 de 14 resultados');

        // O link "Anterior" (volta pra página 1) tem que preservar os três filtros.
        preg_match('/<a href="([^"]*)" rel="prev">/', $response->getContent(), $matches);
        $this->assertNotEmpty($matches, 'link "Anterior" não encontrado');
        $this->assertStringContainsString('categoria='.$categoria->slug, $matches[1]);
        $this->assertStringContainsString('preco=60-80', $matches[1]);
        $this->assertStringContainsString('ordem=alfabetica', $matches[1]);
    }

    public function test_trocar_de_filtro_reseta_para_pagina_1(): void
    {
        foreach (range(1, 14) as $i) {
            $this->criarProduto(['nome' => 'Produto '.str_pad((string) $i, 2, '0', STR_PAD_LEFT)]);
        }

        // Parte da página 2, sem filtro nenhum.
        $response = $this->get(route('loja', ['page' => 2]));
        $response->assertOk();

        $html = $response->getContent();

        // O link de ordenar por "menor preço" não pode levar page=2 junto --
        // mudar de ordenação tem que voltar pra página 1.
        preg_match('/href="([^"]*)"[^>]*>Menor preço<\/a>/', $html, $matches);
        $this->assertNotEmpty($matches, 'link "Menor preço" não encontrado');
        $this->assertStringNotContainsString('page=', $matches[1]);

        // O mesmo vale pro filtro de faixa de preço.
        preg_match('/href="([^"]*)"[^>]*>\s*Até R\$ 60\s*<\/a>/', $html, $matches2);
        $this->assertNotEmpty($matches2, 'link "Até R$ 60" não encontrado');
        $this->assertStringNotContainsString('page=', $matches2[1]);
    }

    public function test_contador_mostra_total_filtrado_nao_o_da_pagina(): void
    {
        foreach (range(1, 14) as $i) {
            $this->criarProduto(['nome' => 'Produto Contador '.$i]);
        }

        $response = $this->get(route('loja'));
        $response->assertOk();
        $response->assertSee('exibindo 1–12 de 14 resultados');

        $response = $this->get(route('loja', ['page' => 2]));
        $response->assertOk();
        $response->assertSee('exibindo 13–14 de 14 resultados');
    }

    public function test_categoria_pequena_nao_renderiza_controles_de_paginacao(): void
    {
        $categoria = $this->criarCategoria('Categoria Pequena');
        $this->criarProduto(['nome' => 'Único', 'category_id' => $categoria->id]);

        $response = $this->get(route('loja', ['categoria' => $categoria->slug]));

        $response->assertOk();
        $response->assertDontSee('id="pagination"', false);
    }

    private function extrairOrdemDosNomes(string $html, array $nomes): array
    {
        $posicoes = [];
        foreach ($nomes as $nome) {
            $pos = strpos($html, $nome);
            if ($pos !== false) {
                $posicoes[$nome] = $pos;
            }
        }
        asort($posicoes);

        return array_keys($posicoes);
    }

    private function criarCategoria(string $nome): Category
    {
        return Category::create([
            'nome' => $nome,
            'slug' => \Illuminate\Support\Str::slug($nome).'-'.uniqid(),
        ]);
    }

    private function criarProduto(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'nome' => 'Produto Teste',
            'slug' => 'produto-teste-'.uniqid(),
            'descricao' => null,
            'preco' => 70.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ], $overrides));
    }

    private function criarPedido(Product $produto, string $status, int $qty): Order
    {
        $subtotal = round($produto->preco * $qty, 2);

        $order = Order::create([
            'user_id' => null,
            'numero_pedido' => 'TEST-'.strtoupper(uniqid()),
            'nome' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'subtotal' => $subtotal,
            'desconto' => 0,
            'frete' => 0,
            'total' => $subtotal,
            'cupom' => null,
            'forma_pagamento' => Order::PAGAMENTO_PIX,
            'status' => $status,
            'endereco_entrega' => [
                'cep' => '00000-000', 'rua' => 'Rua Teste', 'numero' => '1',
                'complemento' => null, 'bairro' => 'Centro', 'cidade' => 'Cidade Teste',
                'uf' => 'SP', 'referencia' => null,
            ],
            'endereco_faturamento' => null,
        ]);

        $order->items()->create([
            'product_id' => $produto->id,
            'nome' => $produto->nome,
            'preco' => $produto->preco,
            'cor' => null,
            'tamanho' => 'M',
            'quantidade' => $qty,
            'subtotal' => $subtotal,
        ]);

        return $order;
    }
}
