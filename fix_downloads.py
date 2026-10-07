import json
import urllib.request
import os
import time
import random

logos = json.load(open('public/logos.json'))

user_agents = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0'
]

for team, local_path in logos.items():
    # local_path is like /logos/filename.png
    filepath = "public" + local_path
    
    # Check if the file is an HTML file (Cloudflare block) or doesn't exist
    redownload = False
    if not os.path.exists(filepath):
        redownload = True
    else:
        with open(filepath, 'rb') as f:
            header = f.read(50)
            if b'<!doctype html>' in header.lower() or b'<html' in header.lower():
                redownload = True

    if redownload:
        print(f"Fixing {team}...")
        # Since local_path is saved in logos.json, we need the ORIGINAL URL!
        # Oh wait, logos.json NOW contains the local path. We lost the original URL!
