<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Layanan;
echo json_encode(Layanan::orderBy('urutan', 'asc')->get()->toArray(), JSON_PRETTY_PRINT);
