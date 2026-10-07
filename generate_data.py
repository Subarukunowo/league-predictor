import csv
import random
import os
from datetime import datetime, timedelta

os.makedirs('storage/app/2526', exist_ok=True)

leagues = {
    'E0': ['Arsenal', 'Aston Villa', 'Bournemouth', 'Brentford', 'Brighton', 'Chelsea', 'Crystal Palace', 'Everton', 'Fulham', 'Ipswich', 'Leicester', 'Liverpool', 'Man City', 'Man United', 'Newcastle', 'Nott\'m Forest', 'Southampton', 'Tottenham', 'West Ham', 'Wolves'],
    'SP1': ['Alaves', 'Ath Bilbao', 'Ath Madrid', 'Barcelona', 'Betis', 'Celta', 'Espanol', 'Getafe', 'Girona', 'Las Palmas', 'Leganes', 'Mallorca', 'Osasuna', 'Rayo Vallecano', 'Real Madrid', 'Sociedad', 'Sevilla', 'Valencia', 'Valladolid', 'Villarreal'],
    'D1': ['Augsburg', 'Bayer Leverkusen', 'Bayern Munich', 'Bochum', 'Dortmund', 'Eintracht Frankfurt', 'Freiburg', 'Heidenheim', 'Hoffenheim', 'Holstein Kiel', 'Mainz', 'M\'gladbach', 'RB Leipzig', 'St Pauli', 'Stuttgart', 'Union Berlin', 'Werder Bremen', 'Wolfsburg'],
    'I1': ['Atalanta', 'Bologna', 'Cagliari', 'Como', 'Empoli', 'Fiorentina', 'Genoa', 'Inter', 'Juventus', 'Lazio', 'Lecce', 'Milan', 'Monza', 'Napoli', 'Parma', 'Roma', 'Torino', 'Udinese', 'Venezia', 'Verona']
}

start_date = datetime(2025, 8, 15)

for code, teams in leagues.items():
    filepath = f'storage/app/2526/{code}.csv'
    with open(filepath, 'w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(['Div', 'Date', 'Time', 'HomeTeam', 'AwayTeam', 'FTHG', 'FTAG', 'FTR'])
        
        current_date = start_date
        
        # Play 10 matchweeks
        for week in range(10):
            week_teams = teams.copy()
            random.shuffle(week_teams)
            
            for i in range(0, len(week_teams), 2):
                home = week_teams[i]
                away = week_teams[i+1]
                
                # Generate realistic scores (Poisson-like)
                fthg = random.choices([0, 1, 2, 3, 4, 5], weights=[0.25, 0.33, 0.23, 0.12, 0.05, 0.02])[0]
                ftag = random.choices([0, 1, 2, 3, 4], weights=[0.35, 0.35, 0.20, 0.08, 0.02])[0]
                
                if fthg > ftag:
                    ftr = 'H'
                elif fthg < ftag:
                    ftr = 'A'
                else:
                    ftr = 'D'
                    
                date_str = current_date.strftime('%d/%m/%Y')
                writer.writerow([code, date_str, '15:00', home, away, fthg, ftag, ftr])
                
            current_date += timedelta(days=7)
