import pandas as pd
import urllib.request
import ssl
import math

# SSL bypass in case of certificate issues on some machines
ssl._create_default_https_context = ssl._create_unverified_context

# Mapping for the 4 leagues to football-data.co.uk codes
LEAGUES = {
    'premier_league': 'E0',
    'la_liga': 'SP1',
    'bundesliga': 'D1',
    'serie_a': 'I1'
}
SEASON = '2425' # Musim 2024-2025

def load_data(league_name):
    code = LEAGUES.get(league_name.lower())
    if not code:
        print("Liga tidak ditemukan!")
        return None
    
    url = f"https://www.football-data.co.uk/mmz4281/{SEASON}/{code}.csv"
    print(f"Mengunduh data untuk {league_name} dari {url}...")
    try:
        df = pd.read_csv(url)
        # Hapus baris kosong jika ada
        df = df.dropna(subset=['HomeTeam', 'AwayTeam', 'FTHG', 'FTAG'])
        return df
    except Exception as e:
        print(f"Gagal mengunduh data: {e}")
        return None

def calculate_team_stats(df):
    """
    Menghitung metrik untuk Distribusi Poisson:
    - Rata-rata gol yang dicetak home & away
    - Rata-rata gol yang kebobolan home & away
    """
    # Total pertandingan
    total_matches = len(df)
    
    # Rata-rata gol liga
    league_home_goals_avg = df['FTHG'].mean()
    league_away_goals_avg = df['FTAG'].mean()
    
    stats = {}
    teams = pd.unique(df[['HomeTeam', 'AwayTeam']].values.ravel('K'))
    
    for team in teams:
        # Performa Kandang (Home)
        home_matches = df[df['HomeTeam'] == team]
        home_scored = home_matches['FTHG'].mean() if not home_matches.empty else 0
        home_conceded = home_matches['FTAG'].mean() if not home_matches.empty else 0
        
        # Performa Tandang (Away)
        away_matches = df[df['AwayTeam'] == team]
        away_scored = away_matches['FTAG'].mean() if not away_matches.empty else 0
        away_conceded = away_matches['FTHG'].mean() if not away_matches.empty else 0
        
        # Hitung Kekuatan Serangan (Attack Strength) & Kekuatan Pertahanan (Defense Strength)
        home_attack = home_scored / league_home_goals_avg if league_home_goals_avg > 0 else 0
        away_attack = away_scored / league_away_goals_avg if league_away_goals_avg > 0 else 0
        
        home_defense = home_conceded / league_away_goals_avg if league_away_goals_avg > 0 else 0
        away_defense = away_conceded / league_home_goals_avg if league_home_goals_avg > 0 else 0
        
        stats[team] = {
            'home_attack': home_attack,
            'away_attack': away_attack,
            'home_defense': home_defense,
            'away_defense': away_defense
        }
        
    return stats, league_home_goals_avg, league_away_goals_avg

def poisson_probability(l, x):
    """Menghitung probabilitas poisson"""
    return ((l**x) * math.exp(-l)) / math.factorial(x)

def predict_match(home_team, away_team, stats, league_home_avg, league_away_avg):
    if home_team not in stats or away_team not in stats:
        print("Tim tidak ditemukan di database liga ini.")
        return
        
    # Expected Goals (xG) untuk tim Kandang dan Tandang
    # xG Home = Home Attack * Away Defense * League Home Average
    home_xg = stats[home_team]['home_attack'] * stats[away_team]['away_defense'] * league_home_avg
    
    # xG Away = Away Attack * Home Defense * League Away Average
    away_xg = stats[away_team]['away_attack'] * stats[home_team]['home_defense'] * league_away_avg
    
    print(f"\n--- Prediksi: {home_team} (Home) vs {away_team} (Away) ---")
    print(f"Expected Goals (xG) -> {home_team}: {home_xg:.2f} | {away_team}: {away_xg:.2f}")
    
    # Hitung probabilitas skor hingga maksimal 5 gol
    home_win_prob = 0
    draw_prob = 0
    away_win_prob = 0
    
    for i in range(6): # Gol home (0 - 5)
        for j in range(6): # Gol away (0 - 5)
            prob = poisson_probability(home_xg, i) * poisson_probability(away_xg, j)
            if i > j:
                home_win_prob += prob
            elif i == j:
                draw_prob += prob
            else:
                away_win_prob += prob
                
    print("\nProbabilitas Hasil:")
    print(f"{home_team} Menang: {home_win_prob * 100:.2f}%")
    print(f"Seri (Draw): {draw_prob * 100:.2f}%")
    print(f"{away_team} Menang: {away_win_prob * 100:.2f}%")

if __name__ == "__main__":
    print("Selamat datang di Sistem Prediksi Sepakbola AI")
    print("Liga Tersedia: premier_league, la_liga, bundesliga, serie_a")
    
    # Contoh pemakaian
    liga = input("Masukkan nama liga (contoh: premier_league): ").strip().lower()
    df = load_data(liga)
    
    if df is not None:
        print("\nTim yang tersedia:")
        print(", ".join(df['HomeTeam'].unique()[:10]) + ", ...")
        
        stats, l_home_avg, l_away_avg = calculate_team_stats(df)
        
        home = input("\nMasukkan Tim Kandang (Home): ")
        away = input("Masukkan Tim Tandang (Away): ")
        
        predict_match(home, away, stats, l_home_avg, l_away_avg)
