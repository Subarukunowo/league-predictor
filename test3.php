<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$response = Illuminate\Support\Facades\Http::withHeaders([
    'User-Agent' => 'Mozilla/5.0'
])->withoutVerifying()->get('https://www.football-data.co.uk/mmz4281/2627/E0.csv');
echo $response->status() . "\n";
echo substr($response->body(), 0, 100);
