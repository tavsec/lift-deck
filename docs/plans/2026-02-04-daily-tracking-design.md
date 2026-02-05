# Daily Tracking Metrics

## Overview
Coaches define custom daily tracking metrics (weight, steps, mood, etc.) and assign them to clients. Clients log values daily via a dedicated Check-in tab, with ability to edit past entries.

## Data Model

### tracking_metrics (coach's metric library)
- id, coach_id (FK users), name, type (number|scale|boolean|text), unit (nullable), scale_min/scale_max (nullable, default 1/5), order, is_active, timestamps

### client_tracking_metrics (assignment pivot)
- id, client_id (FK users), tracking_metric_id (FK), order, timestamps
- Unique: client_id + tracking_metric_id

### daily_logs (client entries)
- id, client_id (FK users), tracking_metric_id (FK), date, value (text), timestamps
- Unique: client_id + tracking_metric_id + date

## Preseeded Metrics
Body Weight (number, kg), Steps (number, steps), Sleep Quality (scale 1-5), Energy Level (scale 1-5), Mood (scale 1-5), Took Supplements (boolean), Daily Notes (text)

## Coach UI
- Metric library management page (CRUD)
- Per-client assignment toggles on client detail page
- Client daily log viewing (last 7 days grid) on client detail page

## Client UI
- New Check-in tab (5th bottom nav item)
- Date navigation with today as default, past dates editable
- Type-specific inputs: number, 1-5 scale buttons, yes/no toggle, textarea
- Dashboard widget showing today's completion status
