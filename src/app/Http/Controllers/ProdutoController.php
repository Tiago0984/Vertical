<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProdutoController extends Controller
{
    public function produto(?string $slug = null)
    {
        $produto = $slug
            ? Product::where('slug', $slug)->first()
            : Product::orderBy('id')->first();

        if (! $produto) {
            throw new NotFoundHttpException();
        }

        $relacionados = Product::where('category_id', $produto->category_id)
            ->where('id', '!=', $produto->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('site.produto.produto', compact('produto', 'relacionados'));
    }
}
