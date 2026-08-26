<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;

class FavoritosSyncController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($this->formatFavoritos($request->user()));
    }

    public function merge(Request $request)
    {
        $data = $request->validate([
            'items' => ['array'],
            'items.*.id' => ['required', 'integer'],
        ]);

        $user = $request->user();

        foreach ($data['items'] ?? [] as $item) {
            if (! Product::whereKey($item['id'])->exists()) {
                continue;
            }

            Favorite::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $item['id'],
            ]);
        }

        return response()->json($this->formatFavoritos($user));
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $user = $request->user();

        $favorite = Favorite::where('user_id', $user->id)->where('product_id', $data['id'])->first();
        if ($favorite) {
            $favorite->delete();
        } else {
            Favorite::create(['user_id' => $user->id, 'product_id' => $data['id']]);
        }

        return response()->json($this->formatFavoritos($user));
    }

    private function formatFavoritos($user): array
    {
        return $user->favorites()->with('product')->get()
            ->filter(fn ($favorite) => $favorite->product !== null)
            ->map(fn ($favorite) => [
                'id' => $favorite->product_id,
                'nome' => $favorite->product->nome,
                'preco' => (float) $favorite->product->preco,
                'imagem' => asset($favorite->product->imagem),
            ])
            ->values()
            ->all();
    }
}
