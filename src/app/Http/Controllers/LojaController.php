<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class LojaController extends Controller
{
    public function loja()
    {
        $produtos = Product::with('category')->orderBy('nome')->get();
        $categorias = Category::withCount('products')->orderBy('nome')->get();

        return view('site.loja.loja', compact('produtos', 'categorias'));
    }
}
