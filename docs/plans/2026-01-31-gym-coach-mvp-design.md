# Gym Coach MVP - Design Document

**Date:** 2026-01-31
**Status:** In Progress (5/6 phases complete)
**Scope:** V1 MVP - Core coaching flow

## Overview

Web-first MVP for a gym coaching platform that replaces "WhatsApp + spreadsheets" workflow. Mobile-responsive UI so coaches and clients can use it on any device via browser.

### MVP Features (V1)

1. Coach authentication & branding
2. Client onboarding & management
3. Training program builder
4. In-app messaging

### Deferred to V1.1

- Meal planning & nutrition tracking
- Scheduling & payments
- Progress charts & tracking
- Flutter mobile app

## Technology Stack

- **Backend:** Laravel 12
- **Views:** Blade + BladewindUI components
- **Interactivity:** Livewire (forms, messaging, builder)
- **Styling:** Tailwind CSS v4 (mobile-responsive)
- **Database:** SQLite (dev) / MySQL (production)
- **Auth:** Laravel Breeze

## Architecture

### User Model Structure

Single `users` table with role-based access:

- Coaches: `role = 'coach'`, `coach_id = null`
- Clients: `role = 'client'`, `coach_id` references their coach

### Route Structure

```
/                    - Landing page
/login, /register    - Auth (coaches register, clients invited)
/coach/*             - Coach dashboard, clients, programs, messages
/client/*            - Client portal, program, workout log, messages
```

### Middleware

- `auth` - Must be logged in
- `role:coach` - Coach-only routes
- `role:client` - Client-only routes

## Database Schema

### users (extend existing)

| Column | Type | Notes |
|--------|------|-------|
| role | enum('coach', 'client') | Required |
| coach_id | foreign_key | Nullable, clients only |
| phone | string | Nullable |
| bio | text | Nullable |
| avatar | string | Nullable |
| gym_name | string | Coach only |
| logo | string | Coach only |
| primary_color | string | Coach only, hex code |

### client_profiles

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary |
| user_id | foreign_key | Links to users |
| goal | enum | fat_loss, strength, general_fitness |
| experience_level | enum | beginner, intermediate, advanced |
| injuries | text | Nullable, free text |
| equipment_access | text | Nullable |
| availability | json | Nullable |
| onboarding_completed_at | timestamp | Nullable |

### exercises

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary |
| name | string | Required |
| description | text | Nullable |
| muscle_group | string | e.g., chest, back, legs |
| video_url | string | YouTube link |
| coach_id | foreign_key | Nullable (null = global/seeded) |
| is_active | boolean | Default true |

### programs

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary |
| coach_id | foreign_key | Required |
| name | string | Required |
| description | text | Nullable |
| duration_weeks | integer | Nullable |
| type | string | strength, hypertrophy, fat_loss, general |
| is_template | boolean | Default false |

### program_workouts

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary |
| program_id | foreign_key | Required |
| name | string | e.g., "Day 1 - Push" |
| day_number | integer | Required |
| notes | text | Nullable |
| order | integer | For sorting |

### workout_exercises

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary |
| program_workout_id | foreign_key | Required |
| exercise_id | foreign_key | Required |
| sets | integer | Required |
| reps | string | e.g., "8-12" or "10" |
| rest_seconds | integer | Nullable |
| notes | text | Nullable |
| order | integer | For sorting |

### client_programs

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary |
| client_id | foreign_key | References users |
| program_id | foreign_key | Required |
| started_at | date | Required |
| completed_at | date | Nullable |
| status | enum | active, completed, paused |

### workout_logs

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary |
| client_id | foreign_key | References users |
| program_workout_id | foreign_key | Required |
| logged_at | timestamp | Required |
| notes | text | Nullable |
| difficulty_rating | integer | 1-10, nullable |
| completed | boolean | Default false |

### workout_log_sets

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary |
| workout_log_id | foreign_key | Required |
| workout_exercise_id | foreign_key | Required |
| set_number | integer | Required |
| reps_completed | integer | Nullable |
| weight_used | decimal | Nullable |
| notes | text | Nullable |

### messages

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary |
| sender_id | foreign_key | References users |
| receiver_id | foreign_key | References users |
| body | text | Required |
| read_at | timestamp | Nullable |
| created_at | timestamp | Required |

## Feature Specifications

### Coach Dashboard (`/coach`)

- Overview cards: Active clients, unread messages, recent activity
- Quick action buttons: Add client, create program
- Recent client activity feed

### Client Management (`/coach/clients`)

- **List view:** Searchable table with filters (goal, tag)
- **Client card:** Name, avatar, goal, current program, last active
- **Add client:** Email invite flow
  - Coach enters client email
  - System sends invite with registration link
  - Link contains token linking to coach
- **Client detail page:**
  - Profile info (from onboarding)
  - Current assigned program
  - Workout log history
  - Direct message link

### Program Builder (`/coach/programs`)

- **List view:** All programs with template badge
- **Create/Edit form:**
  - Basic info: name, description, duration, type
  - Workouts section: Add days/workouts
  - Per workout: Search and add exercises
  - Per exercise: Set sets, reps, rest, notes
  - Reorder with up/down buttons (no drag-drop)
  - "Save as template" checkbox
- **Assign flow:**
  - From program: "Assign to client" button
  - Select client from dropdown
  - Set start date
  - Creates client_program record

### Exercise Library (`/coach/exercises`)

- **List view:** Searchable, filterable by muscle group
- **Pre-seeded:** ~50 common exercises from public database
- **Exercise detail:** Name, description, embedded YouTube video
- **Add custom:** Coach can add their own exercises

### Messaging (`/coach/messages`)

- **Inbox:** List of conversations with clients
- **Unread indicator:** Badge count
- **Thread view:**
  - Simple chat bubbles (coach right, client left)
  - Text input at bottom
  - Livewire polling every 10 seconds
  - No typing indicators (MVP)

### Client Portal (`/client`)

- **Dashboard:** Welcome, current program overview
- **My Program:** List of workouts in assigned program
- **Today's Workout:**
  - Shows exercises with target sets/reps
  - "Log Workout" button
- **Log Workout:**
  - Per exercise: Enter weight and reps for each set
  - Overall difficulty rating (1-10)
  - Notes field
  - Submit creates workout_log + workout_log_sets
- **Messages:** Chat with coach (same UI as coach side)

### Client Onboarding

When client registers via invite:

1. Set password
2. Multi-step form:
   - Goal selection (fat loss / strength / general fitness)
   - Experience level
   - Injuries/limitations (optional)
   - Equipment access
   - Availability
3. Redirect to client dashboard

## UI Approach

- **BladewindUI components:** Cards, modals, tables, forms, buttons, alerts
- **Mobile-first responsive:** All views work on phone browsers
- **Simple interactions:** No complex drag-drop, use standard form controls
- **Livewire for:**
  - Exercise search in program builder
  - Message thread updates
  - Form submissions without page reload

## Implementation Phases

### Phase 1: Foundation ✅

- [x] Extend User model (role, coach_id, profile fields)
- [x] Create all migrations
- [x] Role middleware
- [x] Coach and client layouts (Blade + Tailwind CSS)
- [x] Coach registration
- [x] Basic dashboard shells

### Phase 2: Client Management ✅

- [x] Client invitation system
- [ ] Client registration from invite
- [ ] Client onboarding form
- [x] Client list view
- [x] Client detail page

### Phase 3: Exercise Library ✅

- [x] Exercise model and migration
- [x] Research public exercise API/database
- [x] Seed ~50 exercises (51 exercises seeded)
- [x] Exercise list with search/filter
- [x] Add custom exercise
- [x] Exercise detail with video

### Phase 4: Program Builder ✅

- [x] Program CRUD
- [x] Workout (day) management
- [x] Add exercises to workouts
- [x] Reorder functionality
- [x] Save as template
- [x] Assign program to client

### Phase 5: Client Workout Experience

- [ ] Client views assigned program
- [ ] Today's workout view
- [ ] Workout logging form
- [ ] Workout history view

### Phase 6: Messaging ✅

- [x] Message model
- [x] Coach inbox
- [x] Chat thread with JS polling (10 seconds)
- [x] Unread indicators
- [x] Client messaging view

## Notes

- **Exercise data:** Research Wger API, ExerciseDB, or open datasets for seeding
- **No tests for MVP:** Focus on shipping, add tests in V1.1
- **No payments/scheduling:** Deferred to V1.1
- **No nutrition:** Deferred to V1.1
