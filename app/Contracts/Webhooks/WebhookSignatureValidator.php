<?php

namespace App\Contracts\Webhooks;

use Illuminate\Http\Request;

interface WebhookSignatureValidator
{
    public function validate(Request $request): bool;

    public function provider(): string;
}
