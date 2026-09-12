<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Limiares do painel de estoque
    |--------------------------------------------------------------------------
    |
    | multiplicador_atencao: um produto entra em "Atenção" quando o estoque
    | cai até este múltiplo do estoque_minimo (ex.: 1.5 = até 50% acima do
    | mínimo). Abaixo (ou igual) do próprio estoque_minimo, vira "Repor".
    |
    */

    'estoque' => [
        'multiplicador_atencao' => 1.5,
    ],

];
