# Image Metrics for Progress Photos

## Overview

Add `image` as a 5th tracking metric type so coaches can define progress photo metrics (e.g. "Front Photo", "Back Photo", "Arms") and assign them to clients. Clients upload one image per metric per day via the existing check-in form. Coach analytics show images in a gallery view with a before/after comparison mode.

## Data Model & Storage

- **TrackingMetric** gains `image` as a new type value (alongside `number`, `scale`, `boolean`, `text`)
- **DailyLog** implements Spatie `HasMedia` interface with `InteractsWithMedia` trait
- Media collection: `check-in-image` — single file, private disk
- Accepted mimetypes: `image/jpeg`, `image/png`, `image/webp`
- Max file size: 10MB
- Conversions: `thumb` (300px), `full` (1920px)
- `value` column stores `"uploaded"` as a marker for image metrics
- Storage: `local` disk (private). S3-compatible in production (configurable via env)

## Privacy & Authorization

- Images are private — no public URLs
- Dedicated route: `GET /media/daily-log/{dailyLog}/{conversion?}`
- Auth check: user is the log owner OR user is the owner's coach
- Local disk: file stream response. S3: temporary signed URLs

## Coach Workflow

### Metric Creation
- `image` added to the type dropdown on tracking metrics page
- When `image` is selected, irrelevant fields (unit, scale min/max) are hidden

### Metric Assignment
- No changes. Existing toggle system works as-is

### Analytics Page
- New section for image metrics below existing charts/tables
- One block per image metric
- **Gallery view** (default): Chronological grid of thumbnails for the date range. Click to view full size
- **Before/After toggle**: Side-by-side comparison. Defaults to first and last image in range. Coach can pick different dates via dropdowns

## Client Check-in Flow

- Image metrics render as a file input / "tap to upload" area
- If image exists for that date: shows thumbnail with replace/remove buttons
- Accepts `.jpg`, `.png`, `.webp`, `.heic` files
- HEIC converted client-side to JPEG via `heic2any` JS library (transparent to user)
- Preview shown before submitting
- Submitted as part of the existing multipart form (single submit button)

## Image Constraints
- One image per metric per day (matches existing one-value-per-metric-per-date pattern)
- Max file size: 10MB
- Formats: JPEG, PNG, WebP (HEIC accepted and converted client-side)

## Technical Details

### Dependencies
- `spatie/laravel-medialibrary` ^11.17 (already in composer.json)
- `heic2any` npm package (client-side HEIC conversion)

### Spatie Setup
- Publish and run medialibrary migration
- DailyLog model: `HasMedia` interface, `InteractsWithMedia` trait
- Single collection `check-in-image`, private disk, conversions registered

### Route
- `GET /media/daily-log/{dailyLog}/{conversion?}` — `auth` middleware + inline authorization

### Controller Changes
- **CheckInController** `store()`: handle file uploads for image-type metrics, create DailyLog + attach media. Removal deletes log entry + media
- **AnalyticsController**: fetch image-type metrics separately, load DailyLog entries with media for the date range

### Form Changes
- Check-in form becomes `multipart/form-data`
- Alpine.js handles HEIC detection + conversion, image preview, replace/remove UI

## Tests
- Coach can create/update image-type metrics
- Client can upload, replace, and remove images on check-in
- Image serving route enforces authorization (client + coach access, others denied)
- Analytics displays image metrics correctly
