<?php

/*
|--------------------------------------------------------------------------
| Configurações do checkout
|--------------------------------------------------------------------------
|
| frete_padrao: valor cobrado quando o subtotal não atinge o mínimo de
| frete grátis. frete_gratis_a_partir_de: decidido pelo subtotal CHEIO,
| antes de qualquer desconto de cupom (PedidoCalculoService).
|
*/

return [
    'frete_padrao' => 19.90,
    'frete_gratis_a_partir_de' => 150.00,
];
