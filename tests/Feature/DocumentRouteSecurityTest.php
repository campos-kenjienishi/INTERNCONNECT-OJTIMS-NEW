<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DocumentRouteSecurityTest extends TestCase
{
    public function test_document_routes_use_named_rate_limiters(): void
    {
        $this->assertContains(
            'throttle:document-downloads',
            Route::getRoutes()->getByName('coordinator.requirements.download')->gatherMiddleware()
        );
        $this->assertContains(
            'throttle:document-previews',
            Route::getRoutes()->getByName('coordinator.requirements.view')->gatherMiddleware()
        );
        $this->assertContains(
            'throttle:document-downloads',
            Route::getRoutes()->getByName('download.file')->gatherMiddleware()
        );
        $this->assertContains(
            'throttle:document-previews',
            Route::getRoutes()->getByName('moa.file.preview')->gatherMiddleware()
        );
        $this->assertContains(
            'throttle:document-previews',
            Route::getRoutes()->getByName('requirements.preview')->gatherMiddleware()
        );
    }
}
