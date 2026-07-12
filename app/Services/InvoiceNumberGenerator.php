<?php

namespace App\Services;

use App\Models\InvoiceSequence;
use Illuminate\Support\Facades\DB;

class InvoiceNumberGenerator
{
    public function generate(string $prefix = 'INV'): string
    {
        return DB::transaction(function () use ($prefix) {
            $sequence = InvoiceSequence::where('prefix', $prefix)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                $sequence = InvoiceSequence::create([
                    'prefix' => $prefix,
                    'last_number' => 1,
                ]);

                return $prefix . '-' . str_pad('1', 8, '0', STR_PAD_LEFT);
            }

            $sequence->increment('last_number');

            return $prefix . '-' . str_pad((string) $sequence->last_number, 8, '0', STR_PAD_LEFT);
        });
    }
}
