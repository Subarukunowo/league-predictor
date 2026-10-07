<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$response = Illuminate\Support\Facades\Http::withHeaders([
    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
])->withoutVerifying()->get('https://www.football-data.co.uk/mmz4281/2425/E0.csv');

$csv = array_map("str_getcsv", explode("\n", trim($response->body())));
$headers = array_shift($csv);
$row = array_shift($csv);
echo "Headers count: " . count($headers) . "\n";
echo "Row count: " . count($row) . "\n";
if (count($headers) != count($row)) {
    echo "Diff! headers last: '" . end($headers) . "', row last: '" . end($row) . "'\n";
}
