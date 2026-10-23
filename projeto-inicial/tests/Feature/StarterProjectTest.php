<?php

namespace Tests\Feature;

use Acme\FeedbackWidget\FeedbackWidgetServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StarterProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_is_available(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('stage', 'starter');
    }

    public function test_demo_exposes_the_alpine_starting_point(): void
    {
        $this->get('/demo/feedback')
            ->assertOk()
            ->assertSee('Ponto de partida')
            ->assertSee('x-data="{ open: false }"', false);
    }

    public function test_package_provider_is_discovered_by_laravel(): void
    {
        $this->assertTrue(app()->providerIsLoaded(FeedbackWidgetServiceProvider::class));
    }

    public function test_lesson_artifacts_are_available(): void
    {
        $this->assertFileExists(app_path('Models/Feedback.php'));
        $this->assertFileExists(app_path('Http/Requests/StoreFeedbackRequest.php'));
        $this->assertFileExists(app_path('Http/Controllers/StoreFeedbackController.php'));
        $this->assertFileExists(base_path('packages/acme/laravel-feedback-widget/resources/views/components/widget.blade.php'));
    }
}
