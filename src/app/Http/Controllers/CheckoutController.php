<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PedidoCalculoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function checkout()
    {
        return view('site.checkout.checkout');
    }

    public function finalizar(Request $request, PedidoCalculoService $calculoService)
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

            // 'desconto' deliberadamente ausente daqui -- o cliente manda no
            // máximo um código de cupom (fase 2b), nunca um valor de
            // desconto. Qualquer 'desconto' no payload é descartado pelo
            // validate() por não estar nas regras, nunca chega em $data.
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.cor' => ['nullable', 'string'],
            'items.*.tamanho' => ['nullable', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        // Toda a aritmética (subtotal, desconto, frete, total) mora no
        // service -- sem cupom vindo do cliente ainda (sem campo/UI, fase
        // 2b), então desconto sai sempre zero, mas pelo MESMO caminho que
        // vai calcular desconto de verdade depois.
        $calculo = $calculoService->calcular($data['items']);

        if ($calculo['itens']->isEmpty()) {
            return response()->json(['message' => 'Carrinho vazio ou produtos inválidos.'], 422);
        }

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
            'subtotal' => $calculo['subtotal'],
            'desconto' => $calculo['desconto'],
            'frete' => $calculo['frete'],
            'total' => $calculo['total'],
            // Sem campo de cupom no checkout ainda (fase 2b) -- ninguém
            // preenche isso hoje. Quando existir, grava o CÓDIGO aqui
            // ($calculo['cupom']?->codigo), nunca um valor de desconto.
            'cupom' => null,
            'forma_pagamento' => $data['forma_pagamento'],
            'status' => $status,
            'endereco_entrega' => $data['endereco_entrega'],
            'endereco_faturamento' => $data['endereco_faturamento'] ?? null,
        ]);

        foreach ($calculo['itens'] as $item) {
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
