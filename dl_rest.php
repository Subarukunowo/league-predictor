<?php
$urls = [
    'https://www.football-data.co.uk/mmz4281/2425/D1.csv' => 'storage/app/bundesliga.csv',
    'https://www.football-data.co.uk/mmz4281/2425/I1.csv' => 'storage/app/serie_a.csv'
];

foreach ($urls as $url => $file) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($code == 200 && strpos($result, '404 Not Found') === false) {
        file_put_contents($file, $result);
        echo "Saved $file (" . strlen($result) . " bytes)\n";
    } else {
        echo "Failed $url (Code: $code)\n";
    }
}
