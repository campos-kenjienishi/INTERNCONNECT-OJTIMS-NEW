<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class InjectAccessibilityWidget
{
    /**
     * Inject the accessibility widget script into HTML responses.
     */
    public function handle(Request $request, Closure $next)
    {
        $routeName = optional($request->route())->getName();
        $path = $request->path();

        if (
            (is_string($routeName) && str_ends_with($routeName, '.print')) ||
            (is_string($path) && str_contains($path, '/print'))
        ) {
            return $next($request);
        }

        $response = $next($request);

        $contentType = (string) $response->headers->get('Content-Type', '');
        if (stripos($contentType, 'text/html') === false) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || $content === '') {
            return $response;
        }

        if (stripos($content, 'sienna-accessibility.umd.js') !== false) {
            return $response;
        }

        $script = '<script src="https://cdn.jsdelivr.net/npm/sienna-accessibility@latest/dist/sienna-accessibility.umd.js" data-asw-position="bottom-right" defer></script>';

        if (stripos($content, '</body>') !== false) {
            $updatedContent = preg_replace('/<\/body>/i', $script . PHP_EOL . '</body>', $content, 1);
            if ($updatedContent !== null) {
                $response->setContent($updatedContent);
            }
            return $response;
        }

        // Fallback for unusual HTML responses without a closing body tag.
        $response->setContent($content . PHP_EOL . $script);

        return $response;
    }
}
