# User Settings Design

## Overview

Both coaches and clients get a Settings page where they can update their profile (name, email, phone, bio, avatar) and change their password.

## Architecture

### Controller
Single `App\Http\Controllers\SettingsController` with three methods:
- `edit` — show settings page (detects role, returns appropriate view)
- `update` — update profile fields
- `updatePassword` — change password

### Routes
Added to both route groups:
```
GET  /coach/settings        coach.settings.edit
PUT  /coach/settings        coach.settings.update
PUT  /coach/settings/password  coach.settings.password

GET  /client/settings       client.settings.edit
PUT  /client/settings       client.settings.update
PUT  /client/settings/password  client.settings.password
```

### Form Request
`App\Http\Requests\UpdateSettingsRequest` — validates name, email (unique ignore self), phone (nullable), bio (nullable), avatar (nullable image, max 2MB).

Password validation is inline in the controller (current_password, new password with confirmation).

### Avatar Storage
Same pattern as `logo` on the User model — stored via `Storage`, accessed via `Storage::temporaryUrl`.

## UI

### Client Navigation
- Top header: circular avatar/initials button (replaces or sits next to the logout button) linking to `client.settings.edit`
- Logout moves to the bottom of the settings page as a "Sign out" button

### Coach Navigation
- Sidebar: "Settings" nav item added near the bottom, grouped with "Branding"

### Settings Page Layout
Two stacked cards:

**Profile Card**
- Avatar upload with current image preview (or initials fallback)
- Name (text input)
- Email (email input)
- Phone (text input, optional)
- Bio (textarea, optional)
- Save button with inline success flash

**Password Card**
- Current password
- New password
- Confirm new password
- Update button with inline success/error flash

## Views
- `resources/views/coach/settings/edit.blade.php` — uses `<x-layouts.coach>`
- `resources/views/client/settings/edit.blade.php` — uses `<x-layouts.client>`
