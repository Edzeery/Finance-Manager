<?php

// app\Services\Payments\DTOs\GatewayDefinition.php

namespace App\Services\Payments\DTOs;

class GatewayDefinition
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $category,
        public readonly string $description,
        public readonly string $icon,
        public readonly array $supportedCurrencies,
        public readonly bool $sandbox,
        public readonly bool $webhook,
        public readonly bool $enabledByDefault,
        public readonly array $fields,
    ) {}

    public function requiredFields(): array
    {
        return array_filter($this->fields, fn (FieldDefinition $f) => $f->required);
    }

    public function optionalFields(): array
    {
        return array_filter($this->fields, fn (FieldDefinition $f) => ! $f->required);
    }

    public function encryptedFields(): array
    {
        return array_filter($this->fields, fn (FieldDefinition $f) => $f->encrypted);
    }

    public function field(string $key): ?FieldDefinition
    {
        foreach ($this->fields as $field) {
            if ($field->key === $key) {
                return $field;
            }
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description,
            'icon' => $this->icon,
            'supported_currencies' => $this->supportedCurrencies,
            'sandbox' => $this->sandbox,
            'webhook' => $this->webhook,
            'enabled_by_default' => $this->enabledByDefault,
            'fields' => array_map(fn (FieldDefinition $f) => [
                'key' => $f->key,
                'type' => $f->type,
                'label' => $f->label,
                'required' => $f->required,
                'default' => $f->default,
                'placeholder' => $f->placeholder,
                'help' => $f->help,
                'options' => $f->options,
                'encrypted' => $f->encrypted,
                'sensitive' => $f->sensitive,
            ], $this->fields),
        ];
    }
}
