<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Toda a aritmética do pedido mora aqui -- subtotal, desconto, frete, total
 * -- em vez de inline no controller. Sempre recalcula a partir dos produtos
 * do banco: nunca confia em preço, subtotal ou desconto vindo do cliente.
 *
 * Regras do projeto (não reabrir sem avisar):
 *   1. desconto incide sobre o subtotal, nunca sobre o frete;
 *   2. frete grátis é decidido pelo subtotal CHEIO, antes do desconto --
 *      um cupom não pode tirar o frete grátis que o cliente já tinha;
 *   3. subtotal - desconto + frete = total, sempre;
 *   4. desconto nunca passa do subtotal e nunca é negativo.
 */
class PedidoCalculoService
{
    public function __construct(private CupomValidador $cupomValidador)
    {
    }

    /**
     * @param  array<int, array{id: int, qty: int, cor?: ?string, tamanho?: ?string}>  $itens
     * @return array{
     *     itens: Collection,
     *     subtotal: float,
     *     desconto: float,
     *     frete: float,
     *     total: float,
     *     cupom: ?Coupon,
     *     cupom_status: ?string,
     *     cupom_mensagem: ?string,
     * }
     */
    public function calcular(array $itens, ?string $codigoCupom = null): array
    {
        $itensCalculados = $this->calcularItens($itens);
        $subtotal = round($itensCalculados->sum('subtotal'), 2);

        // Regra #2: decidido pelo subtotal cheio, antes de qualquer desconto.
        $frete = $subtotal >= (float) config('checkout.frete_gratis_a_partir_de')
            ? 0.0
            : (float) config('checkout.frete_padrao');

        $cupom = null;
        $cupomStatus = null;
        $cupomMensagem = null;
        $desconto = 0.0;

        if ($codigoCupom !== null && trim($codigoCupom) !== '') {
            $resultado = $this->cupomValidador->validar($codigoCupom, $subtotal);
            $cupomStatus = $resultado['status'];
            $cupomMensagem = $resultado['mensagem'];

            if ($resultado['status'] === CupomValidador::VALIDO) {
                $cupom = $resultado['cupom'];
                $desconto = $this->calcularDesconto($cupom, $subtotal);
            }
        }

        $total = round($subtotal - $desconto + $frete, 2);

        return [
            'itens' => $itensCalculados,
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'frete' => $frete,
            'total' => $total,
            'cupom' => $cupom,
            'cupom_status' => $cupomStatus,
            'cupom_mensagem' => $cupomMensagem,
        ];
    }

    /**
     * @param  array<int, array{id: int, qty: int, cor?: ?string, tamanho?: ?string}>  $itens
     */
    private function calcularItens(array $itens): Collection
    {
        $produtos = Product::whereIn('id', collect($itens)->pluck('id'))->get()->keyBy('id');

        return collect($itens)
            ->map(function (array $item) use ($produtos) {
                $produto = $produtos->get($item['id']);
                if (! $produto) {
                    return null;
                }

                $itemSubtotal = round((float) $produto->preco * $item['qty'], 2);

                return [
                    'product_id' => $produto->id,
                    'nome' => $produto->nome,
                    'preco' => (float) $produto->preco,
                    'cor' => $item['cor'] ?? null,
                    'tamanho' => $item['tamanho'] ?? null,
                    'quantidade' => $item['qty'],
                    'subtotal' => $itemSubtotal,
                ];
            })
            ->filter()
            ->values();
    }

    private function calcularDesconto(Coupon $cupom, float $subtotal): float
    {
        $desconto = $cupom->tipo === Coupon::TIPO_PERCENTUAL
            ? round($subtotal * ((float) $cupom->valor / 100), 2)
            : round((float) $cupom->valor, 2);

        // Regra #4: nunca negativo, nunca passa do subtotal (sem total negativo).
        return max(0.0, min($desconto, $subtotal));
    }
}
