<?php

namespace App\Support;

use Illuminate\Support\Str;

class SlugGenerator
{
    public static function unique(string $texto, string $modelClass, ?int $ignoreId = null): string
    {
        $base = Str::slug($texto);
        $slug = $base;
        $contador = 2;

        while (
            $modelClass::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$contador}";
            $contador++;
        }

        return $slug;
    }
}
