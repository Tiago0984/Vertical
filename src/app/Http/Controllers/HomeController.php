<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    public function home()
    {
        // Ranking real de vendas: soma de order_items.quantidade em pedidos
        // pagos, do mais vendido pro menos vendido. Sem nenhuma venda, vem
        // vazio -- nada de fallback fingindo que existe "mais vendido".
        $rankingVendidosIds = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', Order::STATUS_PAGO)
            ->whereNotNull('order_items.product_id')
            ->groupBy('order_items.product_id')
            ->selectRaw('order_items.product_id as product_id, SUM(order_items.quantidade) as qtd_vendida')
            ->orderByDesc('qtd_vendida')
            ->pluck('product_id');

        $maisVendidosIds = $rankingVendidosIds->take(9)->all();
        $maisVendidos = $this->produtosNaOrdemDosIds($maisVendidosIds);

        $lancamentos = Product::where('is_novo', true)->latest()->limit(9)->get();
        $emPromocao = Product::where('is_promocao', true)->latest()->limit(9)->get();

        // destaques é o "resto" desta seção -- só ele precisa excluir as
        // outras três coleções das abas de Tendências. lancamentos, mais
        // vendidos e em promoção continuam independentes entre si: um
        // produto pode legitimamente ser novo E mais vendido E estar em
        // promoção ao mesmo tempo, isso não é o bug (o bug era o antigo
        // inRandomOrder() colidir por sorte a cada reload).
        $idsTendencias = $lancamentos->pluck('id')
            ->merge($maisVendidos->pluck('id'))
            ->merge($emPromocao->pluck('id'))
            ->unique()
            ->all();

        $destaques = Product::whereNotIn('id', $idsTendencias)
            ->orderBy('id')
            ->limit(9)
            ->get();

        // "Destaques" é catch-all sem filtro próprio -- diferente de "Mais
        // Vendidos" (cujo rótulo já explica uma lista vazia), um "Destaques"
        // vazio ou curto demais pra sustentar um carrossel (1-2 itens) só
        // confunde. Abaixo do mínimo, a aba inteira não é renderizada.
        $exibirDestaques = $destaques->count() >= (int) config('home.minimo_produtos_carrossel');

        // Vitrine (resources/views/site/home/produtos.blade.php): 3 colunas
        // visíveis lado a lado ao mesmo tempo -- diferente das abas de
        // Tendências acima (só uma aba visível por vez), aqui repetir
        // produto entre as colunas É o bug original. Não precisam excluir
        // as coleções de Tendências: são seções diferentes da página.
        $vitrineMaisVendidosIds = $rankingVendidosIds->take(6)->all();
        $vitrineMaisVendidos = $this->produtosNaOrdemDosIds($vitrineMaisVendidosIds);

        $vitrineOfertas = Product::where('is_promocao', true)
            ->whereNotIn('id', $vitrineMaisVendidosIds)
            ->latest()
            ->limit(6)
            ->get();

        $idsVitrine = array_merge($vitrineMaisVendidosIds, $vitrineOfertas->pluck('id')->all());

        $vitrineDestaque = Product::whereNotIn('id', $idsVitrine)
            ->orderBy('id')
            ->limit(6)
            ->get();

        return view('site.home.home', compact(
            'lancamentos', 'maisVendidos', 'emPromocao', 'destaques', 'exibirDestaques',
            'vitrineDestaque', 'vitrineOfertas', 'vitrineMaisVendidos'
        ));
    }

    /**
     * Product::whereIn() não preserva a ordem dos ids -- precisamos da
     * ordem do ranking de vendas (mais vendido primeiro), não a ordem
     * natural da tabela.
     */
    private function produtosNaOrdemDosIds(array $ids): Collection
    {
        $produtos = Product::whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)->map(fn ($id) => $produtos->get($id))->filter()->values();
    }
}
