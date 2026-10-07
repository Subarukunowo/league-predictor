# ⚽ League Prediction AI System

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Python](https://img.shields.io/badge/Python-3776AB?style=for-the-badge&logo=python&logoColor=white)
![Pandas](https://img.shields.io/badge/Pandas-150458?style=for-the-badge&logo=pandas&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)

An AI-powered web application and prediction engine for Europe's top football leagues. This project uses the **Poisson Distribution** statistical model to calculate match probabilities, Expected Goals (xG), and predict outcomes (Home Win, Draw, Away Win).

## Features

- **Multi-League Support**: Supports 4 major European leagues:
  - 🏴󠁧󠁢󠁥󠁮󠁧󠁿 English Premier League
  - 🇪🇸 Spanish La Liga
  - 🇩🇪 German Bundesliga
  - 🇮🇹 Italian Serie A
- **Statistical AI Engine**: Uses Python and Pandas to analyze historical match data, computing Attack & Defense Strength to derive Expected Goals (xG) and Poisson probabilities for every possible match outcome.
- **Automated Data Pipeline**: Scripts to fetch real-time and historical CSV data from `football-data.co.uk`.
- **Media Management**: Automated fetching, validation, and fixing of team logos and assets (`fix_corrupted_logos.py`, `download_images.php`).
- **Backend**: Built on top of **Laravel 12**, providing a scalable MVC architecture, routing, and development experience.

## Architecture

The system is built with a hybrid architecture combining a web framework with a data processing engine:

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

## How the AI Prediction Works (Poisson Distribution)

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

## Software Engineering Use Cases

This project demonstrates several core software engineering practices:

- **Data Pipeline & ETL Automation**: Scripts (`generate_data.py`, `download_images.php`, `fix_corrupted_logos.py`) act as automated ETL processors. They extract raw data from third-party sources (football-data.co.uk), transform and clean it (handling empty rows or corrupted files), and load it for the prediction engine.
- **Polyglot Architecture (PHP + Python)**: Bridges Python's strength in mathematical computation (Poisson Distribution via Pandas) with PHP/Laravel's strength in HTTP request management and web routing.
- **Scalable MVC Design**: Uses the Laravel framework to enforce separation of concerns among the data logic, prediction engine, and presentation layer, making the codebase maintainable.
- **Fault Tolerance**: Implements SSL bypasses and exception handling in the data extraction scripts to prevent application crashes when external APIs fail or assets are corrupted.

## Prerequisites

- PHP >= 8.2
- Composer
- Python 3.x
- Node.js & NPM (for frontend assets)
- Python packages: `pandas`

## Installation & Setup

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

## Usage

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

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
