<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritosController extends Controller
{
    public function favoritos()
    {
        return view('site.favoritos.favoritos');
    }
}
