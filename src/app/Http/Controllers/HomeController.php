<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function home()
    {
        $lancamentos = Product::where('is_novo', true)->latest()->limit(9)->get();
        $maisVendidos = Product::orderBy('id')->limit(9)->get();
        $emPromocao = Product::where('is_promocao', true)->latest()->limit(9)->get();
        $destaques = Product::inRandomOrder()->limit(9)->get();

        return view('site.home.home', compact('lancamentos', 'maisVendidos', 'emPromocao', 'destaques'));
    }
}
