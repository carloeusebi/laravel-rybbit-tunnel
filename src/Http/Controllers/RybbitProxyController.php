<?php

namespace Carloeusebi\RybbitTunnel\Http\Controllers;

use Carloeusebi\RybbitTunnel\Jobs\ForwardRybbitData;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RybbitProxyController extends Controller
{
    private string $rybbitHost;

    public function __construct()
    {
        $this->rybbitHost = config('rybbit-tunnel.host');
    }

    public function proxyScript(Request $request, string $script)
    {
        $cacheKey = config('rybbit-tunnel.cache-key-prefix').'script_'.$script;

        return Cache::remember($cacheKey, 3600, fn () => $this->forwardRequest("api/$script", 'GET', $request));
    }

    public function proxyTrack(Request $request)
    {
        return $this->forwardRequest('api/track', 'POST', $request);
    }

    public function proxyIdentify(Request $request)
    {
        return $this->forwardRequest('api/identify', 'POST', $request);
    }

    public function proxySessionReplay(Request $request, string $siteId)
    {
        ForwardRybbitData::dispatch(
            "$this->rybbitHost/api/session-replay/record/$siteId",
            $request->all(),
            [
                'X-Real-Ip' => $request->ip(),
                'User-Agent' => $request->userAgent(),
            ]
        );

        return response()->json(['status' => 'queued']);
    }

    public function proxySiteConfig(Request $request, string $siteId)
    {
        $cacheKey = config('rybbit-tunnel.cache-key-prefix').'config_'.$siteId;

        return Cache::remember($cacheKey, 3600, fn () => $this->forwardRequest("api/site/tracking-config/$siteId", 'GET', $request));
    }

    private function forwardRequest(string $path, string $method, Request $request)
    {
        $url = "$this->rybbitHost/$path";

        $clientIp = $request->header('X-Forwarded-For', $request->ip());

        $httpRequest = Http::timeout(30)
            ->withHeaders([
                'X-Real-IP' => $clientIp,
                'X-Forwarded-For' => $clientIp,
                'User-Agent' => $request->userAgent(),
                'Referer' => $request->header('Referer', ''),
            ]);

        try {
            if ($method === 'POST') {
                $response = $httpRequest->post($url, $request->all());
            } else {
                $response = $httpRequest->get($url);
            }

            return response($response->body(), $response->status())
                ->header('Content-Type', $response->header('Content-Type'));

        } catch (\Exception $e) {
            Log::error('Rybbit proxy error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Analytics proxy error'], 500);
        }
    }
}
