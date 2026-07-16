<?php

namespace App\Services\Payments;

use App\Models\PaymentGateway;
use App\Services\Payments\DTOs\FieldDefinition;
use App\Services\Payments\DTOs\GatewayDefinition;

class PaymentGatewayRegistry
{
    private array $definitions = [];

    private bool $loaded = false;

    public function all(): array
    {
        $this->load();

        return $this->definitions;
    }

    public function find(string $key): ?GatewayDefinition
    {
        $this->load();

        return $this->definitions[$key] ?? null;
    }

    public function categories(): array
    {
        return [
            'online' => __('super-admin.online_payment'),
            'wallet' => __('super-admin.wallet'),
            'bank_transfer' => __('super-admin.bank_transfer'),
            'cash' => __('super-admin.cash'),
            'delivery' => __('super-admin.delivery'),
            'crypto' => __('super-admin.crypto'),
            'internal' => __('super-admin.internal'),
            'custom' => __('super-admin.custom'),
        ];
    }

    public function byCategory(string $category): array
    {
        return array_filter($this->all(), fn (GatewayDefinition $g) => $g->category === $category);
    }

    private function load(): void
    {
        if ($this->loaded) {
            return;
        }

        $this->definitions = [];

        foreach (PaymentGateway::ordered()->get() as $row) {
            $this->definitions[$row->key] = new GatewayDefinition(
                key: $row->key,
                name: $row->name,
                category: $row->category,
                description: $row->description ?? '',
                icon: $row->icon ?? 'bi-credit-card',
                supportedCurrencies: $row->supported_currencies ?? ['DZD'],
                sandbox: $row->sandbox ?? false,
                webhook: $row->webhook ?? false,
                enabledByDefault: true,
                fields: $this->hydrateFields($row->fields ?? []),
            );
        }

        $this->loaded = true;
    }

    private function hydrateFields(array $fields): array
    {
        return array_map(fn (array $f) => FieldDefinition::fromArray($f), $fields);
    }
}
