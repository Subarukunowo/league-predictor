<?php
$opts = [
    'http' => [
        'method' => 'GET',
        'header' => "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\n" .
                    "Accept-Language: en-US,en;q=0.9\r\n" .
                    "Cache-Control: max-age=0\r\n" .
                    "Connection: keep-alive\r\n" .
                    "Host: www.football-data.co.uk\r\n" .
                    "Upgrade-Insecure-Requests: 1\r\n" .
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36\r\n"
    ]
];

$context = stream_context_create($opts);
$result = file_get_contents('https://www.football-data.co.uk/mmz4281/2425/E0.csv', false, $context);
echo substr($result, 0, 100);
