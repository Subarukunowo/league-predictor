<?php
$logos = json_decode(file_get_contents('public/logos.json'), true);
$dir = __DIR__ . '/public/logos';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

foreach ($logos as $team => $url) {
    // Buat nama file aman
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($team)) . '.png';
    $filepath = $dir . '/' . $filename;
    
    // Download jika belum ada
    if (!file_exists($filepath)) {
        echo "Downloading $team ...\n";
        $ch = curl_init($url);
        $fp = fopen($filepath, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        sleep(1); // Mencegah diblokir
    }
    
    // Update logos.json dengan path lokal
    $logos[$team] = '/logos/' . $filename;
}

file_put_contents('public/logos.json', json_encode($logos, JSON_PRETTY_PRINT));
echo "Semua logo telah didownload dan disimpan ke public/logos!\n";
