<?php

namespace App\DTOs;

use Illuminate\Contracts\Support\Arrayable;

class SearchResult implements Arrayable
{
    public function __construct(
        public readonly int $id,
        public readonly string $type,
        public readonly ?string $description,
        public readonly float $amount,
        public readonly mixed $date,
        public readonly string $category,
        public readonly string $url,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'description' => $this->description,
            'amount' => $this->amount,
            'date' => $this->date,
            'category' => $this->category,
            'url' => $this->url,
        ];
    }
}
