# ⚽ League Prediction AI System

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Python](https://img.shields.io/badge/Python-3776AB?style=for-the-badge&logo=python&logoColor=white)
![Pandas](https://img.shields.io/badge/Pandas-150458?style=for-the-badge&logo=pandas&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

A comprehensive, AI-powered web application and prediction engine for Europe's top football leagues. This project leverages the **Poisson Distribution** statistical model to calculate match probabilities, Expected Goals (xG), and predict outcomes (Home Win, Draw, Away Win).

## 🚀 Features

- **Multi-League Support**: Supports 4 major European leagues:
  - 🏴󠁧󠁢󠁥󠁮󠁧󠁿 English Premier League
  - 🇪🇸 Spanish La Liga
  - 🇩🇪 German Bundesliga
  - 🇮🇹 Italian Serie A
- **Statistical AI Engine**: Utilizes Python and Pandas to analyze historical match data, computing Attack & Defense Strength to derive Expected Goals (xG) and Poisson probabilities for every possible match outcome.
- **Automated Data Pipeline**: Scripts to fetch real-time and historical CSV data from `football-data.co.uk`.
- **Media Management**: Automated fetching, validation, and fixing of team logos and assets (`fix_corrupted_logos.py`, `download_images.php`).
- **Robust Backend**: Built on top of **Laravel 12**, providing a scalable MVC architecture, robust routing, and an elegant development experience.

## 🏗 Architecture

The system is built with a hybrid architecture combining a robust web framework with a powerful data processing engine:

1. **Frontend / Web Layer (Laravel / PHP)**:
   - Handles user interactions, API requests, and web views.
   - Manages routing and serves as the presentation layer.
2. **Prediction Engine (Python)**:
   - Contains the core logic for the AI prediction model (`prediction.py`).
   - Downloads the latest dataset dynamically.
   - Computes statistical metrics (Attack/Defense strengths based on historical goals).
   - Runs the Poisson mathematical model for probability outcomes.
3. **Data & Asset Management Scripts**:
   - `fetch_logos.php`, `download_images.php`: Automated web scrapers for team assets.
   - `fix_corrupted_logos.py`, `check_logos.php`: Data integrity utilities for visual assets.
   - `generate_data.py`: Pre-processing for statistical models.

## 🧠 How the AI Prediction Works (Poisson Distribution)

The model predicts the number of goals each team is likely to score based on their historical performance.
1. **Calculate League Averages**: Average home and away goals scored across the entire league.
2. **Team Strengths**:
   - **Attack Strength**: Ratio of a team's average goals scored to the league average.
   - **Defense Strength**: Ratio of a team's average goals conceded to the league average.
3. **Expected Goals (xG)**:
   - Home xG = `Home Attack * Away Defense * League Home Average`
   - Away xG = `Away Attack * Home Defense * League Away Average`
4. **Poisson Probability**:
   - Applies the Poisson formula: `P(x; μ) = (e^(-μ) * μ^x) / x!` to find the exact probability of scoring `x` goals.
   - Simulates thousands of scoreline permutations (e.g., 0-0 up to 5-5) to calculate the aggregate probability of a Home Win, Draw, or Away Win.

## 🛠 Prerequisites

- PHP >= 8.2
- Composer
- Python 3.x
- Node.js & NPM (for frontend assets)
- Python packages: `pandas`

## ⚙️ Installation & Setup

1. **Clone the repository** (if not already done).
2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```
3. **Setup Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Install Python Dependencies**:
   ```bash
   pip install pandas
   ```
5. **Run the Web Application**:
   ```bash
   php artisan serve
   ```
   *(Alternatively, use `composer run dev` for the full dev stack).*

## 🔮 Usage

### Running the CLI Predictor
You can test the prediction engine directly via the command line:

```bash
python prediction.py
```
**Steps in CLI:**
1. Enter the league name (e.g., `premier_league`, `la_liga`, `bundesliga`, `serie_a`).
2. Wait for the engine to download the latest dataset.
3. Enter the Home Team and Away Team names as prompted.
4. View the expected goals (xG) and the calculated win/draw/loss probabilities.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
