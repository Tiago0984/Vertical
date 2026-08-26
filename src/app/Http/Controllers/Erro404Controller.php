<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Erro404Controller extends Controller
{
    public function erro404()
    {
        return view('site.erro404.erro404');
    }
}
