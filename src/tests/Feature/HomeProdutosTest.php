<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cobre a correção da home mostrando produto repetido, causada por duas
 * coisas independentes:
 *
 *   1. HomeController: maisVendidos era um placeholder (ids 1-9, sem
 *      relação com venda nenhuma) e destaques usava inRandomOrder() --
 *      ambos colidiam por acaso com as outras coleções.
 *   2. resources/views/site/home/produtos.blade.php: 3 colunas hardcoded
 *      lado a lado, sem nenhuma relação com o banco, repetindo produto
 *      dentro da própria seção.
 *
 * Duas garantias diferentes, testadas separadamente (não "nenhum id em
 * lugar nenhum"): as abas de Tendências (lancamentos/maisVendidos/
 * emPromocao) podem se sobrepor entre si -- um produto pode legitimamente
 * ser novo E estar em promoção, isso não é bug -- só "destaques" precisa
 * ser exclusivo delas. Já a vitrine (3 colunas visíveis ao mesmo tempo)
 * precisa ser mutuamente exclusiva, porque ali a repetição é visível na
 * mesma tela, lado a lado -- esse é o bug relatado.
 */
class HomeProdutosTest extends TestCase
{
    use RefreshDatabase;

    private const FRETE = 19.90;

    public function test_destaques_nao_repete_produto_das_outras_tres_abas_e_vitrine_nao_repete_entre_si(): void
    {
        $produtos = collect(range(1, 14))->map(fn ($i) => $this->criarProduto([
            'nome' => "Produto {$i}",
            'is_novo' => $i <= 6,
            'is_promocao' => $i >= 4 && $i <= 10,
        ]));

        foreach ($produtos->take(5) as $produto) {
            $this->criarPedido($produto, Order::STATUS_PAGO, qty: 10);
        }

        $response = $this->get(route('home'));
        $response->assertOk();

        $lancamentos = $response->viewData('lancamentos');
        $maisVendidos = $response->viewData('maisVendidos');
        $emPromocao = $response->viewData('emPromocao');
        $destaques = $response->viewData('destaques');
        $vitrineDestaque = $response->viewData('vitrineDestaque');
        $vitrineOfertas = $response->viewData('vitrineOfertas');
        $vitrineMaisVendidos = $response->viewData('vitrineMaisVendidos');

        $idsOutrasTres = $lancamentos->pluck('id')
            ->merge($maisVendidos->pluck('id'))
            ->merge($emPromocao->pluck('id'));
        $this->assertTrue($destaques->pluck('id')->intersect($idsOutrasTres)->isEmpty());

        $this->assertTrue($vitrineDestaque->pluck('id')->intersect($vitrineOfertas->pluck('id'))->isEmpty());
        $this->assertTrue($vitrineDestaque->pluck('id')->intersect($vitrineMaisVendidos->pluck('id'))->isEmpty());
        $this->assertTrue($vitrineOfertas->pluck('id')->intersect($vitrineMaisVendidos->pluck('id'))->isEmpty());
    }

    public function test_sem_pedidos_pagos_mais_vendidos_vem_vazio(): void
    {
        $this->criarProduto(['nome' => 'Produto Sem Venda']);

        $response = $this->get(route('home'));

        $this->assertTrue($response->viewData('maisVendidos')->isEmpty());
        $this->assertTrue($response->viewData('vitrineMaisVendidos')->isEmpty());
    }

    public function test_catalogo_populado_sem_pedidos_home_200_e_nenhuma_coluna_da_vitrine_fica_vazia(): void
    {
        // Cenário real do primeiro deploy: 16 produtos, ZERO pedidos (nem
        // pago, nem pendente, nem cancelado) -- vitrineMaisVendidos fica
        // garantidamente vazio, sem nenhuma venda pra rankear.
        collect(range(1, 10))->each(fn ($i) => $this->criarProduto([
            'nome' => "Produto {$i}",
            'is_promocao' => $i <= 3,
        ]));

        $response = $this->get(route('home'));
        $response->assertOk();

        $this->assertTrue(
            $response->viewData('vitrineMaisVendidos')->isEmpty(),
            'pré-condição do teste: sem pedido pago, não pode haver ranking'
        );

        $html = $response->getContent();

        // Coluna sem produto não pode aparecer no HTML -- nem o cabeçalho,
        // nem a mensagem de "nenhum produto" (essa é a resposta certa pro
        // dashboard admin, não pra vitrine pública: aqui um buraco visual
        // não ajuda ninguém).
        $this->assertStringNotContainsString('<h3>Mais Vendidos</h3>', $html);
        $this->assertStringNotContainsString('Nenhum produto vendido ainda.', $html);

        // As colunas que têm produto continuam aparecendo normalmente.
        $this->assertStringContainsString('<h3>Itens em Destaque</h3>', $html);
        $this->assertStringContainsString('<h3>Ofertas Especiais</h3>', $html);
    }

    public function test_zero_pedidos_lancamentos_e_promocao_cobrem_tudo_aba_destaques_fica_ausente(): void
    {
        // Cenário real do primeiro deploy: todo produto do catálogo é
        // is_novo ou is_promocao -- a união esgota o catálogo, sobrando
        // menos que o mínimo configurável (padrão 3) pra "Destaques".
        collect(range(1, 5))->each(fn ($i) => $this->criarProduto([
            'nome' => "Lançamento {$i}",
            'is_novo' => true,
        ]));
        collect(range(1, 5))->each(fn ($i) => $this->criarProduto([
            'nome' => "Promoção {$i}",
            'is_promocao' => true,
        ]));

        $response = $this->get(route('home'));
        $response->assertOk();

        $destaques = $response->viewData('destaques');
        $this->assertLessThan(3, $destaques->count(), 'pré-condição: lançamentos+promoção devem esgotar o catálogo');
        $this->assertFalse($response->viewData('exibirDestaques'));

        $html = $response->getContent();

        // A aba inteira some -- nav-tab e tab-pane, não só o conteúdo.
        $this->assertStringNotContainsString('DESTAQUES</a>', $html);
        $this->assertStringNotContainsString('id="tab-destaques"', $html);

        // A primeira aba que sobrou (Lançamentos) vira a ativa.
        $this->assertMatchesRegularExpression(
            '/<li role="presentation" class="active">\s*<a[^>]*>LANÇAMENTOS<\/a>/',
            $html
        );
    }

    public function test_ranking_de_mais_vendidos_bate_com_a_quantidade_vendida(): void
    {
        $campeao = $this->criarProduto(['nome' => 'Campeão de Vendas']);
        $mediano = $this->criarProduto(['nome' => 'Vendas Medianas']);
        $poucoVendido = $this->criarProduto(['nome' => 'Pouco Vendido']);

        $this->criarPedido($campeao, Order::STATUS_PAGO, qty: 20);
        $this->criarPedido($mediano, Order::STATUS_PAGO, qty: 8);
        $this->criarPedido($poucoVendido, Order::STATUS_PAGO, qty: 1);

        // Pendente e cancelado com quantidade gigante -- não podem entrar
        // na soma nem mudar a ordem do ranking.
        $this->criarPedido($poucoVendido, Order::STATUS_PENDENTE, qty: 500);
        $this->criarPedido($poucoVendido, Order::STATUS_CANCELADO, qty: 500);

        $response = $this->get(route('home'));
        $ranking = $response->viewData('maisVendidos');

        $this->assertEquals(
            [$campeao->id, $mediano->id, $poucoVendido->id],
            $ranking->pluck('id')->all()
        );
    }

    private function criarProduto(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'nome' => 'Produto Teste',
            'slug' => 'produto-teste-'.uniqid(),
            'descricao' => null,
            'preco' => 100.00,
            'preco_promocional' => 150.00,
            'imagem' => 'vertical/images/teste.jpg',
            'is_novo' => false,
            'is_promocao' => false,
        ], $overrides));
    }

    private function criarPedido(Product $produto, string $status, int $qty): Order
    {
        $subtotal = round($produto->preco * $qty, 2);
        $frete = $status === Order::STATUS_PAGO ? self::FRETE : 0;

        $order = Order::create([
            'user_id' => null,
            'numero_pedido' => 'TEST-'.strtoupper(uniqid()),
            'nome' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente.'.uniqid().'@exemplo.test',
            'telefone' => '(11) 90000-0000',
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $subtotal + $frete,
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
            'tamanho' => null,
            'quantidade' => $qty,
            'subtotal' => $subtotal,
        ]);

        return $order;
    }
}
