<?php
$logos = json_decode(file_get_contents('public/logos.json'), true);
$logos['Málaga CF'] = 'https://r2.thesportsdb.com/images/media/team/badge/v1j0m91627404481.png';
file_put_contents('public/logos.json', json_encode($logos, JSON_PRETTY_PRINT));
