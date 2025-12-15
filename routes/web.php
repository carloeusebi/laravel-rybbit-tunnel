<?php

use Carloeusebi\RybbitTunnel\Http\Controllers\RybbitProxyController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('rybbit-tunnel.tunnel-url'))->group(function () {
    Route::get('/{script}', [RybbitProxyController::class, 'proxyScript'])->where('script', '(script|script-full|replay|metrics)\.js');

    Route::post('/track', [RybbitProxyController::class, 'proxyTrack']);
    Route::post('/identify', [RybbitProxyController::class, 'proxyIdentify']);
    Route::post('/session-replay/record/{siteId}', [RybbitProxyController::class, 'proxySessionReplay']);

    Route::get('/site/tracking-config/{siteId}', [RybbitProxyController::class, 'proxySiteConfig']);
});
