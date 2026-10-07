<?php
$logos = json_decode(file_get_contents('public/logos.json'), true);
echo "Bayern: " . $logos['FC Bayern München'] . "\n";
echo "Dortmund: " . $logos['Borussia Dortmund'] . "\n";
echo "Leverkusen: " . $logos['Bayer 04 Leverkusen'] . "\n";
