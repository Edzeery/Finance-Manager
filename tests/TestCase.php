<?php

namespace Tests;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;
use PHPUnit\Framework\Assert;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(PreventRequestForgery::class);

        $this->registerLivewireTestingMacros();
    }

    private function registerLivewireTestingMacros(): void
    {
        if (TestResponse::hasMacro('assertSeeLivewire')) {
            return;
        }

        TestResponse::macro('assertSeeLivewire', function ($component) {
            if (is_subclass_of($component, Component::class)) {
                $component = app(ComponentRegistry::class)->getName($component);
            }
            $escapedComponentName = trim(htmlspecialchars(json_encode(['name' => $component])), '{}');

            Assert::assertStringContainsString(
                $escapedComponentName,
                $this->getContent(),
                'Cannot find Livewire component ['.$component.'] rendered on page.'
            );

            return $this;
        });

        TestResponse::macro('assertDontSeeLivewire', function ($component) {
            if (is_subclass_of($component, Component::class)) {
                $component = app(ComponentRegistry::class)->getName($component);
            }
            $escapedComponentName = trim(htmlspecialchars(json_encode(['name' => $component])), '{}');

            Assert::assertStringNotContainsString(
                $escapedComponentName,
                $this->getContent(),
                'Found Livewire component ['.$component.'] rendered on page.'
            );

            return $this;
        });
    }
}
