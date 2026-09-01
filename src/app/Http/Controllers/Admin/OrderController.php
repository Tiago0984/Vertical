<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $pedidos = Order::query()
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('busca'), function ($query) use ($request) {
                $busca = $request->input('busca');
                $query->where(fn ($q) => $q->where('numero_pedido', 'like', "%{$busca}%")
                    ->orWhere('email', 'like', "%{$busca}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pedidos.index', ['pedidos' => $pedidos]);
    }

    public function show(Order $pedido)
    {
        return view('admin.pedidos.show', [
            'pedido' => $pedido->load('items.product', 'user'),
        ]);
    }

    public function updateStatus(Request $request, Order $pedido)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
        ]);

        $pedido->update($data);

        return back()->with('status', 'Status do pedido atualizado.');
    }
}
