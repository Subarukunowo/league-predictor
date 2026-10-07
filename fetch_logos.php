<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$leagues = ['en.1', 'es.1', 'de.1', 'it.1'];
$season = '2026-27';
$logos = [];

if (file_exists(__DIR__ . '/public/logos.json')) {
    $logos = json_decode(file_get_contents(__DIR__ . '/public/logos.json'), true);
}

foreach ($leagues as $code) {
    $url = "https://raw.githubusercontent.com/openfootball/football.json/master/{$season}/{$code}.json";
    $response = Http::withoutVerifying()->get($url);
    if (!$response->successful()) continue;
    
    $json = $response->json();
    $teams = [];
    if (isset($json['matches'])) {
        foreach ($json['matches'] as $match) {
            $teams[$match['team1']] = true;
            $teams[$match['team2']] = true;
        }
    }
    
    foreach (array_keys($teams) as $teamName) {
        if (isset($logos[$teamName]) && strpos($logos[$teamName], 'ui-avatars.com') === false) {
            continue; // Already have a good logo
        }
        
        $cleanName = trim(str_replace(
            [' FC', 'FC ', ' AFC', ' AC ', 'AC ', ' BC', ' ACF ', 'ACF ', ' AS ', 'AS ', ' 1909', ' Calcio', ' 1907', ' 1913', ' SS ', 'SS ', 'SSC ', ' US ', 'US ', ' CFC', 'Internazionale Milano', ' 04', ' 05', ' 07', ' 1899', ' SV', 'SV ', '1. '], 
            ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Inter Milan', '', '', '', '', '', '', ''], 
            $teamName
        ));
        
        echo "Fetching logo for: $teamName (Clean: $cleanName)...\n";
        
        $url = "https://www.thesportsdb.com/api/v1/json/3/searchteams.php?t=" . urlencode($cleanName);
        $res = Http::withoutVerifying()->get($url);
        
        if ($res->successful()) {
            $data = $res->json();
            if (isset($data['teams'][0]['strBadge'])) {
                $logos[$teamName] = $data['teams'][0]['strBadge'];
                echo "Found: " . $logos[$teamName] . "\n";
            } else {
                echo "Not found!\n";
                $logos[$teamName] = "https://ui-avatars.com/api/?name=".urlencode($teamName)."&background=random&color=fff&size=128&font-size=0.4";
            }
        }
        
        // Save incrementally
        file_put_contents(__DIR__ . '/public/logos.json', json_encode($logos, JSON_PRETTY_PRINT));
        sleep(1); // Sleep to avoid rate limits
    }
}
echo "Done!\n";
