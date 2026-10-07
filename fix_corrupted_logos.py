import os
import re
import urllib.request
import json
import time
import urllib.parse

logos = json.load(open('public/logos.json'))

def get_filename(team):
    return re.sub(r'[^a-zA-Z0-9_-]', '_', team.lower()) + '.png'

user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36'

for team in logos.keys():
    filename = get_filename(team)
    filepath = os.path.join('public', 'logos', filename)
    
    redownload = False
    if not os.path.exists(filepath):
        redownload = True
    else:
        with open(filepath, 'rb') as f:
            header = f.read(50)
            if b'<!doctype html>' in header.lower() or b'<html' in header.lower():
                redownload = True
                
    if redownload:
        print(f"Redownloading {team}...")
        
        # Bersihkan nama tim sesuai algoritma PHP
        clean_name = team.replace(' FC', '').replace('FC ', '').replace(' AFC', '').replace(' AC ', '').replace('AC ', '').replace(' BC', '').replace(' ACF ', '').replace('ACF ', '').replace(' AS ', '').replace('AS ', '').replace(' 1909', '').replace(' Calcio', '').replace(' 1907', '').replace(' 1913', '').replace(' SS ', '').replace('SS ', '').replace('SSC ', '').replace(' US ', '').replace('US ', '').replace(' CFC', '').replace('Internazionale Milano', 'Inter Milan').replace(' 04', '').replace(' 05', '').replace(' 07', '').replace(' 1899', '').replace(' SV', '').replace('SV ', '').replace('1. ', '').strip()
        
        search_url = f"https://www.thesportsdb.com/api/v1/json/3/searchteams.php?t={urllib.parse.quote(clean_name)}"
        try:
            req = urllib.request.Request(search_url, headers={'User-Agent': user_agent})
            with urllib.request.urlopen(req) as response:
                data = json.loads(response.read())
                if data.get('teams') and data['teams'][0].get('strBadge'):
                    img_url = data['teams'][0]['strBadge']
                    
                    # Download image
                    img_req = urllib.request.Request(img_url, headers={'User-Agent': user_agent})
                    with urllib.request.urlopen(img_req) as img_resp:
                        img_data = img_resp.read()
                        if img_data.startswith(b'\x89PNG') or img_data.startswith(b'GIF') or img_data.startswith(b'\xff\xd8'):
                            with open(filepath, 'wb') as f:
                                f.write(img_data)
                            print(f"Success: {team} ({len(img_data)} bytes)")
                        else:
                            print(f"Failed: Not an image for {team}")
                else:
                    print(f"Not found in API for {team}")
        except Exception as e:
            print(f"Error for {team}: {e}")
        time.sleep(1)

print("Done fixing all corrupted logos!")
