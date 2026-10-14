<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InjectProcessFeedback
{
    public function handle(Request $request, Closure $next)
    {
        // Only the shared upload client opts into this redirect-to-JSON contract.
        $upload = $request->header('X-InternConnect-Upload') === '1'
            && in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)
            && count($request->allFiles()) > 0;

        try {
            $response = $next($request);
        } catch (ValidationException $exception) {
            if (!$upload) {
                throw $exception;
            }
            return response()->json([
                'success' => false,
                'message' => collect($exception->errors())->flatten()->first(),
                'errors' => $exception->errors(),
            ], 422);
        }

        if ($upload && $response instanceof RedirectResponse) {
            $flash = $this->flash($request, true);
            if ($flash) {
                return response()->json([
                    'success' => $flash['type'] === 'success',
                    'message' => $flash['message'],
                    'redirect' => $response->getTargetUrl(),
                ], $flash['type'] === 'success' ? 200 : 422);
            }
            // A redirect alone is not proof that an upload succeeded (e.g. login).
            return response()->json([
                'success' => false,
                'message' => 'The upload was not confirmed. Please check your session and try again.',
                'redirect' => $response->getTargetUrl(),
            ], 409);
        }

        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse
            || $response instanceof RedirectResponse
            || stripos((string) $response->headers->get('Content-Type'), 'text/html') === false
            || $response->headers->has('Content-Disposition')
            || $request->is('voucher*', '*/voucher*', '*print*', '*export*', 'view/file*')
            || $request->routeIs('*.print', '*.export')) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || stripos($content, '</head>') === false
            || str_contains($content, 'id="ic-process-feedback-config"')) {
            return $response;
        }

        $config = json_encode(['flash' => $this->flash($request)],
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        $assets = '<link rel="stylesheet" href="'.e(vasset('css/components/process-feedback.css')).'">'
            .'<script type="application/json" id="ic-process-feedback-config">'.$config.'</script>'
            .'<script src="'.e(vasset('js/components/process-feedback.js')).'"></script>';
        $response->setContent(preg_replace('/<\/head>/i', $assets.PHP_EOL.'</head>', $content, 1));
        $response->headers->remove('Content-Length');

        return $response;
    }

    private function flash(Request $request, bool $newOnly = false): ?array
    {
        if (!$request->hasSession()) {
            return null;
        }
        $session = $request->session();
        $newKeys = $session->get('_flash.new', []);
        foreach (['fail' => 'error', 'error' => 'error', 'upload_error' => 'error', 'renewal_error' => 'error',
            'success' => 'success', 'upload_success' => 'success', 'renewal_success' => 'success'] as $key => $type) {
            if ($newOnly && !in_array($key, $newKeys, true)) {
                continue;
            }
            $message = $session->get($key);
            if (is_string($message) && trim($message) !== '') {
                return ['type' => $type, 'message' => $message];
            }
        }
        $errors = $session->get('errors');
        if ((!$newOnly || in_array('errors', $newKeys, true)) && $errors instanceof \Illuminate\Support\ViewErrorBag
            && $errors->any()) {
            return ['type' => 'error', 'message' => $errors->first()];
        }
        return null;
    }
}
