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
     * Preview (rota de validar cupom) ou cálculo sem cupom nenhum: busca o
     * cupom pelo código, sem lock -- não escreve nada no banco.
     *
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
        [$itensCalculados, $subtotal, $frete] = $this->calcularBase($itens);

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

        return $this->montarResultado($itensCalculados, $subtotal, $desconto, $frete, $cupom, $cupomStatus, $cupomMensagem);
    }

    /**
     * Revalidação final do checkout: recebe um Coupon JÁ CARREGADO sob
     * lockForUpdate() dentro da transação do pedido -- não busca por
     * código de novo aqui, pra não abrir mão do lock que o chamador já
     * tomou. Se a revalidação falhar (esgotou, expirou, foi desativado
     * entre a tela e o envio), o desconto simplesmente não é aplicado --
     * o pedido segue sem cupom, não é bloqueado.
     *
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
    public function calcularComCupomTravado(array $itens, ?Coupon $cupomTravado): array
    {
        [$itensCalculados, $subtotal, $frete] = $this->calcularBase($itens);

        $cupom = null;
        $cupomStatus = null;
        $cupomMensagem = null;
        $desconto = 0.0;

        if ($cupomTravado !== null) {
            $resultado = $this->cupomValidador->validarCupom($cupomTravado, $subtotal);
            $cupomStatus = $resultado['status'];
            $cupomMensagem = $resultado['mensagem'];

            if ($resultado['status'] === CupomValidador::VALIDO) {
                $cupom = $resultado['cupom'];
                $desconto = $this->calcularDesconto($cupom, $subtotal);
            }
        }

        return $this->montarResultado($itensCalculados, $subtotal, $desconto, $frete, $cupom, $cupomStatus, $cupomMensagem);
    }

    /**
     * @param  array<int, array{id: int, qty: int, cor?: ?string, tamanho?: ?string}>  $itens
     * @return array{0: Collection, 1: float, 2: float}
     */
    private function calcularBase(array $itens): array
    {
        $itensCalculados = $this->calcularItens($itens);
        $subtotal = round($itensCalculados->sum('subtotal'), 2);

        // Regra #2: decidido pelo subtotal cheio, antes de qualquer desconto.
        $frete = $subtotal >= (float) config('checkout.frete_gratis_a_partir_de')
            ? 0.0
            : (float) config('checkout.frete_padrao');

        return [$itensCalculados, $subtotal, $frete];
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

    private function montarResultado(
        Collection $itensCalculados,
        float $subtotal,
        float $desconto,
        float $frete,
        ?Coupon $cupom,
        ?string $cupomStatus,
        ?string $cupomMensagem
    ): array {
        return [
            'itens' => $itensCalculados,
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'frete' => $frete,
            'total' => round($subtotal - $desconto + $frete, 2),
            'cupom' => $cupom,
            'cupom_status' => $cupomStatus,
            'cupom_mensagem' => $cupomMensagem,
        ];
    }
}
