<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoriasController extends Controller
{
    public function categorias()
    {
        return view('site.categorias.categorias');
    }
}
