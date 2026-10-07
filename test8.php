<?php
$ch = curl_init('https://corsproxy.io/?https%3A%2F%2Fwww.football-data.co.uk%2Fmmz4281%2F2425%2FE0.csv');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
echo substr(curl_exec($ch), 0, 100);
