# Nutritional Planning & Meal Logging

## Overview

Coaches set target macro goals (calories, protein, carbs, fat) per client. Goals have effective dates so history is preserved when targets change. Clients log meals throughout the day — either from a coach-curated meal library or as fully custom entries. Meals are categorized by type (Breakfast, Lunch, Dinner, Snack, or custom).

## Data Model

### `macro_goals`

| Column         | Type           | Notes                              |
|----------------|----------------|------------------------------------|
| id             | bigint         | PK                                 |
| client_id      | FK → users     | The client                         |
| coach_id       | FK → users     | The coach who set it               |
| calories       | integer        | kcal target                        |
| protein        | decimal(6,1)   | grams                              |
| carbs          | decimal(6,1)   | grams                              |
| fat            | decimal(6,1)   | grams                              |
| effective_date | date           | When this goal starts              |
| notes          | text (nullable)| Coach can note why they changed it |
| timestamps     |                |                                    |

Active goal for a date = most recent `effective_date <= date`.

### `meals`

| Column      | Type            | Notes                    |
|-------------|-----------------|--------------------------|
| id          | bigint          | PK                       |
| coach_id    | FK → users      | Creator                  |
| name        | varchar         | e.g. "Chicken & Rice"    |
| description | text (nullable) |                          |
| calories    | integer         |                          |
| protein     | decimal(6,1)    |                          |
| carbs       | decimal(6,1)    |                          |
| fat         | decimal(6,1)    |                          |
| is_active   | boolean         | Soft archive             |
| timestamps  |                 |                          |

### `meal_logs`

| Column    | Type                    | Notes                                    |
|-----------|-------------------------|------------------------------------------|
| id        | bigint                  | PK                                       |
| client_id | FK → users              |                                          |
| meal_id   | FK → meals (nullable)   | If from library, otherwise custom        |
| date      | date                    |                                          |
| meal_type | varchar                 | Breakfast, Lunch, Dinner, Snack, custom  |
| name      | varchar                 | Copied from library or manual entry      |
| calories  | integer                 |                                          |
| protein   | decimal(6,1)            |                                          |
| carbs     | decimal(6,1)            |                                          |
| fat       | decimal(6,1)            |                                          |
| notes     | text (nullable)         |                                          |
| timestamps|                         |                                          |

Macros are denormalized into meal_logs so editing a library meal doesn't change past logs.

## Routes

### Coach

```
GET    /coach/clients/{client}/nutrition       → Coach\NutritionController@show
POST   /coach/clients/{client}/macro-goals     → Coach\MacroGoalController@store
DELETE /coach/macro-goals/{macroGoal}           → Coach\MacroGoalController@destroy

GET    /coach/meals                            → Coach\MealController@index
GET    /coach/meals/create                     → Coach\MealController@create
POST   /coach/meals                            → Coach\MealController@store
GET    /coach/meals/{meal}/edit                → Coach\MealController@edit
PUT    /coach/meals/{meal}                     → Coach\MealController@update
DELETE /coach/meals/{meal}                     → Coach\MealController@destroy
```

### Client

```
GET    /client/nutrition                       → Client\NutritionController@index
POST   /client/nutrition                       → Client\NutritionController@store
DELETE /client/nutrition/{mealLog}             → Client\NutritionController@destroy
GET    /client/nutrition/meals                 → Client\NutritionController@meals (JSON)
```

## UI

### Client Nutrition Page (`/client/nutrition`)

- New 6th tab in bottom navigation
- Date picker (defaults to today)
- Progress bars: daily totals vs macro goals (calories, protein, carbs, fat)
- Logged meals grouped by meal type with delete option
- Add meal form (Alpine.js): toggle Library/Custom mode
  - Library: searchable list, tap to select, pick meal type
  - Custom: name, meal type, calories, protein, carbs, fat fields
- Meal type quick-select: Breakfast, Lunch, Dinner, Snack + custom text input

### Coach Meal Library (`/coach/meals`)

- List/card view of meals with macros
- Create/edit forms
- Archive toggle (is_active)

### Coach Client Nutrition (`/coach/clients/{client}/nutrition`)

- Set Macro Goals form: calories, protein, carbs, fat, effective_date, notes
- Goal History table
- Last 7 days of meal logs with daily totals vs targets, expandable per day

### Client Detail Page (existing)

- Add Nutrition summary card: today's totals vs goal with link to full page

## Key Behaviors

- Goal resolution: `WHERE client_id = ? AND effective_date <= ? ORDER BY effective_date DESC LIMIT 1`
- Library meals: macros copied to meal_log at creation (denormalized)
- Daily totals: computed by summing meal_logs per client+date (no totals table)
- Authorization: role middleware + inline coach_id/client_id checks
