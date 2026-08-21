<?php

if (!function_exists('vasset')) {
    /**
     * Generate an asset URL with an automated cache-busting version query string.
     *
     * @param string $path Relative path to asset in the public/ directory
     * @return string Full asset URL with ?v=<filemtime>
     */
    function vasset(string $path): string
    {
        $cleanPath = ltrim($path, '/');
        $fullPath = public_path($cleanPath);

        if (file_exists($fullPath)) {
            return asset($cleanPath) . '?v=' . filemtime($fullPath);
        }

        return asset($cleanPath);
    }
}
