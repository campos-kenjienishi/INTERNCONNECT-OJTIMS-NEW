<?php

namespace Tests\Feature;

use App\Services\IdpService;
use Mockery;
use Tests\TestCase;

class AuthenticationHardeningTest extends TestCase
{
    public function test_guisis_credentials_do_not_have_nonempty_source_defaults(): void
    {
        $files = [
            app_path('Services/GuidanceApiService.php'),
            config_path('services.php'),
            base_path('.env.example'),
        ];

        foreach ($files as $file) {
            $this->assertDoesNotMatchRegularExpression(
                "/GUISIS_(?:API_KEY|CLIENT_ID|CLIENT_SECRET)',\\s*'[^']+'/",
                (string) file_get_contents($file)
            );
        }

        foreach (['GuidanceApiService.php', 'PuptasApiService.php'] as $service) {
            $this->assertStringNotContainsString(
                "withOptions(['verify' => false])",
                (string) file_get_contents(app_path('Services/'.$service))
            );
        }
    }

    public function test_idp_redirect_includes_and_stores_oauth_state(): void
    {
        config()->set('services.idp.enforce_state', true);

        $idp = Mockery::mock(IdpService::class);
        $idp->shouldReceive('getAuthorizeUrl')
            ->once()
            ->with(Mockery::on(fn ($state) => is_string($state) && strlen($state) === 64))
            ->andReturnUsing(fn ($state) => 'https://identity-provider.example/authorize?state='.$state);
        $this->app->instance(IdpService::class, $idp);

        $response = $this->get('/auth/redirect-to-idp');

        $state = session('idp_oauth_state');
        $this->assertIsString($state);
        $this->assertSame(64, strlen($state));
        $response->assertRedirect('https://identity-provider.example/authorize?state='.$state);
    }

    public function test_idp_callback_rejects_a_mismatched_oauth_state(): void
    {
        config()->set('services.idp.enforce_state', true);

        $this->withSession(['idp_oauth_state' => 'expected-state'])
            ->get('/auth/callback?code=authorization-code&state=wrong-state')
            ->assertOk()
            ->assertSee('The authentication response could not be verified.');

        $this->assertNull(session('idp_oauth_state'));
    }
}
