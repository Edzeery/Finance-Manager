<?php

use Edzeery\MyStatusKit\Facades\Status;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
);

try {
    echo 'payment.paid: '.__('status-kit::statuses.payment.paid')."\n";
} catch (Throwable $e) {
    echo 'payment.paid Error: '.$e->getMessage()."\n";
}

try {
    echo 'verification.pending: '.__('status-kit::statuses.verification.pending')."\n";
} catch (Throwable $e) {
    echo 'verification.pending Error: '.$e->getMessage()."\n";
}

try {
    echo 'goal.in_progress: '.__('status-kit::statuses.goal.in_progress')."\n";
} catch (Throwable $e) {
    echo 'goal.in_progress Error: '.$e->getMessage()."\n";
}

try {
    echo 'asset.cash: '.__('status-kit::statuses.asset.cash')."\n";
} catch (Throwable $e) {
    echo 'asset.cash Error: '.$e->getMessage()."\n";
}

// Check the label() behavior
try {
    $result = Status::for('payment', 'paid');
    echo 'label(): '.$result->label()."\n";
} catch (Throwable $e) {
    echo 'label() Error: '.$e->getMessage()."\n";
}

try {
    $result = Status::for('verification', 'pending');
    echo 'verification label(): '.$result->label()."\n";
} catch (Throwable $e) {
    echo 'verification label() Error: '.$e->getMessage()."\n";
}

try {
    $result = Status::for('goal', 'in_progress');
    echo 'goal label(): '.$result->label()."\n";
} catch (Throwable $e) {
    echo 'goal label() Error: '.$e->getMessage()."\n";
}

try {
    $result = Status::for('debt_type', 'owed');
    echo 'debt_type label(): '.$result->label()."\n";
} catch (Throwable $e) {
    echo 'debt_type label() Error: '.$e->getMessage()."\n";
}

// Check the vendor namespace paths
$translator = app('translator');
$loader = $translator->getLoader();
if (method_exists($loader, 'getPaths')) {
    echo 'Loader paths: '.print_r($loader->getPaths(), true)."\n";
}
if (method_exists($loader, 'namespaces')) {
    echo 'Namespaces: '.print_r($loader->namespaces(), true)."\n";
}

echo "\nLang path: ".app()->langPath()."\n";
echo 'Vendor lang path: '.app()->langPath('vendor/status-kit')."\n";
echo 'Exists en: '.(is_file(app()->langPath('vendor/status-kit/en/statuses.php')) ? 'YES' : 'NO')."\n";
echo 'Exists ar: '.(is_file(app()->langPath('vendor/status-kit/ar/statuses.php')) ? 'YES' : 'NO')."\n";
