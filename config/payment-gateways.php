<?php

return [
    'drivers' => [
        'payment-gerencianet-pix' => \App\Support\Payments\Drivers\GerencianetPixGateway::class,
        'payment-binance-pay' => \App\Support\Payments\Drivers\BinancePayGateway::class,
        'payment-mercado-pago' => \App\Support\Payments\Drivers\MercadoPagoGateway::class,
        'payment-stripe' => \App\Support\Payments\Drivers\StripeGateway::class,
    ],
];
