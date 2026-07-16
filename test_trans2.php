<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$response = $kernel->handle(Request::capture());

use Edzeery\MyStatusKit\Facades\Status;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

try {
    echo 'payment.paid label: '.Status::for('payment', 'paid')->label()."\n";
} catch (Throwable $e) {
    echo 'payment.paid Error: '.$e->getMessage()."\n";
}

try {
    echo 'verification.pending label: '.Status::for('verification', 'pending')->label()."\n";
} catch (Throwable $e) {
    echo 'verification.pending Error: '.$e->getMessage()."\n";
}

try {
    echo 'goal.in_progress label: '.Status::for('goal', 'in_progress')->label()."\n";
} catch (Throwable $e) {
    echo 'goal.in_progress Error: '.$e->getMessage()."\n";
}

try {
    echo 'debt.active label: '.Status::for('debt', 'active')->label()."\n";
} catch (Throwable $e) {
    echo 'debt.active Error: '.$e->getMessage()."\n";
}

try {
    echo 'asset.cash label: '.Status::for('asset', 'cash')->label()."\n";
} catch (Throwable $e) {
    echo 'asset.cash Error: '.$e->getMessage()."\n";
}

try {
    echo 'general.yes label: '.Status::for('general', 'yes')->label()."\n";
} catch (Throwable $e) {
    echo 'general.yes Error: '.$e->getMessage()."\n";
}

try {
    echo 'nonexistent.foo label: '.Status::for('nonexistent', 'foo')->label()."\n";
} catch (Throwable $e) {
    echo 'nonexistent.foo Error: '.$e->getMessage()."\n";
}

echo "\n--- badge() test ---\n";
echo Status::for('payment', 'paid')->badge('bi')."\n";
echo Status::for('verification', 'pending')->badge('bi')."\n";
