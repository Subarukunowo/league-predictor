<?php
$md = file_get_contents('C:\Users\ASUS\.gemini\antigravity-ide\brain\b1605f8d-7670-4818-8e4b-2763ca372fd7\.system_generated\steps\116\content.md');
$parts = explode("---", $md);
$csv = trim($parts[1]);
file_put_contents('storage/app/premier_league.csv', $csv);

$md = file_get_contents('C:\Users\ASUS\.gemini\antigravity-ide\brain\b1605f8d-7670-4818-8e4b-2763ca372fd7\.system_generated\steps\183\content.md');
$parts = explode("---", $md);
$csv = trim($parts[1]);
file_put_contents('storage/app/la_liga.csv', $csv);

echo "Saved 2 CSV files";
