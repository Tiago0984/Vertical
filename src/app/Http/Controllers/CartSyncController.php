<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartSyncController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($this->formatCart($request->user()));
    }

    public function merge(Request $request)
    {
        $data = $request->validate([
            'items' => ['array'],
            'items.*.id' => ['required', 'integer'],
            'items.*.cor' => ['nullable', 'string'],
            'items.*.tamanho' => ['nullable', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $user = $request->user();

        foreach ($data['items'] ?? [] as $item) {
            if (! Product::whereKey($item['id'])->exists()) {
                continue;
            }

            $cartItem = CartItem::firstOrNew([
                'user_id' => $user->id,
                'product_id' => $item['id'],
                'cor' => $item['cor'] ?? '',
                'tamanho' => $item['tamanho'] ?? '',
            ]);
            $cartItem->quantidade = ($cartItem->exists ? $cartItem->quantidade : 0) + $item['qty'];
            $cartItem->save();
        }

        return response()->json($this->formatCart($user));
    }

    public function upsert(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'exists:products,id'],
            'cor' => ['nullable', 'string'],
            'tamanho' => ['nullable', 'string'],
            'qty' => ['required', 'integer', 'min:1'],
        ]);

        CartItem::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'product_id' => $data['id'],
                'cor' => $data['cor'] ?? '',
                'tamanho' => $data['tamanho'] ?? '',
            ],
            ['quantidade' => $data['qty']]
        );

        return response()->json($this->formatCart($request->user()));
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer'],
            'cor' => ['nullable', 'string'],
            'tamanho' => ['nullable', 'string'],
        ]);

        CartItem::where('user_id', $request->user()->id)
            ->where('product_id', $data['id'])
            ->where('cor', $data['cor'] ?? '')
            ->where('tamanho', $data['tamanho'] ?? '')
            ->delete();

        return response()->json($this->formatCart($request->user()));
    }

    private function formatCart($user): array
    {
        return $user->cartItems()->with('product')->get()
            ->filter(fn ($cartItem) => $cartItem->product !== null)
            ->map(fn ($cartItem) => [
                'id' => $cartItem->product_id,
                'nome' => $cartItem->product->nome,
                'preco' => (float) $cartItem->product->preco,
                'imagem' => asset($cartItem->product->imagem),
                'cor' => $cartItem->cor,
                'tamanho' => $cartItem->tamanho,
                'qty' => $cartItem->quantidade,
            ])
            ->values()
            ->all();
    }
}
