# LiftDeck

A fitness coaching platform built with Laravel 12, connecting coaches with their clients for workout programming, nutrition tracking, and progress monitoring.

## Features

**For Coaches**
- Client management — invite clients, view progress, and manage profiles
- Program builder — create workout programs with exercises and targets
- Nutrition tools — build a meal library and set macro goals per client
- Check-ins & tracking metrics — define custom metrics and review client submissions
- Messaging — communicate with clients in-app
- Analytics — monitor client activity and progress
- Loyalty & rewards — create reward systems and track client achievements
- Branding — customise your coaching profile

**For Clients**
- Dashboard with program overview and today's workouts
- Workout logging — log sets, reps, and weights against assigned programs
- Nutrition logging — log meals from the coach's library or add custom entries
- Progress tracking — view macro progress, check-in history, and personal bests
- Achievements & rewards — earn XP and redeem loyalty rewards
- Onboarding — complete coach-configured intake forms

## Tech Stack

- **Laravel 12** — PHP 8.4
- **Livewire 4** + **Alpine.js 3** — reactive UI components
- **Tailwind CSS 3** — utility-first styling
- **BladewindUI** — UI component library
- **SQLite** — default database (configurable)
- **Pest 4** — test suite

## Getting Started

```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Start development server
composer run dev
```

The app will be available at `http://localhost:8000`.

## Testing

```bash
php artisan test --compact
```

## License

Private — all rights reserved.
