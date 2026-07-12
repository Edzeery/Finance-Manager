<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Vite;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    private const DEFAULT_CSP = [
        'default-src' => ["'self'"],
        'script-src' => ["'self'", "'unsafe-eval'"],
        'script-src-elem' => ["'self'", "'unsafe-inline'"],
        'script-src-attr' => ["'unsafe-inline'"],
        'style-src' => ["'self'", 'fonts.googleapis.com', 'cdn.jsdelivr.net'],
        'style-src-elem' => ["'self'", "'unsafe-inline'", 'fonts.googleapis.com'],
        'style-src-attr' => ["'unsafe-inline'"],
        'font-src' => ["'self'", 'fonts.gstatic.com'],
        'img-src' => ["'self'", 'data:', 'blob:'],
        'media-src' => ["'self'", 'data:'],
        'connect-src' => ["'self'"],
        'frame-ancestors' => ["'none'"],
        'base-uri' => ["'self'"],
        'object-src' => ["'none'"],
    ];

    private const CSP_ORDER = [
        'default-src', 'script-src', 'script-src-elem', 'script-src-attr',
        'style-src', 'style-src-elem', 'style-src-attr',
        'font-src', 'img-src', 'media-src', 'connect-src', 'object-src',
        'frame-ancestors', 'base-uri',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $appOrigin = $this->resolveAppOrigin();

        $response->headers->set('Content-Security-Policy', $this->buildCsp($appOrigin));

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        return $response;
    }

    private function resolveAppOrigin(): string
    {
        $appUrl = config('app.url');
        if (!$appUrl) {
            return '';
        }
        $host = parse_url($appUrl, PHP_URL_HOST);
        if (!$host) {
            return '';
        }
        $scheme = parse_url($appUrl, PHP_URL_SCHEME) ?? 'https';
        return "$scheme://$host";
    }

    private function buildCsp(string $appOrigin = ''): string
    {
        $directives = self::DEFAULT_CSP;

        $nonce = $this->resolveNonce();
        if ($nonce) {
            $directives['script-src'][] = "'nonce-{$nonce}'";
        }

        if ($appOrigin) {
            $directives['script-src'][] = $appOrigin;
            $directives['style-src'][] = $appOrigin;
            $directives['connect-src'][] = $appOrigin;
        }

        return $this->formatCsp($directives);
    }

    private function resolveNonce(): ?string
    {
        try {
            $vite = app(Vite::class);
            return $vite->cspNonce();
        } catch (\Throwable) {
            return null;
        }
    }

    private function formatCsp(array $directives): string
    {
        $parts = [];
        foreach (self::CSP_ORDER as $directive) {
            $sources = $directives[$directive] ?? [];
            if (!empty($sources)) {
                $parts[] = $directive . ' ' . implode(' ', $sources);
            }
        }
        return implode('; ', $parts);
    }
}
