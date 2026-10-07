<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://www.football-data.co.uk/mmz4281/2526/E0.csv");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$result = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "Status: $code\n";
echo substr($result, 0, 100);
curl_close($ch);
