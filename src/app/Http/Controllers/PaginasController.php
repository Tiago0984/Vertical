<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaginasController extends Controller
{
    public function paginas()
    {
        return view('site.paginas.paginas');
    }
}
