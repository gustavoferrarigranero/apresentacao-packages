<?php

namespace Acme\FeedbackWidget;

use Illuminate\Support\ServiceProvider;

final class FeedbackWidgetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/feedback-widget.php',
            'feedback-widget',
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            'feedback-widget',
        );

        $this->publishes([
            __DIR__.'/../config/feedback-widget.php' => config_path('feedback-widget.php'),
        ], 'feedback-widget-config');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/feedback-widget'),
        ], 'feedback-widget-assets');
    }
}
