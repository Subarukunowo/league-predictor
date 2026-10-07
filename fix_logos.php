<?php
$file = __DIR__ . '/public/logos.json';
if (!file_exists($file)) die("No file\n");
$logos = json_decode(file_get_contents($file), true);

$overrides = [
    'AFC Bournemouth' => 'https://r2.thesportsdb.com/images/media/team/badge/gh5hc31761433174.png',
    'FC Barcelona' => 'https://r2.thesportsdb.com/images/media/team/badge/xqwpup1473502878.png',
    'Real Madrid CF' => 'https://r2.thesportsdb.com/images/media/team/badge/xyqysq1473504825.png',
    'FC Bayern München' => 'https://r2.thesportsdb.com/images/media/team/badge/z2r3eh1678017187.png',
    '1. FC Köln' => 'https://r2.thesportsdb.com/images/media/team/badge/ziv57e1724245648.png',
    '1. FC Union Berlin' => 'https://r2.thesportsdb.com/images/media/team/badge/yqsqsu1523707010.png',
    '1. FSV Mainz 05' => 'https://r2.thesportsdb.com/images/media/team/badge/yqtwvu1451554988.png',
    'Bayer 04 Leverkusen' => 'https://r2.thesportsdb.com/images/media/team/badge/qvvpqv1420799797.png',
    'Borussia Dortmund' => 'https://r2.thesportsdb.com/images/media/team/badge/tqo8ge1716960353.png',
    'Borussia Mönchengladbach' => 'https://r2.thesportsdb.com/images/media/team/badge/xptsqx1420799292.png',
    'Eintracht Frankfurt' => 'https://r2.thesportsdb.com/images/media/team/badge/tqtxpt1420803565.png',
    'FC Augsburg' => 'https://r2.thesportsdb.com/images/media/team/badge/twuqts1420799650.png',
    'Hamburger SV' => 'https://r2.thesportsdb.com/images/media/team/badge/twuuus1420803588.png',
    'RB Leipzig' => 'https://r2.thesportsdb.com/images/media/team/badge/xqtwus1472911762.png',
    'SC Freiburg' => 'https://r2.thesportsdb.com/images/media/team/badge/wrqrss1420799689.png',
    'SC Paderborn 07' => 'https://r2.thesportsdb.com/images/media/team/badge/tvupvy1420799719.png',
    'SV 07 Elversberg' => 'https://r2.thesportsdb.com/images/media/team/badge/3b88b21685810237.png',
    'SV Werder Bremen' => 'https://r2.thesportsdb.com/images/media/team/badge/swqtwq1420799516.png',
    'TSG 1899 Hoffenheim' => 'https://r2.thesportsdb.com/images/media/team/badge/wqtqwq1420803623.png',
    'VfB Stuttgart' => 'https://r2.thesportsdb.com/images/media/team/badge/5k1k9r1705609462.png',
    'ACF Fiorentina' => 'https://r2.thesportsdb.com/images/media/team/badge/vwwruy1448806584.png',
    'FC Internazionale Milano' => 'https://r2.thesportsdb.com/images/media/team/badge/ryhu6d1617113103.png',
    'SSC Napoli' => 'https://r2.thesportsdb.com/images/media/team/badge/l8qyxv1742982541.png',
    'US Sassuolo Calcio' => 'https://r2.thesportsdb.com/images/media/team/badge/xystvp1448806138.png',
    'AS Roma' => 'https://r2.thesportsdb.com/images/media/team/badge/jwro2s1760820674.png',
    'SS Lazio' => 'https://r2.thesportsdb.com/images/media/team/badge/rwqyvs1448806608.png',
    'AC Milan' => 'https://r2.thesportsdb.com/images/media/team/badge/31vu4p1705226276.png',
    'Juventus FC' => 'https://r2.thesportsdb.com/images/media/team/badge/uxf0gr1742983727.png',
    'Cagliari Calcio' => 'https://r2.thesportsdb.com/images/media/team/badge/wvsvxt1447534471.png',
    'Frosinone Calcio' => 'https://r2.thesportsdb.com/images/media/team/badge/a7xa151603170120.png',
    'Como 1907' => 'https://r2.thesportsdb.com/images/media/team/badge/02x81t1627405841.png',
    'US Lecce' => 'https://r2.thesportsdb.com/images/media/team/badge/j4vznr1567365249.png',
    'Udinese Calcio' => 'https://r2.thesportsdb.com/images/media/team/badge/vwvstr1448806811.png',
    'Torino FC' => 'https://r2.thesportsdb.com/images/media/team/badge/xxprty1448806802.png',
    'Parma Calcio 1913' => 'https://r2.thesportsdb.com/images/media/team/badge/6yiaxs1627406063.png',
    'AC Monza' => 'https://r2.thesportsdb.com/images/media/team/badge/bxearg1603170113.png',
    'Bologna FC 1909' => 'https://r2.thesportsdb.com/images/media/team/badge/2qi1u31655592366.png',
    'Genoa CFC' => 'https://r2.thesportsdb.com/images/media/team/badge/52s8dn1655553600.png',
    'Venezia FC' => 'https://r2.thesportsdb.com/images/media/team/badge/vbiget1781026964.png',
    'Atalanta BC' => 'https://r2.thesportsdb.com/images/media/team/badge/qix5ku1780561327.png',
    'Real Sociedad de Fútbol' => 'https://r2.thesportsdb.com/images/media/team/badge/wqtqwq1473504856.png',
    'Athletic Club' => 'https://r2.thesportsdb.com/images/media/team/badge/wqwwsw1473502845.png',
    'CA Osasuna' => 'https://r2.thesportsdb.com/images/media/team/badge/wsvtvx1473504812.png',
    'RC Celta de Vigo' => 'https://r2.thesportsdb.com/images/media/team/badge/ssuwtt1473504780.png',
    'Elche CF' => 'https://r2.thesportsdb.com/images/media/team/badge/wqrpvv1473504791.png',
    'Levante UD' => 'https://r2.thesportsdb.com/images/media/team/badge/ssttsu1473504801.png',
    'Valencia CF' => 'https://r2.thesportsdb.com/images/media/team/badge/uqutsq1473504877.png',
    'Club Atlético de Madrid' => 'https://r2.thesportsdb.com/images/media/team/badge/62q0r41724244583.png',
    'Real Betis Balompié' => 'https://r2.thesportsdb.com/images/media/team/badge/vqvsst1473504838.png',
    'Deportivo Alavés' => 'https://r2.thesportsdb.com/images/media/team/badge/mfn99h1734673842.png',
    'RC Deportivo La Coruña' => 'https://r2.thesportsdb.com/images/media/team/badge/txruqs1473504764.png',
];

foreach ($overrides as $team => $url) {
    $logos[$team] = $url;
}

file_put_contents($file, json_encode($logos, JSON_PRETTY_PRINT));
echo "Overrides applied\n";
