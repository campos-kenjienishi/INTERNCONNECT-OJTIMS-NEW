<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ProductionExceptionHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.debug', false);

        Route::middleware('web')->post('/_test/production-validation', function (Request $request) {
            $request->validate(
                ['file' => 'required'],
                ['file.required' => 'Select a PDF file to upload.']
            );
        });

        Route::middleware('web')->get('/_test/production-error', function () {
            throw new RuntimeException('Internal details must remain hidden.');
        });
    }

    public function test_validation_failures_remain_redirects_in_production(): void
    {
        $this->from('/student/requirements')
            ->post('/_test/production-validation')
            ->assertRedirect('/student/requirements')
            ->assertSessionHasErrors(['file' => 'Select a PDF file to upload.']);
    }

    public function test_real_server_errors_use_the_branded_page_in_production(): void
    {
        $this->get('/_test/production-error')
            ->assertStatus(500)
            ->assertSee('Something went wrong.')
            ->assertDontSee('Internal details must remain hidden.');
    }
}
