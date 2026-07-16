<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    protected $fillable = [
        'key', 'name', 'category', 'icon', 'description',
        'supported_currencies',
        'sandbox', 'webhook', 'sort_order', 'fields', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'sandbox' => 'boolean',
            'webhook' => 'boolean',
            'sort_order' => 'integer',
            'supported_currencies' => 'json',
            'fields' => 'json',
            'metadata' => 'json',
        ];
    }

    public function methods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class, 'key', 'key');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
