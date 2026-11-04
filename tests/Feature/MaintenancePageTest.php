<?php

namespace Tests\Feature;

use Illuminate\Contracts\Foundation\MaintenanceMode;
use Mockery;
use Tests\TestCase;

class MaintenancePageTest extends TestCase
{
    public function test_maintenance_page_is_self_contained(): void
    {
        $content = view('errors.503')->render();

        $this->assertStringContainsString('Temporarily Unavailable | InternConnect', $content);
        $this->assertStringContainsString('width=device-width', $content);
        $this->assertStringContainsString('noindex, nofollow', $content);
        $this->assertStringNotContainsString('<script', $content);
        $this->assertStringNotContainsString('<link', $content);
        $this->assertStringContainsString('src="data:image/png;base64,', $content);
        $this->assertStringContainsString('Bud, your OJT buddy, waving hello', $content);
        $this->assertStringContainsString('Hey, OJT buddies! A quick heads-up.', $content);
        $this->assertStringContainsString('grid-template-columns: 1fr', $content);
        $this->assertDoesNotMatchRegularExpression('/(?:src|href)=["\']https?:/i', $content);
    }

    public function test_prerendered_maintenance_blocks_public_and_portal_requests(): void
    {
        $this->simulateMaintenance(['template' => view('errors.503')->render(), 'status' => 503, 'retry' => 60]);

        foreach (['/', '/dashboard', '/student/requirements', '/student/completion-certificate', '/professor/completion-certificates', '/coordinator/completion-certificates'] as $path) {
            $this->get($path)->assertStatus(503)
                ->assertHeader('Retry-After', '60')
                ->assertSeeText('Temporary System Notice');
        }

        $this->post('/uploadReq')->assertStatus(503)->assertSeeText('Temporary System Notice');
    }

    public function test_non_prerendered_maintenance_keeps_the_custom_page_and_retry_header(): void
    {
        config(['app.debug' => false]);
        $this->simulateMaintenance(['status' => 503, 'retry' => 60]);

        $this->get('/')->assertStatus(503)
            ->assertHeader('Retry-After', '60')
            ->assertSeeText('Temporary System Notice')
            ->assertDontSeeText('Something went wrong.');
    }

    public function test_normal_access_is_unchanged_when_maintenance_is_disabled(): void
    {
        $maintenance = Mockery::mock(MaintenanceMode::class);
        $maintenance->shouldReceive('active')->andReturn(false);
        $this->app->instance(MaintenanceMode::class, $maintenance);

        $this->get('/')->assertOk()->assertDontSeeText('Temporary System Notice');
    }

    private function simulateMaintenance(array $data): void
    {
        $maintenance = Mockery::mock(MaintenanceMode::class);
        $maintenance->shouldReceive('active')->andReturn(true);
        $maintenance->shouldReceive('data')->andReturn($data);
        $this->app->instance(MaintenanceMode::class, $maintenance);
    }
}
