<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.football-data.co.uk/mmz4281/2425/E0.csv');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);

$csv = array_map("str_getcsv", explode("\n", trim($response)));
$headers = array_shift($csv);
$data = [];
echo "Headers count: " . count($headers) . "\n";
echo "Headers array: "; print_r($headers);
foreach ($csv as $i => $row) {
    if ($i < 2) {
        echo "Row count: " . count($row) . "\n";
        echo "Row array: "; print_r($row);
    }
    if (is_array($row) && count($headers) == count($row) && !empty($row[0])) {
        $data[] = array_combine($headers, $row);
    }
}
echo "Data count: " . count($data) . "\n";
