<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Catálogo consolidado a partir dos produtos que estavam espalhados
     * (e duplicados com IDs diferentes) entre loja, categorias e produto.
     */
    public function run(): void
    {
        $basicas = Category::where('slug', 'camisetas-basicas')->first();
        $estampadas = Category::where('slug', 'camisetas-estampadas')->first();
        $esportivas = Category::where('slug', 'camisetas-esportivas')->first();
        $premium = Category::where('slug', 'camisetas-premium')->first();

        $produtos = [
            ['nome' => 'Camiseta Estampada Street Art', 'categoria' => $estampadas, 'preco' => 69.90, 'promo' => 77.90, 'imagem' => 't_item1.jpg', 'novo' => true],
            ['nome' => 'Camiseta Gola V Azul', 'categoria' => $basicas, 'preco' => 54.90, 'promo' => null, 'imagem' => 't_item2.jpg', 'novo' => true],
            ['nome' => 'Camiseta Oversized Preta', 'categoria' => $basicas, 'preco' => 79.90, 'promo' => null, 'imagem' => 't_item3.jpg', 'novo' => true],
            ['nome' => 'Camiseta Dry-Fit Preta', 'categoria' => $esportivas, 'preco' => 63.90, 'promo' => 79.90, 'imagem' => 't_item4.jpg', 'novo' => false],
            ['nome' => 'Camiseta Dry-Fit Sport', 'categoria' => $esportivas, 'preco' => 59.90, 'promo' => 69.90, 'imagem' => 't_item5.jpg', 'novo' => true],
            ['nome' => 'Camiseta Long Line', 'categoria' => $basicas, 'preco' => 74.90, 'promo' => null, 'imagem' => 't_item6.jpg', 'novo' => true],
            ['nome' => 'Camiseta Estampada Geométrica', 'categoria' => $estampadas, 'preco' => 64.90, 'promo' => null, 'imagem' => 't_item7.jpg', 'novo' => true],
            ['nome' => 'Camiseta Básica Cinza Mescla', 'categoria' => $basicas, 'preco' => 49.90, 'promo' => null, 'imagem' => 't_item8.jpg', 'novo' => true],
            ['nome' => 'Camiseta Premium Slim Fit', 'categoria' => $premium, 'preco' => 99.90, 'promo' => 119.90, 'imagem' => 't_item9.jpg', 'novo' => true],
            ['nome' => 'Camiseta Básica Branca', 'categoria' => $basicas, 'preco' => 39.90, 'promo' => 49.90, 'imagem' => 't_item10.jpg', 'novo' => false],
            ['nome' => 'Camiseta Estampada Geométrica Azul', 'categoria' => $estampadas, 'preco' => 59.90, 'promo' => 74.90, 'imagem' => 't_item11.jpg', 'novo' => true],
            ['nome' => 'Camiseta Premium Manga Longa', 'categoria' => $premium, 'preco' => 89.90, 'promo' => 99.90, 'imagem' => 't_item12.jpg', 'novo' => true],
            ['nome' => 'Camiseta Tie-Dye Colorida', 'categoria' => $estampadas, 'preco' => 74.90, 'promo' => 89.90, 'imagem' => 't_item13.jpg', 'novo' => false],
            ['nome' => 'Camiseta Manga Longa Listrada', 'categoria' => $basicas, 'preco' => 84.90, 'promo' => null, 'imagem' => 't_item14.jpg', 'novo' => true],
            ['nome' => 'Camiseta Retrô Vintage', 'categoria' => $estampadas, 'preco' => 69.90, 'promo' => 87.90, 'imagem' => 't_item15.jpg', 'novo' => true],
            ['nome' => 'Camiseta Estampa Minimalista', 'categoria' => $estampadas, 'preco' => 64.90, 'promo' => null, 'imagem' => 't_item16.jpg', 'novo' => true],
        ];

        foreach ($produtos as $p) {
            Product::updateOrCreate(
                ['slug' => str($p['nome'])->slug()],
                [
                    'category_id' => $p['categoria']?->id,
                    'nome' => $p['nome'],
                    'preco' => $p['preco'],
                    'preco_promocional' => $p['promo'],
                    'imagem' => 'vertical/images/' . $p['imagem'],
                    'is_novo' => $p['novo'],
                    'is_promocao' => $p['promo'] !== null,
                ]
            );
        }
    }
}
