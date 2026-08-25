<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SingleBlogController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\LojaController;
use App\Http\Controllers\AcessoriosController;
use App\Http\Controllers\PaginasController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavoritosController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/blog', [BlogController::class, 'blog'])->name('blog');
Route::get('/blog/post/{slug?}', [SingleBlogController::class, 'singleBlog'])->name('single-blog');
Route::get('/categorias', [CategoriasController::class, 'categorias'])->name('categorias');
Route::get('/loja', [LojaController::class, 'loja'])->name('loja');
Route::get('/acessorios', [AcessoriosController::class, 'acessorios'])->name('acessorios');
Route::get('/paginas', [PaginasController::class, 'paginas'])->name('paginas');
Route::get('/produto/{slug?}', [ProdutoController::class, 'produto'])->name('produto');
Route::get('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::get('/favoritos', [FavoritosController::class, 'favoritos'])->name('favoritos');
