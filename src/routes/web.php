<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\AcessoriosController;
use App\Http\Controllers\PaginasController;
use App\Http\Controllers\CarrinhoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/blog', [BlogController::class, 'blog'])->name('blog');
Route::get('/categorias', [CategoriasController::class, 'categorias'])->name('categorias');
Route::get('/loja', [LojaController::class, 'loja'])->name('loja');
Route::get('/acessorios', [AcessoriosController::class, 'acessorios'])->name('acessorios');
Route::get('/paginas', [PaginasController::class, 'paginas'])->name('paginas');
Route::get('/carrinho', [CarrinhoController::class, 'carrinho'])->name('carrinho');
