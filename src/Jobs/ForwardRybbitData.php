<?php

namespace Carloeusebi\RybbitTunnel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

class ForwardRybbitData implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $url,
        protected array $data,
        protected array $headers
    )
    {
    }

    public function handle(): void
    {
        Http::withHeaders($this->headers)->post($this->url, $this->data);
    }

}