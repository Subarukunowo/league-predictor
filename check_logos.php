<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logos = json_decode(file_get_contents('public/logos.json'), true);
$c = new App\Http\Controllers\PredictionController;
foreach(['premier_league','la_liga','bundesliga','serie_a'] as $l) {
    $req = new Illuminate\Http\Request();
    $req->merge(['league' => $l]);
    $res = $c->getTeams($req)->getContent();
    $d = json_decode($res, true);
    if (!$d) { echo "Failed to load $l\n"; continue; }
    
    foreach($d['teams'] as $t) {
        if(!isset($logos[$t['name']])) {
            echo "Missing: " . $t['name'] . "\n";
        } elseif (strpos($logos[$t['name']], 'ui-avatars.com') !== false) {
            echo "UI-Avatar: " . $t['name'] . "\n";
        }
    }
}
