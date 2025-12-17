<?php

return [
    'host' => env('RYBBIT_TUNNEL_HOST', 'https://app.rybbit.io'),

    'tunnel-url' => env('RYBBIT_TUNNEL_URL', '/analytics'),

    'cache-key-prefix' => 'rybbit_',

    // Enable verbose logging for debugging purposes. When true, incoming request
    // headers will be logged by the proxy controller.
    'debug' => env('RYBBIT_TUNNEL_DEBUG', false),

    'middleware' => [
        'web',
    ],
];
