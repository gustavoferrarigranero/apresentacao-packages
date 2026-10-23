<?php

namespace Tests\Feature;

use App\Models\Feedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class FeedbackWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_is_available(): void
    {
        $this->getJson('/api/health')
            ->assertOk()
            ->assertJsonPath('package', 'acme/laravel-feedback-widget');
    }

    public function test_demo_renders_the_package_component(): void
    {
        $this->get('/demo/feedback')
            ->assertOk()
            ->assertSee('feedbackWidget', false)
            ->assertSee('Como foi sua experiência?');
    }

    public function test_feedback_is_validated_and_stored(): void
    {
        $this->postJson('/feedback', [
            'rating' => 5,
            'message' => 'A experiência do curso foi muito clara.',
            'email' => 'aluno@example.com',
        ])->assertCreated()
            ->assertJsonPath('message', 'Obrigado pelo feedback!');

        $this->assertDatabaseHas(Feedback::class, [
            'rating' => 5,
            'email' => 'aluno@example.com',
        ]);
    }

    public function test_invalid_feedback_returns_validation_errors(): void
    {
        $this->postJson('/feedback', [
            'rating' => 9,
            'message' => 'curta',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['rating', 'message']);
    }

    public function test_application_can_override_package_configuration(): void
    {
        config()->set('feedback-widget.title', 'Conte como foi a aula');

        $this->get('/demo/feedback')
            ->assertOk()
            ->assertSee('Conte como foi a aula');
    }

    public function test_published_javascript_registers_the_alpine_component(): void
    {
        $asset = public_path('vendor/feedback-widget/feedback-widget.js');

        $this->assertFileExists($asset);
        $this->assertStringContainsString("Alpine.data('feedbackWidget'", file_get_contents($asset));
    }
}
