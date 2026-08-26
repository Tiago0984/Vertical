<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Camisetas Básicas',
            'Camisetas Estampadas',
            'Camisetas Esportivas',
            'Camisetas Premium',
        ];

        foreach ($categorias as $nome) {
            Category::updateOrCreate(
                ['slug' => str($nome)->slug()],
                ['nome' => $nome]
            );
        }
    }
}
