<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Services\PedidoCalculoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            // máximo um código de cupom, nunca um valor de desconto.
            // Qualquer 'desconto' no payload é descartado pelo validate()
            // por não estar nas regras, nunca chega em $data.
            'cupom' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.cor' => ['nullable', 'string'],
            'items.*.tamanho' => ['nullable', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($request, $data, $calculoService) {
            // Revalidação final: busca o cupom sob lockForUpdate() dentro da
            // MESMA transação do pedido. A linha fica travada até o commit
            // -- um segundo pedido concorrente com o mesmo cupom espera essa
            // transação terminar antes de conseguir ler/incrementar usos,
            // então dois pedidos não conseguem furar uso_maximo juntos.
            $cupomTravado = null;
            if (! empty($data['cupom'])) {
                $cupomTravado = Coupon::where('codigo', Str::upper(trim($data['cupom'])))
                    ->lockForUpdate()
                    ->first();
            }

            // Se o cupom expirou/esgotou/foi desativado entre a tela e o
            // envio, calcularComCupomTravado() já devolve desconto=0 e
            // cupom=null -- o pedido segue sem desconto, não é bloqueado.
            $calculo = $calculoService->calcularComCupomTravado($data['items'], $cupomTravado);

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
                // CÓDIGO congelado no pedido, nunca o valor recalculável --
                // null se nenhum cupom foi aplicado (ou se a revalidação
                // acima recusou).
                'cupom' => $calculo['cupom']?->codigo,
                'forma_pagamento' => $data['forma_pagamento'],
                'status' => $status,
                'endereco_entrega' => $data['endereco_entrega'],
                'endereco_faturamento' => $data['endereco_faturamento'] ?? null,
            ]);

            foreach ($calculo['itens'] as $item) {
                $order->items()->create($item);
            }

            // Incrementa usos só quando o cupom realmente foi aplicado,
            // dentro da mesma transação/lock do pedido.
            $calculo['cupom']?->increment('usos');

            if ($request->user()) {
                $request->user()->cartItems()->delete();
            }

            return response()->json([
                'numero_pedido' => $order->numero_pedido,
                'status' => $order->status,
            ]);
        });
    }

    /**
     * Preview do desconto pra atualizar o resumo sem recarregar a página --
     * NÃO grava nada (nem pedido, nem usos). A revalidação de verdade
     * acontece de novo em finalizar(), sob lock: o cliente pode ter ficado
     * minutos na tela e o cupom ter mudado nesse meio tempo.
     */
    public function validarCupom(Request $request, PedidoCalculoService $calculoService)
    {
        $data = $request->validate([
            'codigo' => ['required', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.cor' => ['nullable', 'string'],
            'items.*.tamanho' => ['nullable', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $calculo = $calculoService->calcular($data['items'], $data['codigo']);

        return response()->json([
            'valido' => $calculo['cupom'] !== null,
            'mensagem' => $calculo['cupom_mensagem'],
            'subtotal' => $calculo['subtotal'],
            'desconto' => $calculo['desconto'],
            'frete' => $calculo['frete'],
            'total' => $calculo['total'],
        ]);
    }
}
