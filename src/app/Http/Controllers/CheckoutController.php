<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function checkout()
    {
        return view('site.checkout.checkout');
    }

    public function finalizar(Request $request)
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'sobrenome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefone' => ['required', 'string', 'max:20'],
            'forma_pagamento' => ['required', Rule::in([Order::PAGAMENTO_CARTAO, Order::PAGAMENTO_PIX, Order::PAGAMENTO_BOLETO])],

            'endereco_entrega' => ['required', 'array'],
            'endereco_entrega.cep' => ['required', 'string'],
            'endereco_entrega.rua' => ['required', 'string'],
            'endereco_entrega.numero' => ['required', 'string'],
            'endereco_entrega.complemento' => ['nullable', 'string'],
            'endereco_entrega.bairro' => ['required', 'string'],
            'endereco_entrega.cidade' => ['required', 'string'],
            'endereco_entrega.uf' => ['required', 'string', 'size:2'],
            'endereco_entrega.referencia' => ['nullable', 'string'],

            'endereco_faturamento' => ['nullable', 'array'],
            'endereco_faturamento.nome' => ['required_with:endereco_faturamento', 'string'],
            'endereco_faturamento.sobrenome' => ['required_with:endereco_faturamento', 'string'],
            'endereco_faturamento.cep' => ['required_with:endereco_faturamento', 'string'],
            'endereco_faturamento.rua' => ['required_with:endereco_faturamento', 'string'],
            'endereco_faturamento.numero' => ['required_with:endereco_faturamento', 'string'],
            'endereco_faturamento.complemento' => ['nullable', 'string'],
            'endereco_faturamento.bairro' => ['required_with:endereco_faturamento', 'string'],
            'endereco_faturamento.cidade' => ['required_with:endereco_faturamento', 'string'],
            'endereco_faturamento.uf' => ['required_with:endereco_faturamento', 'string', 'size:2'],
            'endereco_faturamento.telefone' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.cor' => ['nullable', 'string'],
            'items.*.tamanho' => ['nullable', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $produtos = Product::whereIn('id', collect($data['items'])->pluck('id'))->get()->keyBy('id');

        $subtotal = 0;
        $itensParaCriar = [];
        foreach ($data['items'] as $item) {
            $produto = $produtos->get($item['id']);
            if (! $produto) {
                continue;
            }

            $itemSubtotal = $produto->preco * $item['qty'];
            $subtotal += $itemSubtotal;
            $itensParaCriar[] = [
                'product_id' => $produto->id,
                'nome' => $produto->nome,
                'preco' => $produto->preco,
                'cor' => $item['cor'] ?? null,
                'tamanho' => $item['tamanho'] ?? null,
                'quantidade' => $item['qty'],
                'subtotal' => $itemSubtotal,
            ];
        }

        if (empty($itensParaCriar)) {
            return response()->json(['message' => 'Carrinho vazio ou produtos inválidos.'], 422);
        }

        // Frete grátis decidido pelo subtotal CHEIO, antes do desconto -- um
        // cupom não pode fazer o cliente perder o frete grátis que ele já
        // tinha, isso é experiência ruim e gera reclamação.
        $frete = $subtotal >= 150 ? 0 : 19.90;

        // Sem cupom implementado ainda (fase 2), desconto é sempre zero --
        // 0 aqui significa "sem desconto" de verdade, diferente de
        // products.custo (onde 0 seria uma afirmação falsa sobre o custo).
        $desconto = 0.00;
        $total = $subtotal - $desconto + $frete;

        $status = $data['forma_pagamento'] === Order::PAGAMENTO_CARTAO ? Order::STATUS_PAGO : Order::STATUS_PENDENTE;

        do {
            $numeroPedido = 'CS-'.strtoupper(Str::random(8));
        } while (Order::where('numero_pedido', $numeroPedido)->exists());

        $order = Order::create([
            'user_id' => $request->user()?->id,
            'numero_pedido' => $numeroPedido,
            'nome' => $data['nome'],
            'sobrenome' => $data['sobrenome'],
            'email' => $data['email'],
            'telefone' => $data['telefone'],
            'subtotal' => $subtotal,
            'desconto' => $desconto,
            'frete' => $frete,
            'total' => $total,
            // Cupom hoje é só o campo string 'orders.cupom' e nunca é
            // preenchido aqui (sempre null) -- não existe campo de cupom no
            // checkout nem tabela de cupons ainda (fase 2). O problema que
            // bloqueava desconto real (não ter onde gravar o VALOR aplicado
            // no momento da compra) já está resolvido: orders.desconto acima.
            'cupom' => null,
            'forma_pagamento' => $data['forma_pagamento'],
            'status' => $status,
            'endereco_entrega' => $data['endereco_entrega'],
            'endereco_faturamento' => $data['endereco_faturamento'] ?? null,
        ]);

        foreach ($itensParaCriar as $item) {
            $order->items()->create($item);
        }

        if ($request->user()) {
            $request->user()->cartItems()->delete();
        }

        return response()->json([
            'numero_pedido' => $order->numero_pedido,
            'status' => $order->status,
        ]);
    }
}
