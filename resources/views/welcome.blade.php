<!DOCTYPE html>
<html lang="en" class="antialiased text-gray-100 bg-gray-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Football Predictor | Poisson & Monte Carlo</title>
    @vite('resources/css/app.css')
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .accent-glow { box-shadow: 0 0 40px -10px rgba(163, 230, 53, 0.3); }
        
        select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
        }

        /* Standings Table Styles */
        .table-row-hover:hover { background: rgba(255,255,255,0.05); }
    </style>
</head>
<body class="min-h-screen selection:bg-lime-400 selection:text-gray-950">

    <nav class="sticky top-0 z-50 w-full glass-panel border-b-0 border-white/5">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded bg-lime-400 flex items-center justify-center text-gray-950 font-bold tracking-tighter">AI</div>
                <span class="font-semibold tracking-tight text-white">Predictor<span class="text-gray-500">.io</span></span>
            </div>
            
            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-400">
                <button id="nav-simulator" class="text-white hover:text-lime-400 transition-colors">Match Simulator</button>
                <button id="nav-standings" class="hover:text-white transition-colors">Live Standings</button>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Sidebar -->
        <aside class="lg:col-span-3 space-y-8">
            <div>
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Pilih Liga</h3>
                <div class="space-y-2" id="league-selector">
                    <button data-league="premier_league" class="league-btn w-full flex items-center justify-between px-4 py-3 rounded-xl bg-lime-400 text-gray-950 font-medium transition-transform active:scale-95">
                        <span>Premier League</span>
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                    <button data-league="la_liga" class="league-btn w-full flex items-center justify-between px-4 py-3 rounded-xl glass-panel hover:bg-white/5 text-gray-300 font-medium transition-colors">
                        <span>La Liga</span>
                    </button>
                    <button data-league="bundesliga" class="league-btn w-full flex items-center justify-between px-4 py-3 rounded-xl glass-panel hover:bg-white/5 text-gray-300 font-medium transition-colors">
                        <span>Bundesliga</span>
                    </button>
                    <button data-league="serie_a" class="league-btn w-full flex items-center justify-between px-4 py-3 rounded-xl glass-panel hover:bg-white/5 text-gray-300 font-medium transition-colors">
                        <span>Serie A</span>
                    </button>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-gradient-to-b from-gray-900 to-gray-950 border border-white/5">
                <h4 class="text-sm font-medium text-white mb-2" id="sidebar-info-title">Algorithm Engine</h4>
                <p class="text-xs text-gray-400 leading-relaxed" id="sidebar-info-text">Data klasemen ini di-generate secara real-time langsung dari dataset skor mentah mingguan.</p>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:col-span-9 space-y-6 relative">
            <!-- Loading Overlay -->
            <div id="loading-overlay" class="hidden absolute inset-0 z-50 bg-gray-950/80 backdrop-blur-sm flex items-center justify-center rounded-3xl">
                <div class="text-lime-400 text-xl font-bold animate-pulse">Memuat Data...</div>
            </div>

            <!-- View: Match Simulator -->
            <div id="view-simulator" class="block">
                <header class="mb-8">
                    <h1 class="text-3xl font-bold text-white tracking-tight">Match Simulator</h1>
                    <p class="text-gray-400 mt-2">Pilih tim untuk melihat prediksi statistik pertandingan berdasarkan Poisson Distribution.</p>
                </header>

                <div class="glass-panel rounded-3xl p-8 md:p-12 relative overflow-hidden accent-glow">
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="flex-1 text-center w-full">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tuan Rumah (Home)</label>
                            <div class="w-24 h-24 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-4 overflow-hidden shadow-lg">
                                <img id="home-logo" src="https://ui-avatars.com/api/?name=H&background=random&color=fff&size=128" alt="Home Team" class="w-full h-full object-cover">
                            </div>
                            <select id="home-team-select" class="w-full bg-gray-900 border border-white/10 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-lime-400 transition-colors cursor-pointer">
                                <option value="">Pilih Tim...</option>
                            </select>
                        </div>

                        <div class="shrink-0 flex flex-col items-center py-6">
                            <div class="px-4 py-1.5 rounded-full bg-white/10 text-xs font-semibold text-gray-300 uppercase tracking-widest mb-2 border border-white/5">Simulate</div>
                            <span class="text-3xl font-black text-lime-400 italic">VS</span>
                        </div>

                        <div class="flex-1 text-center w-full">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tandang (Away)</label>
                            <div class="w-24 h-24 mx-auto rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mb-4 overflow-hidden shadow-lg">
                                <img id="away-logo" src="https://ui-avatars.com/api/?name=A&background=random&color=fff&size=128" alt="Away Team" class="w-full h-full object-cover">
                            </div>
                            <select id="away-team-select" class="w-full bg-gray-900 border border-white/10 text-white text-sm rounded-xl px-4 py-3 outline-none focus:border-lime-400 transition-colors cursor-pointer">
                                <option value="">Pilih Tim...</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-12 flex justify-center relative z-10">
                        <button id="btn-predict" class="px-8 py-4 bg-lime-400 text-gray-950 rounded-full font-bold shadow-[0_0_20px_rgba(163,230,53,0.4)] hover:shadow-[0_0_30px_rgba(163,230,53,0.6)] hover:bg-lime-300 transition-all transform hover:-translate-y-0.5 disabled:opacity-50">
                            Run Poisson Model
                        </button>
                    </div>
                </div>

                <div id="results-container" class="hidden opacity-0 transition-opacity duration-500">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                        <div class="glass-panel p-6 rounded-2xl border-t-2 border-t-lime-400">
                            <div id="res-home-name" class="text-sm text-gray-400 mb-1">Home Win</div>
                            <div id="res-home-prob" class="text-4xl font-black text-white tracking-tighter">0%</div>
                        </div>
                        <div class="glass-panel p-6 rounded-2xl">
                            <div class="text-sm text-gray-400 mb-1">Draw</div>
                            <div id="res-draw-prob" class="text-4xl font-black text-white tracking-tighter">0%</div>
                        </div>
                        <div class="glass-panel p-6 rounded-2xl border-t-2 border-t-blue-500">
                            <div id="res-away-name" class="text-sm text-gray-400 mb-1">Away Win</div>
                            <div id="res-away-prob" class="text-4xl font-black text-white tracking-tighter">0%</div>
                        </div>
                    </div>
                    
                    <div class="mt-8 glass-panel p-8 rounded-3xl relative">
                        <div class="absolute top-8 right-8 text-right">
                            <div class="text-xs text-gray-500 uppercase tracking-widest font-semibold">Skor Prediksi Terkuat</div>
                            <div id="res-score" class="text-2xl font-black text-white mt-1">0 - 0</div>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-8">Expected Goals (xG)</h3>
                        <div class="space-y-6 max-w-xl">
                            <div>
                                <div class="flex justify-between text-sm mb-2">
                                    <span id="res-home-xg-label" class="text-gray-300">Home xG</span>
                                    <span id="res-home-xg" class="text-white font-mono font-bold">0.00</span>
                                </div>
                                <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden">
                                    <div id="res-home-bar" class="h-full bg-lime-400 rounded-full transition-all duration-1000 w-0"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-2">
                                    <span id="res-away-xg-label" class="text-gray-300">Away xG</span>
                                    <span id="res-away-xg" class="text-white font-mono font-bold">0.00</span>
                                </div>
                                <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden">
                                    <div id="res-away-bar" class="h-full bg-blue-500 rounded-full transition-all duration-1000 w-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View: Standings -->
            <div id="view-standings" class="hidden">
                <header class="mb-8 flex items-end justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white tracking-tight">Live Standings</h1>
                        <p class="text-gray-400 mt-2">Klasemen sementara berdasarkan data yang di-generate.</p>
                    </div>
                    <div class="text-sm font-semibold text-lime-400 bg-lime-400/10 px-4 py-2 rounded-lg">2026 / 2027</div>
                </header>
                
                <div class="glass-panel rounded-3xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-white/5 bg-white/5">
                                    <th class="py-4 px-6 text-left">Team</th>
                                    <th class="py-4 px-4 text-center">Pts</th>
                                    <th class="py-4 px-4 text-center">MP</th>
                                    <th class="py-4 px-4 text-center">W</th>
                                    <th class="py-4 px-4 text-center">D</th>
                                    <th class="py-4 px-4 text-center">L</th>
                                    <th class="py-4 px-6 text-center">Form</th>
                                </tr>
                            </thead>
                            <tbody id="standings-body" class="text-sm text-gray-300 divide-y divide-white/5">
                                <!-- Data injected here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let currentLeague = 'premier_league';
            let currentView = 'simulator'; // simulator | standings
            let teamsData = [];
            
            const homeSelect = document.getElementById('home-team-select');
            const awaySelect = document.getElementById('away-team-select');
            const loadingOverlay = document.getElementById('loading-overlay');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            // Nav Tabs
            document.getElementById('nav-simulator').addEventListener('click', (e) => {
                currentView = 'simulator';
                e.target.classList.add('text-white');
                e.target.classList.remove('text-gray-400');
                document.getElementById('nav-standings').classList.remove('text-white');
                document.getElementById('nav-standings').classList.add('text-gray-400');
                document.getElementById('view-simulator').classList.remove('hidden');
                document.getElementById('view-standings').classList.add('hidden');
                fetchTeams(currentLeague);
            });

            document.getElementById('nav-standings').addEventListener('click', (e) => {
                currentView = 'standings';
                e.target.classList.add('text-white');
                e.target.classList.remove('text-gray-400');
                document.getElementById('nav-simulator').classList.remove('text-white');
                document.getElementById('nav-simulator').classList.add('text-gray-400');
                document.getElementById('view-standings').classList.remove('hidden');
                document.getElementById('view-simulator').classList.add('hidden');
                fetchStandings(currentLeague);
            });

            async function fetchTeams(league) {
                loadingOverlay.classList.remove('hidden');
                try {
                    const ts = new Date().getTime();
                    const res = await fetch(`/api/teams?league=${league}&_t=${ts}`);
                    const data = await res.json();
                    
                    if(data.error) throw new Error(data.error);

                    teamsData = data.teams;
                    let options = '<option value="">Pilih Tim...</option>';
                    teamsData.forEach(t => options += `<option value="${t.name}">${t.name}</option>`);
                    homeSelect.innerHTML = options;
                    awaySelect.innerHTML = options;
                } catch (e) {
                    alert('Gagal mengambil data tim. Error: ' + e.message);
                } finally {
                    loadingOverlay.classList.add('hidden');
                }
            }

            async function fetchStandings(league) {
                loadingOverlay.classList.remove('hidden');
                try {
                    const ts = new Date().getTime();
                    const res = await fetch(`/api/standings?league=${league}&_t=${ts}`);
                    const data = await res.json();
                    
                    if(data.error) throw new Error(data.error);

                    let html = '';
                    data.standings.forEach(row => {
                            let formHtml = '';
                            if (row.form) {
                                row.form.forEach(f => {
                                    if (f === 'W') {
                                        formHtml += `<span class="inline-flex items-center justify-center w-5 h-5 bg-emerald-500 rounded-full text-white text-[10px] mx-[2px] font-bold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></span>`;
                                    } else if (f === 'D') {
                                        formHtml += `<span class="inline-flex items-center justify-center w-5 h-5 bg-gray-500 rounded-full text-white text-[10px] mx-[2px] font-bold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg></span>`;
                                    } else {
                                        formHtml += `<span class="inline-flex items-center justify-center w-5 h-5 bg-red-500 rounded-full text-white text-[10px] mx-[2px] font-bold"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg></span>`;
                                    }
                                });
                            }
                            
                            let indicatorClass = 'border-transparent';
                            if (row.position <= 4) indicatorClass = 'border-blue-500';
                            else if (row.position === 5) indicatorClass = 'border-orange-500';
                            else if (row.position >= 18) indicatorClass = 'border-red-500';

                            html += `
                            <tr class="table-row-hover transition-colors border-l-4 ${indicatorClass}">
                                <td class="py-3 px-6 flex items-center gap-4">
                                    <span class="w-4 font-mono text-gray-500 text-sm">${row.position}</span>
                                    <img src="${row.logo}" class="w-7 h-7 bg-white rounded-full object-contain border-white/10" alt="logo">
                                    <span class="font-medium text-gray-200">${row.team}</span>
                                </td>
                                <td class="py-3 px-4 text-center text-gray-300 font-medium">${row.pts}</td>
                                <td class="py-3 px-4 text-center text-gray-400">${row.p}</td>
                                <td class="py-3 px-4 text-center text-gray-400">${row.w}</td>
                                <td class="py-3 px-4 text-center text-gray-400">${row.d}</td>
                                <td class="py-3 px-4 text-center text-gray-400">${row.l}</td>
                                <td class="py-3 px-6 text-center whitespace-nowrap">${formHtml}</td>
                            </tr>
                        `;
                    });
                    document.getElementById('standings-body').innerHTML = html;
                } catch (e) {
                    alert('Gagal mengambil data klasemen. Error: ' + e.message);
                } finally {
                    loadingOverlay.classList.add('hidden');
                }
            }

            // Update Logo on Select Change
            homeSelect.addEventListener('change', (e) => {
                const team = teamsData.find(t => t.name === e.target.value);
                if (team) document.getElementById('home-logo').src = team.logo;
            });
            awaySelect.addEventListener('change', (e) => {
                const team = teamsData.find(t => t.name === e.target.value);
                if (team) document.getElementById('away-logo').src = team.logo;
            });

            // League Change
            document.querySelectorAll('.league-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const target = e.currentTarget;
                    document.querySelectorAll('.league-btn').forEach(b => {
                        b.className = 'league-btn w-full flex items-center justify-between px-4 py-3 rounded-xl glass-panel hover:bg-white/5 text-gray-300 font-medium transition-colors';
                        b.innerHTML = `<span>${b.querySelector('span').innerText}</span>`;
                    });
                    target.className = 'league-btn w-full flex items-center justify-between px-4 py-3 rounded-xl bg-lime-400 text-gray-950 font-medium transition-transform active:scale-95';
                    target.innerHTML = `<span>${target.querySelector('span').innerText}</span><svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>`;
                    
                    currentLeague = target.dataset.league;
                    
                    if (currentView === 'simulator') {
                        document.getElementById('results-container').classList.add('hidden', 'opacity-0');
                        fetchTeams(currentLeague);
                    } else {
                        fetchStandings(currentLeague);
                    }
                });
            });

            // Prediction action
            document.getElementById('btn-predict').addEventListener('click', async (e) => {
                const home = homeSelect.value;
                const away = awaySelect.value;
                if (!home || !away) return alert('Pilih tim terlebih dahulu!');
                if (home === away) return alert('Tim kandang dan tandang tidak boleh sama!');

                e.target.disabled = true;
                loadingOverlay.classList.remove('hidden');

                try {
                    const res = await fetch('/api/predict', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ league: currentLeague, home_team: home, away_team: away })
                    });
                    const data = await res.json();
                    
                    if(data.error) throw new Error(data.error);
                    
                    document.getElementById('res-home-name').innerText = `${data.home.name} Win`;
                    document.getElementById('res-home-prob').innerText = `${data.home.win_prob}%`;
                    document.getElementById('res-draw-prob').innerText = `${data.draw_prob}%`;
                    document.getElementById('res-away-name').innerText = `${data.away.name} Win`;
                    document.getElementById('res-away-prob').innerText = `${data.away.win_prob}%`;
                    document.getElementById('res-score').innerText = data.most_likely_score;
                    document.getElementById('res-home-xg-label').innerText = `${data.home.name} xG`;
                    document.getElementById('res-home-xg').innerText = data.home.xg;
                    document.getElementById('res-home-bar').style.width = `${Math.min((data.home.xg / 3.5) * 100, 100)}%`;
                    document.getElementById('res-away-xg-label').innerText = `${data.away.name} xG`;
                    document.getElementById('res-away-xg').innerText = data.away.xg;
                    document.getElementById('res-away-bar').style.width = `${Math.min((data.away.xg / 3.5) * 100, 100)}%`;

                    const rc = document.getElementById('results-container');
                    rc.classList.remove('hidden');
                    setTimeout(() => rc.classList.remove('opacity-0'), 100);
                } catch (e) {
                    alert('Error: ' + e.message);
                } finally {
                    e.target.disabled = false;
                    loadingOverlay.classList.add('hidden');
                }
            });

            fetchTeams(currentLeague);
        });
    </script>
</body>
</html>
