<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AccountOrderController extends Controller
{
    public function index(Request $request)
    {
        return view('site.conta.pedidos.index', [
            'pedidos' => $request->user()->orders()->latest()->get(),
        ]);
    }

    public function show(Request $request, Order $pedido)
    {
        abort_unless($pedido->user_id === $request->user()->id, 403);

        return view('site.conta.pedidos.show', [
            'pedido' => $pedido->load('items.product'),
        ]);
    }
}
