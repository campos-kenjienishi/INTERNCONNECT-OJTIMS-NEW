<?php

namespace Tests\Feature;

use App\Http\Middleware\InjectProcessFeedback;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProcessFeedbackTest extends TestCase
{
    private function request(string $path = '/student/home'): Request
    {
        $request = Request::create($path);
        $session = new Store('feedback-test', new ArraySessionHandler(120));
        $session->start();
        $request->setLaravelSession($session);
        return $request;
    }

    public function test_html_receives_shared_assets_and_safely_encoded_flash(): void
    {
        $request = $this->request();
        $request->session()->flash('success', 'Saved </script><script>alert(1)</script>');
        $response = (new InjectProcessFeedback)->handle($request,
            fn () => response('<html><head></head><body>Dashboard</body></html>', 200, ['Content-Type' => 'text/html']));
        $this->assertStringContainsString('process-feedback.css', $response->getContent());
        $this->assertStringContainsString('process-feedback.js', $response->getContent());
        $this->assertStringNotContainsString('Saved </script>', $response->getContent());
        $this->assertSame(1, substr_count($response->getContent(), 'id="ic-process-feedback-config"'));
        $again = (new InjectProcessFeedback)->handle($request, fn () => $response);
        $this->assertSame(1, substr_count($again->getContent(), 'id="ic-process-feedback-config"'));
    }

    public function test_json_fragments_prints_and_downloads_are_untouched(): void
    {
        foreach (['/student/home', '/professor/analytics/print', '/reports/export'] as $path) {
            $request = $this->request($path);
            $response = response()->json(['success' => true]);
            $this->assertSame($response, (new InjectProcessFeedback)->handle($request, fn () => $response));
        }
        foreach (['/professor/analytics/print', '/reports/export'] as $path) {
            $response = response('<html><head></head><body>Report</body></html>', 200, ['Content-Type' => 'text/html']);
            $result = (new InjectProcessFeedback)->handle($this->request($path), fn () => $response);
            $this->assertStringNotContainsString('process-feedback.js', $result->getContent());
        }
        $response = response('<div>Fragment</div>');
        $this->assertSame($response, (new InjectProcessFeedback)->handle($this->request(), fn () => $response));
        $response = response('download', 200, ['Content-Disposition' => 'attachment; filename="report.html"']);
        $this->assertSame($response, (new InjectProcessFeedback)->handle($this->request(), fn () => $response));
    }

    private function uploadRequest(): Request
    {
        $request = $this->request('/uploadReq');
        $request->setMethod('POST');
        $request->headers->set('X-InternConnect-Upload', '1');
        $request->files->set('file', UploadedFile::fake()->create('requirement.pdf', 10));
        return $request;
    }

    public function test_upload_success_is_confirmed_from_new_server_flash(): void
    {
        $request = $this->uploadRequest();
        $response = (new InjectProcessFeedback)->handle($request, function () use ($request) {
            $request->session()->flash('success', 'File uploaded successfully!');
            return redirect('/student/files');
        });
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true)['success']);
        $this->assertStringEndsWith('/student/files', $response->getData(true)['redirect']);
    }

    public function test_upload_redirect_without_success_never_reports_success(): void
    {
        $request = $this->uploadRequest();
        $request->session()->put('success', 'Old success from another request');
        $response = (new InjectProcessFeedback)->handle($request, fn () => redirect('/login'));
        $this->assertSame(409, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
    }

    public function test_upload_contract_preserves_laravel_put_method_override(): void
    {
        $request = $this->uploadRequest();
        $request->setMethod('PUT');
        $response = (new InjectProcessFeedback)->handle($request, function () use ($request) {
            $this->assertSame('PUT', $request->method());
            $request->session()->flash('success', 'MOA updated successfully.');
            return redirect('/student/company');
        });
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true)['success']);
    }

    public function test_dev_upload_and_renewal_flash_keys_are_supported(): void
    {
        foreach (['upload_success' => true, 'renewal_success' => true, 'upload_error' => false, 'renewal_error' => false] as $key => $success) {
            $request = $this->uploadRequest();
            $response = (new InjectProcessFeedback)->handle($request, function () use ($request, $key) {
                $request->session()->flash($key, 'Document processing result');
                return redirect('/student/company');
            });
            $this->assertSame($success, $response->getData(true)['success']);
            $this->assertSame($success ? 200 : 422, $response->getStatusCode());
        }
    }

    public function test_upload_validation_and_business_failures_preserve_error_messages(): void
    {
        $request = $this->uploadRequest();
        $response = (new InjectProcessFeedback)->handle($request, function () {
            throw ValidationException::withMessages(['file' => ['Only PDF files are accepted.']]);
        });
        $this->assertSame(422, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertSame('Only PDF files are accepted.', $response->getData(true)['message']);
        $response = (new InjectProcessFeedback)->handle($request, function () use ($request) {
            $request->session()->flash('fail', 'Requirement already submitted.');
            return redirect('/student/files');
        });
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('Requirement already submitted.', $response->getData(true)['message']);
    }

    public function test_regular_forms_keep_their_redirect_and_validation_behavior(): void
    {
        $request = $this->request('/student/edit');
        $request->setMethod('POST');
        $redirect = redirect('/student/account');
        $this->assertSame($redirect, (new InjectProcessFeedback)->handle($request, fn () => $redirect));
        $this->expectException(ValidationException::class);
        (new InjectProcessFeedback)->handle($request, function () {
            throw ValidationException::withMessages(['email' => 'Invalid email.']);
        });
    }
}
