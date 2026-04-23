<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcessoriosController extends Controller
{
    public function acessorios()
    {
        return view('site.acessorios.acessorios');
    }
}
