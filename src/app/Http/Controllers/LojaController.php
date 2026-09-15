<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LojaController extends Controller
{
    private const POR_PAGINA = 12;

    private const ORDEM_PADRAO = 'recentes';

    /**
     * Rótulo de cada opção -- "Mais populares" saiu por não existir métrica
     * nenhuma por trás; "mais vendidos" usa o mesmo critério do dashboard
     * (SUM de order_items.quantidade em pedidos pagos).
     */
    private const OPCOES_ORDEM = [
        self::ORDEM_PADRAO => 'Mais recentes',
        'alfabetica' => 'Ordem alfabética',
        'menor-preco' => 'Menor preço',
        'maior-preco' => 'Maior preço',
        'mais-vendidos' => 'Mais vendidos',
    ];

    /**
     * Faixas derivadas da distribuição real do catálogo (16 produtos entre
     * R$ 39,90 e R$ 99,90 em dev): ~terços do catálogo, arredondados pra
     * número redondo. min/max nulos = sem limite naquele lado; os limites
     * são (min, max] pra cada preço cair em exatamente uma faixa, sem
     * sobreposição nem buraco na borda.
     */
    private const FAIXAS_PRECO = [
        'ate-60' => ['label' => 'Até R$ 60', 'min' => null, 'max' => 60.0],
        '60-80' => ['label' => 'R$ 60 a R$ 80', 'min' => 60.0, 'max' => 80.0],
        'acima-80' => ['label' => 'Acima de R$ 80', 'min' => 80.0, 'max' => null],
    ];

    public function loja(Request $request)
    {
        $categorias = Category::withCount('products')->orderBy('nome')->get();

        // is_string() antes de usar o valor como chave de array/comparação --
        // "?preco[]=x" ou "?ordem[]=x" não pode virar TypeError de "illegal
        // offset type"; um parâmetro com formato errado cai no padrão, igual
        // a um parâmetro com valor desconhecido.
        $categoriaAtiva = $categorias->firstWhere('slug', $this->queryString($request, 'categoria'));

        $faixaChave = $this->queryString($request, 'preco');
        $faixaAtiva = $faixaChave !== null ? (self::FAIXAS_PRECO[$faixaChave] ?? null) : null;

        $ordemChave = $this->queryString($request, 'ordem') ?? self::ORDEM_PADRAO;
        if (! array_key_exists($ordemChave, self::OPCOES_ORDEM)) {
            $ordemChave = self::ORDEM_PADRAO;
        }

        // Página inválida (não numérica, negativa, "?page[]=1") vira 1 aqui
        // mesmo, sem deixar o paginate() cair no fallback dele: passando
        // sempre um inteiro concreto (nunca null), o Laravel nunca releria o
        // "page" bruto da request pra tentar validar sozinho -- e "page"
        // como array quebrando validação interna vem exatamente daí.
        $pagina = $this->paginaValida($request);

        $query = Product::query()->with('category');

        if ($categoriaAtiva) {
            $query->where('category_id', $categoriaAtiva->id);
        }

        if ($faixaAtiva) {
            if ($faixaAtiva['min'] !== null) {
                $query->where('preco', '>', $faixaAtiva['min']);
            }
            if ($faixaAtiva['max'] !== null) {
                $query->where('preco', '<=', $faixaAtiva['max']);
            }
        }

        $this->aplicarOrdenacao($query, $ordemChave);

        // withQueryString() é o que impede a página 2 de perder
        // categoria/preco/ordem -- sem isso, ir pra próxima página cai num
        // catálogo sem filtro nenhum, diferente do que a pessoa via.
        $produtos = $query->paginate(self::POR_PAGINA, ['*'], 'page', $pagina)->withQueryString();

        // Só os parâmetros reconhecidos/válidos sobrevivem nos links -- um
        // parâmetro inválido na URL de entrada (categoria/faixa/ordem que não
        // existe) não se propaga pros links da própria tela. "page" nunca
        // entra aqui de propósito: trocar de filtro tem que voltar pra
        // página 1 -- quem está na 2 e filtra por uma categoria pequena não
        // pode cair numa página que não existe mais pra ela.
        $paramsAtuais = array_filter([
            'categoria' => $categoriaAtiva?->slug,
            'preco' => $faixaAtiva !== null ? $faixaChave : null,
            'ordem' => $ordemChave !== self::ORDEM_PADRAO ? $ordemChave : null,
        ]);

        $categoriasComLink = $categorias->map(fn (Category $categoria) => [
            'nome' => $categoria->nome,
            'products_count' => $categoria->products_count,
            'ativa' => $categoriaAtiva?->is($categoria) ?? false,
            'url' => $this->construirUrl($paramsAtuais, ['categoria' => $categoria->slug]),
        ]);

        $faixasComLink = collect(self::FAIXAS_PRECO)->map(fn (array $faixa, string $chave) => [
            'chave' => $chave,
            'label' => $faixa['label'],
            'ativa' => $faixaChave === $chave && $faixaAtiva !== null,
            'url' => $this->construirUrl($paramsAtuais, ['preco' => $chave]),
        ])->values();

        $opcoesOrdemComLink = collect(self::OPCOES_ORDEM)->map(fn (string $label, string $chave) => [
            'chave' => $chave,
            'label' => $label,
            'ativa' => $ordemChave === $chave,
            'url' => $this->construirUrl($paramsAtuais, ['ordem' => $chave === self::ORDEM_PADRAO ? null : $chave]),
        ])->values();

        return view('site.loja.loja', [
            'produtos' => $produtos,
            'categorias' => $categoriasComLink,
            'urlTodasCategorias' => $this->construirUrl($paramsAtuais, ['categoria' => null]),
            'faixasPreco' => $faixasComLink,
            'urlTodosPrecos' => $this->construirUrl($paramsAtuais, ['preco' => null]),
            'opcoesOrdem' => $opcoesOrdemComLink,
            'ordemAtualLabel' => self::OPCOES_ORDEM[$ordemChave],
            'temFiltroAtivo' => $categoriaAtiva !== null || $faixaAtiva !== null,
            'urlLimparFiltros' => route('loja', $ordemChave !== self::ORDEM_PADRAO ? ['ordem' => $ordemChave] : []),
        ]);
    }

    /**
     * "Mais vendidos" só entra via withSum -- sem isso, produto sem venda
     * nenhuma teria vendidos=null e cairia fora de qualquer SUM, mas o join
     * correlato (EXISTS) do whereHas mantém a linha do produto, só zera a
     * métrica dele. NULL por último no ORDER BY ... DESC do MySQL já resolve
     * "produto sem venda vai pro fim, não some da lista" de graça.
     */
    private function aplicarOrdenacao(Builder $query, string $ordemChave): void
    {
        match ($ordemChave) {
            'alfabetica' => $query->orderBy('nome'),
            'menor-preco' => $query->orderBy('preco')->orderBy('nome'),
            'maior-preco' => $query->orderByDesc('preco')->orderBy('nome'),
            'mais-vendidos' => $query
                ->withSum(['orderItems as vendidos' => function ($q) {
                    $q->whereHas('order', fn ($oq) => $oq->where('status', Order::STATUS_PAGO));
                }], 'quantidade')
                ->orderByDesc('vendidos')
                ->orderBy('nome'),
            default => $query->orderByDesc('created_at')->orderBy('nome'),
        };
    }

    /**
     * Funde os parâmetros atuais (categoria/preco/ordem já validados) com um
     * override -- valor null no override REMOVE a chave (usado pelos links
     * "todas as categorias" / "todos os preços" e pra não gravar
     * ?ordem=recentes na URL do padrão).
     *
     * "page" é descartado explicitamente (nunca está em $paramsAtuais nem
     * deve vir em $overrides, mas o unset() garante isso mesmo que alguém
     * adicione um dia): mudar categoria/preço/ordem tem que voltar pra
     * página 1 sempre, não continuar na página em que a pessoa estava.
     */
    private function construirUrl(array $paramsAtuais, array $overrides): string
    {
        $params = array_filter(
            array_merge($paramsAtuais, $overrides),
            fn ($valor) => $valor !== null
        );
        unset($params['page']);

        return route('loja', $params);
    }

    private function queryString(Request $request, string $chave): ?string
    {
        $valor = $request->query($chave);

        return is_string($valor) ? $valor : null;
    }

    /**
     * Sempre devolve um inteiro concreto (nunca null) -- "?page=abc",
     * "?page=-1" e "?page[]=1" caem pra 1 aqui mesmo. Devolver null faria o
     * paginate() cair no fallback dele (Paginator::resolveCurrentPage()),
     * que RELÊ o "page" bruto da request pra validar sozinho; "page" como
     * array chegando lá é o cenário que queremos evitar por completo, não
     * confiar que o validador interno lida bem com ele.
     */
    private function paginaValida(Request $request): int
    {
        $valor = $this->queryString($request, 'page');

        if ($valor === null || ! ctype_digit($valor) || (int) $valor < 1) {
            return 1;
        }

        return (int) $valor;
    }
}
