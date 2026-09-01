<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard.index', [
            'stats' => [
                'totalPedidos' => Order::count(),
                'pedidosPendentes' => Order::where('status', Order::STATUS_PENDENTE)->count(),
                'totalProdutos' => Product::count(),
                'totalClientes' => User::where('is_admin', false)->count(),
            ],
            'ultimosPedidos' => Order::latest()->take(5)->get(),
        ]);
    }
}
