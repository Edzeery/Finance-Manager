<?php

namespace App\Http\Controllers\Concerns;

trait HasBreadcrumbs
{
    protected array $breadcrumbs = [];

    protected function addBreadcrumb(string $label, ?string $url = null, ?string $icon = null): self
    {
        $this->breadcrumbs[] = [
            'label' => $label,
            'url' => $url,
            'icon' => $icon,
        ];

        return $this;
    }

    protected function homeBreadcrumb(): self
    {
        return $this->addBreadcrumb(__('general.dashboard'), route('dashboard'), 'bi-house');
    }

    protected function resourceBreadcrumbs(string $resourceName, string $resourceRoute, ?string $icon = null): self
    {
        $this->homeBreadcrumb();

        return $this->addBreadcrumb(__($resourceName), route($resourceRoute), $icon);
    }

    protected function withBreadcrumbs(array $data = []): array
    {
        return array_merge(['breadcrumb' => $this->breadcrumbs], $data);
    }

    protected function breadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    protected function resetBreadcrumbs(): self
    {
        $this->breadcrumbs = [];

        return $this;
    }
}
