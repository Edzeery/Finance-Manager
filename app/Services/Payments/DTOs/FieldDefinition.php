<?php

// app\Services\Payments\DTOs\FieldDefinition.php

namespace App\Services\Payments\DTOs;

class FieldDefinition
{
    public function __construct(
        public readonly string $key,
        public readonly string $type,
        public readonly string $label,
        public readonly bool $required = false,
        public readonly mixed $default = null,
        public readonly ?string $placeholder = null,
        public readonly ?string $help = null,
        public readonly array $options = [],
        public readonly array $rules = [],
        public readonly bool $encrypted = false,
        public readonly bool $sensitive = false,
        public readonly ?int $maxLength = null,
        public readonly ?int $minLength = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'],
            type: $data['type'] ?? 'text',
            label: $data['label'] ?? $data['key'],
            required: $data['required'] ?? false,
            default: $data['default'] ?? null,
            placeholder: $data['placeholder'] ?? null,
            help: $data['help'] ?? null,
            options: $data['options'] ?? [],
            rules: $data['rules'] ?? [],
            encrypted: $data['encrypted'] ?? false,
            sensitive: $data['sensitive'] ?? false,
            maxLength: $data['maxLength'] ?? null,
            minLength: $data['minLength'] ?? null,
        );
    }

    public function validationRules(): array
    {
        $rules = $this->rules;

        if ($this->required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        if ($this->type === 'url') {
            $rules[] = 'url';
        } elseif ($this->type === 'email') {
            $rules[] = 'email';
        } elseif ($this->type === 'number' || $this->type === 'integer') {
            $rules[] = $this->type === 'number' ? 'numeric' : 'integer';
        } elseif ($this->type === 'boolean') {
            $rules[] = 'boolean';
        }

        if ($this->maxLength) {
            $rules[] = "max:{$this->maxLength}";
        }
        if ($this->minLength) {
            $rules[] = "min:{$this->minLength}";
        }

        if ($this->type === 'select' && ! empty($this->options)) {
            $rules[] = 'in:'.implode(',', array_column($this->options, 'value'));
        }

        return $rules;
    }
}
