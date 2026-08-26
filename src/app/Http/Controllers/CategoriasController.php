<?php

namespace App\Http\Controllers;

use App\Models\Product;

class CategoriasController extends Controller
{
    public function categorias()
    {
        $produtos = Product::orderBy('nome')->get();

        return view('site.categorias.categorias', compact('produtos'));
    }
}
