<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function produto()
    {
        return view('site.produto.produto');
    }
}
