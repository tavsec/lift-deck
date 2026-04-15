# LiftDeck

**LiftDeck** is an all-in-one gym coaching platform that replaces spreadsheets and messaging apps. Coaches build personalized training programs, clients log workouts in real-time, and everyone stays connected through in-app messaging — all from any device, no app store required.

## What It Does

LiftDeck connects fitness coaches with their clients through a single web platform. Coaches manage their entire client roster: create programs, track progress, log nutrition, and send messages. Clients get a clean, guided experience for logging workouts, checking in daily, and seeing their progress over time.

### For Coaches
- Build reusable training programs with workouts, exercises, sets, reps, and notes
- Invite clients via a unique code — no manual account setup needed
- Track each client's workout logs, nutrition, body metrics, and progress photos
- Communicate via in-app messaging with unread notification badges
- Customize onboarding questions per client (goals, injuries, experience level)
- Add your gym branding (logo, colors, welcome text) on paid plans
- Export client analytics to Excel
- Create "track-only" clients for in-person sessions where the coach logs everything

### For Clients
- Log completed workouts with weight, reps, and difficulty ratings
- Track meals and daily nutrition against macro targets
- Log custom metrics (weight, measurements) with optional progress photos
- Earn XP, unlock levels, and redeem points for coach-defined rewards
- Message your coach directly from the app

## Subscription Plans

| Plan | Price | Clients | Highlights |
|------|-------|---------|------------|
| Basic | €2.50/month | Up to 5 | Core features, 7-day free trial |
| Advanced | €10/month | Up to 15 | Core + loyalty/gamification |
| Professional | €15/month + €0.50/client over 30 | Unlimited | Everything + custom branding |

The Basic plan includes a 7-day free trial via Stripe — a credit card is required to start the trial.

## Tech Stack

- **Backend**: Laravel 12, Livewire 4, Filament 5 (admin panel)
- **Frontend**: Blade, Tailwind CSS v3, Alpine.js
- **Payments**: Laravel Cashier + Stripe
- **Auth**: Laravel Breeze with email verification
- **Storage**: Spatie MediaLibrary (S3-compatible)
- **Testing**: Pest 4

## Getting Started

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
composer run dev
```

Set your Stripe keys in `.env` (`STRIPE_KEY`, `STRIPE_SECRET`) before using payment features.

## Running Tests

```bash
php artisan test --compact
```

## License

Private — all rights reserved.

---

### Coach Sign-Up Flow

1. Coach registers at `/register` — a 3-step wizard:
   - **Step 1:** Choose coaching type (solo / growing / gym)
   - **Step 2:** Optional profile details (name, gym name, niche, client count, current tools)
   - **Step 3:** Email and password
   All text is localized in English, Croatian (`hr`), and Slovenian (`sl`) via `lang/{locale}/auth.php` under the `register.*` key.
2. Redirected to `/coach/plan` — picks a plan:
   - **Basic (€2.50/mo)** — redirected to Stripe Checkout with a 7-day trial. Card required. After trial, auto-charged €2.50/month.
   - **Advanced (€10/mo)** — redirected immediately to Stripe Checkout.
   - **Professional (€15/mo + metered)** — redirected immediately to Stripe Checkout.
3. After Stripe Checkout completes, Cashier webhook activates the subscription and the coach is redirected to the dashboard.
4. Abandoned Stripe Checkout → coach lands on `/coach/subscription` with an option to complete payment.

### Subscription Plans

| Plan         | Price                       | Clients                  | Features                          |
|--------------|-----------------------------|--------------------------|-----------------------------------|
| Basic        | €2.50/mo                    | Up to 5                  | Programs, workout logs, nutrition |
| Advanced     | €10/mo                      | Up to 15                 | + Loyalty & achievements          |
| Professional | €15/mo + per-client overage | 30 included (unlimited+) | + Custom branding                 |

Plans are configured in `config/plans.php`. Stripe price IDs are set via environment variables (`STRIPE_PRICE_BASIC`, `STRIPE_PRICE_ADVANCED`, `STRIPE_PRICE_PROFESSIONAL_FLAT`, `STRIPE_PRICE_PROFESSIONAL_METERED`).

### Required Stripe Webhook Events

The following events must be enabled in the Stripe dashboard and pointed at `/cashier/webhook`:

- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `checkout.session.completed`
- `invoice.payment_succeeded`
- `invoice.payment_failed`

### Environment Variables

```env
STRIPE_KEY=pk_...
STRIPE_SECRET=sk_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_PRICE_BASIC=price_...
STRIPE_PRICE_ADVANCED=price_...
STRIPE_PRICE_PROFESSIONAL_FLAT=price_...
STRIPE_PRICE_PROFESSIONAL_METERED=price_...
```

### Running Locally

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
composer run dev
```

To receive Stripe webhooks locally, run the Stripe CLI in a separate terminal:

```bash
stripe listen --forward-to localhost:8000/cashier/webhook
```

Copy the printed `whsec_...` key into `STRIPE_WEBHOOK_SECRET` in your `.env`.
