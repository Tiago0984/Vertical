<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $clientes = User::where('is_admin', false)
            ->when($request->filled('busca'), function ($query) use ($request) {
                $busca = $request->input('busca');
                $query->where(fn ($q) => $q->where('name', 'like', "%{$busca}%")
                    ->orWhere('email', 'like', "%{$busca}%"));
            })
            ->withCount('orders')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.clientes.index', ['clientes' => $clientes]);
    }

    public function show(User $cliente)
    {
        abort_if($cliente->is_admin, 404);

        return view('admin.clientes.show', [
            'cliente' => $cliente->load('orders', 'addresses'),
        ]);
    }
}
