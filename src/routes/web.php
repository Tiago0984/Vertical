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
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\Erro404Controller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartSyncController;
use App\Http\Controllers\FavoritosSyncController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AccountOrderController;
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
Route::post('/checkout/finalizar', [CheckoutController::class, 'finalizar'])->name('checkout.finalizar');
Route::get('/favoritos', [FavoritosController::class, 'favoritos'])->name('favoritos');
Route::get('/contato', [ContatoController::class, 'contato'])->name('contato');
Route::get('/404', [Erro404Controller::class, 'erro404'])->name('erro404');

Route::middleware('guest')->group(function () {
    Route::get('/entrar', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/entrar', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/cadastro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/cadastro', [AuthController::class, 'register'])->name('register.attempt');
});

Route::post('/sair', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/carrinho/sincronizar', [CartSyncController::class, 'index'])->name('cart.sync');
    Route::post('/carrinho/mesclar', [CartSyncController::class, 'merge'])->name('cart.merge');
    Route::post('/carrinho/item', [CartSyncController::class, 'upsert'])->name('cart.upsert');
    Route::delete('/carrinho/item', [CartSyncController::class, 'destroy'])->name('cart.destroy');

    Route::get('/favoritos/sincronizar', [FavoritosSyncController::class, 'index'])->name('favoritos.sync');
    Route::post('/favoritos/mesclar', [FavoritosSyncController::class, 'merge'])->name('favoritos.merge');
    Route::post('/favoritos/toggle', [FavoritosSyncController::class, 'toggle'])->name('favoritos.toggle');
});

Route::middleware('auth')->prefix('minha-conta')->name('conta.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/dados-pessoais', [AccountController::class, 'dadosPessoais'])->name('dados-pessoais');
    Route::put('/dados-pessoais', [AccountController::class, 'atualizarDadosPessoais'])->name('dados-pessoais.atualizar');
    Route::put('/senha', [AccountController::class, 'atualizarSenha'])->name('senha.atualizar');

    Route::get('/enderecos', [AddressController::class, 'index'])->name('enderecos.index');
    Route::get('/enderecos/novo', [AddressController::class, 'create'])->name('enderecos.create');
    Route::post('/enderecos', [AddressController::class, 'store'])->name('enderecos.store');
    Route::get('/enderecos/{endereco}/editar', [AddressController::class, 'edit'])->name('enderecos.edit');
    Route::put('/enderecos/{endereco}', [AddressController::class, 'update'])->name('enderecos.update');
    Route::delete('/enderecos/{endereco}', [AddressController::class, 'destroy'])->name('enderecos.destroy');
    Route::post('/enderecos/{endereco}/padrao', [AddressController::class, 'definirPadrao'])->name('enderecos.padrao');

    Route::get('/pedidos', [AccountOrderController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{pedido}', [AccountOrderController::class, 'show'])->name('pedidos.show');
});
