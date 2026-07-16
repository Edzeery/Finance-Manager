<?php

namespace App\Services\Payments;

class ValidationResult
{
    public function __construct(
        public readonly bool $passes,
        public readonly array $messages = [],
    ) {}

    public static function valid(): self
    {
        return new self(true);
    }

    public static function invalid(string ...$messages): self
    {
        return new self(false, $messages);
    }

    public function fails(): bool
    {
        return ! $this->passes;
    }

    public function message(): string
    {
        return implode(' ', $this->messages);
    }
}
