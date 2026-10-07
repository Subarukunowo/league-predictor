<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PredictionController extends Controller
{
    private $season = '2026-27';
    private $leagues = [
        'premier_league' => 'en.1',
        'la_liga' => 'es.1',
        'bundesliga' => 'de.1',
        'serie_a' => 'it.1'
    ];

    private function getLeagueData($leagueName)
    {
        $code = $this->leagues[$leagueName] ?? null;
        if (!$code) return null;

        $url = "https://raw.githubusercontent.com/openfootball/football.json/master/{$this->season}/{$code}.json";
        
        // Cache for only 1 minute to make it near real-time instead of 1 hour
        return Cache::remember("league_data_{$leagueName}_{$this->season}", 60, function () use ($url) {
            try {
                $response = Http::withoutVerifying()->get($url);
                if (!$response->successful()) {
                    return [];
                }
                
                $json = $response->json();
                $data = [];
                
                if (isset($json['matches'])) {
                    foreach ($json['matches'] as $match) {
                        // Hanya proses match yang sudah ada skor FT
                        if (isset($match['score']['ft']) && is_array($match['score']['ft'])) {
                            $fthg = $match['score']['ft'][0];
                            $ftag = $match['score']['ft'][1];
                            
                            $ftr = 'D';
                            if ($fthg > $ftag) $ftr = 'H';
                            elseif ($fthg < $ftag) $ftr = 'A';
                            
                            $data[] = [
                                'HomeTeam' => $match['team1'],
                                'AwayTeam' => $match['team2'],
                                'FTHG' => $fthg,
                                'FTAG' => $ftag,
                                'FTR' => $ftr
                            ];
                        }
                    }
                }
                return $data;
            } catch (\Exception $e) {
                \Log::error("Fetch JSON Error: " . $e->getMessage());
                return [];
            }
        });
    }

    public function getStandings(Request $request)
    {
        $league = $request->input('league', 'premier_league');
        $data = $this->getLeagueData($league);
        
        if (!$data || count($data) == 0) {
            return response()->json(['error' => 'Data klasemen tidak ditemukan'], 404);
        }

        $standings = [];

        foreach ($data as $match) {
            $home = $match['HomeTeam'] ?? null;
            $away = $match['AwayTeam'] ?? null;
            $fthg = isset($match['FTHG']) ? (int)$match['FTHG'] : 0;
            $ftag = isset($match['FTAG']) ? (int)$match['FTAG'] : 0;
            $ftr = $match['FTR'] ?? null; // H, D, A

            if (!$home || !$away || !$ftr) continue;

            if (!isset($standings[$home])) {
                $standings[$home] = ['team' => $home, 'p' => 0, 'w' => 0, 'd' => 0, 'l' => 0, 'gf' => 0, 'ga' => 0, 'gd' => 0, 'pts' => 0, 'form' => []];
            }
            if (!isset($standings[$away])) {
                $standings[$away] = ['team' => $away, 'p' => 0, 'w' => 0, 'd' => 0, 'l' => 0, 'gf' => 0, 'ga' => 0, 'gd' => 0, 'pts' => 0, 'form' => []];
            }

            $standings[$home]['p']++;
            $standings[$away]['p']++;
            
            $standings[$home]['gf'] += $fthg;
            $standings[$home]['ga'] += $ftag;
            $standings[$away]['gf'] += $ftag;
            $standings[$away]['ga'] += $fthg;

            if ($ftr === 'H') {
                $standings[$home]['w']++;
                $standings[$home]['pts'] += 3;
                $standings[$home]['form'][] = 'W';
                
                $standings[$away]['l']++;
                $standings[$away]['form'][] = 'L';
            } elseif ($ftr === 'A') {
                $standings[$away]['w']++;
                $standings[$away]['pts'] += 3;
                $standings[$away]['form'][] = 'W';
                
                $standings[$home]['l']++;
                $standings[$home]['form'][] = 'L';
            } else {
                $standings[$home]['d']++;
                $standings[$home]['pts'] += 1;
                $standings[$home]['form'][] = 'D';
                
                $standings[$away]['d']++;
                $standings[$away]['pts'] += 1;
                $standings[$away]['form'][] = 'D';
            }
        }

        foreach ($standings as $key => $s) {
            $standings[$key]['gd'] = $s['gf'] - $s['ga'];
            // Ambil 5 pertandingan terakhir untuk form
            $standings[$key]['form'] = array_slice($standings[$key]['form'], -5);
        }

        // Sort by Points, then GD, then GF
        usort($standings, function($a, $b) {
            if ($a['pts'] === $b['pts']) {
                if ($a['gd'] === $b['gd']) {
                    return $b['gf'] <=> $a['gf'];
                }
                return $b['gd'] <=> $a['gd'];
            }
            return $b['pts'] <=> $a['pts'];
        });

        // Tambahkan atribut logo
        foreach ($standings as $index => $team) {
            $standings[$index]['logo'] = $this->getTeamLogo($team['team']);
            $standings[$index]['position'] = $index + 1;
        }

        return response()->json(['standings' => $standings]);
    }

    private function getTeamLogo($teamName) {
        $logosMap = [];
        $logosFile = public_path('logos.json');
        
        if (file_exists($logosFile)) {
            $logosMap = json_decode(file_get_contents($logosFile), true);
        }

        if (isset($logosMap[$teamName])) {
            $logoUrl = $logosMap[$teamName];
            // Jika path lokal, gunakan asset()
            if (strpos($logoUrl, 'http') === false) {
                return asset($logoUrl);
            }
            return $logoUrl;
        }

        return "https://ui-avatars.com/api/?name=".urlencode($teamName)."&background=random&color=fff&size=128&font-size=0.4";
    }

    public function getTeams(Request $request)
    {
        $league = $request->input('league', 'premier_league');
        $data = $this->getLeagueData($league);
        
        if (!$data) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $teams = [];
        foreach ($data as $row) {
            if (isset($row['HomeTeam']) && !empty($row['HomeTeam'])) {
                $teams[$row['HomeTeam']] = true;
            }
        }
        
        $teams = array_keys($teams);
        sort($teams);
        
        // Buat map URL Logo
        $teamLogos = [];
        foreach ($teams as $team) {
            $teamLogos[] = [
                'name' => $team,
                'logo' => $this->getTeamLogo($team)
            ];
        }

        return response()->json(['teams' => $teamLogos]);
    }

    public function predict(Request $request)
    {
        $league = $request->input('league');
        $homeTeam = $request->input('home_team');
        $awayTeam = $request->input('away_team');
        
        $data = $this->getLeagueData($league);
        if (!$data) {
            return response()->json(['error' => 'Gagal mengambil data liga'], 500);
        }

        // Kalkulasi Statistik Poisson
        $leagueHomeGoals = 0;
        $leagueAwayGoals = 0;
        $totalMatches = count($data);
        
        $homeTeamScored = 0; $homeTeamConceded = 0; $homeMatches = 0;
        $awayTeamScored = 0; $awayTeamConceded = 0; $awayMatches = 0;
        
        foreach ($data as $match) {
            $fthg = (float)($match['FTHG'] ?? 0);
            $ftag = (float)($match['FTAG'] ?? 0);
            
            $leagueHomeGoals += $fthg;
            $leagueAwayGoals += $ftag;
            
            if ($match['HomeTeam'] === $homeTeam) {
                $homeTeamScored += $fthg;
                $homeTeamConceded += $ftag;
                $homeMatches++;
            }
            if ($match['AwayTeam'] === $awayTeam) {
                $awayTeamScored += $ftag;
                $awayTeamConceded += $fthg;
                $awayMatches++;
            }
        }
        
        $leagueHomeGoalsAvg = $totalMatches > 0 ? $leagueHomeGoals / $totalMatches : 0;
        $leagueAwayGoalsAvg = $totalMatches > 0 ? $leagueAwayGoals / $totalMatches : 0;
        
        $homeAvgScored = $homeMatches > 0 ? $homeTeamScored / $homeMatches : 0;
        $homeAvgConceded = $homeMatches > 0 ? $homeTeamConceded / $homeMatches : 0;
        
        $awayAvgScored = $awayMatches > 0 ? $awayTeamScored / $awayMatches : 0;
        $awayAvgConceded = $awayMatches > 0 ? $awayTeamConceded / $awayMatches : 0;
        
        $homeAttack = $leagueHomeGoalsAvg > 0 ? $homeAvgScored / $leagueHomeGoalsAvg : 0;
        $homeDefense = $leagueAwayGoalsAvg > 0 ? $homeAvgConceded / $leagueAwayGoalsAvg : 0;
        
        $awayAttack = $leagueAwayGoalsAvg > 0 ? $awayAvgScored / $leagueAwayGoalsAvg : 0;
        $awayDefense = $leagueHomeGoalsAvg > 0 ? $awayAvgConceded / $leagueHomeGoalsAvg : 0;
        
        // Expected Goals (xG)
        $homeXg = $homeAttack * $awayDefense * $leagueHomeGoalsAvg;
        $awayXg = $awayAttack * $homeDefense * $leagueAwayGoalsAvg;
        
        // Poisson Probabilities
        $homeWinProb = 0;
        $drawProb = 0;
        $awayWinProb = 0;
        
        $scores = [];
        
        for ($i = 0; $i <= 5; $i++) {
            for ($j = 0; $j <= 5; $j++) {
                $prob = $this->poisson($homeXg, $i) * $this->poisson($awayXg, $j);
                $scores["{$i}-{$j}"] = $prob;
                
                if ($i > $j) $homeWinProb += $prob;
                elseif ($i == $j) $drawProb += $prob;
                else $awayWinProb += $prob;
            }
        }
        
        // Cari skor paling mungkin
        arsort($scores);
        $mostLikelyScore = array_key_first($scores);
        
        return response()->json([
            'home' => [
                'name' => $homeTeam,
                'xg' => round($homeXg, 2),
                'win_prob' => round($homeWinProb * 100, 1),
                'logo' => $this->getTeamLogo($homeTeam)
            ],
            'away' => [
                'name' => $awayTeam,
                'xg' => round($awayXg, 2),
                'win_prob' => round($awayWinProb * 100, 1),
                'logo' => $this->getTeamLogo($awayTeam)
            ],
            'draw_prob' => round($drawProb * 100, 1),
            'most_likely_score' => str_replace('-', ' - ', $mostLikelyScore)
        ]);
    }

    private function poisson($mean, $k) {
        return (pow($mean, $k) * exp(-$mean)) / $this->factorial($k);
    }

    private function factorial($n) {
        if ($n <= 1) return 1;
        $result = 1;
        for ($i = 2; $i <= $n; $i++) $result *= $i;
        return $result;
    }
}
