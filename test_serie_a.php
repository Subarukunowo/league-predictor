<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = new Illuminate\Http\Request();
$req->merge(['league' => 'serie_a']);
echo (new App\Http\Controllers\PredictionController)->getTeams($req)->getContent();
