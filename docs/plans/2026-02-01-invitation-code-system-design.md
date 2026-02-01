# Invitation Code System Design

## Overview

Replace the current email-based client invitation system with a code-based approach. Coaches generate short invitation codes that clients use to register.

## Database Changes

### `client_invitations` table modifications

- `token`: Change from 64-char to 8-char uppercase alphanumeric
- `email`: Make nullable (no longer required)
- `name`: Already nullable, unchanged

### Token format

- 8 uppercase characters from: `ABCDEFGHJKMNPQRSTUVWXYZ23456789`
- Excludes ambiguous characters (0, O, I, 1, L)
- Must be unique among non-expired invitations

## Coach Flow

### Code generation

1. Coach clicks "Generate Code" button (no form fields required)
2. AJAX request creates invitation and returns code
3. Modal displays:
   - Code in large text (e.g., `XK7M2P9A`)
   - "Copy Code" button
   - Shareable link: `/join/XK7M2P9A`
   - "Copy Link" button

### Pending invitations list

- Shows code instead of email
- Displays expiration time
- Copy button for each code

## Client Registration Flow

### Routes

| Method | Path | Description |
|--------|------|-------------|
| GET | `/join` | Code entry form |
| GET | `/join/{code}` | Pre-filled registration form |
| POST | `/join` | Process registration |

### Registration form

- Name (required)
- Email (required, unique)
- Password + confirmation (required)
- Hidden invitation code

### On successful registration

1. Create user with `role: 'client'` and `coach_id` from invitation
2. Mark invitation as accepted (`accepted_at = now()`)
3. Log user in
4. Redirect to `/client/welcome`

## Welcome & Onboarding Flow

### Routes

| Method | Path | Description |
|--------|------|-------------|
| GET | `/client/welcome` | Welcome page |
| GET | `/client/onboarding` | Questionnaire form |
| POST | `/client/onboarding` | Save profile |
| POST | `/client/onboarding/skip` | Skip onboarding |

### Welcome page

- "Welcome to [App Name]!"
- "You're now connected with [Coach Name]"
- Coach avatar/gym name if available
- "Continue to Setup" button

### Onboarding questionnaire (skippable)

- Goal: fat_loss, strength, general_fitness
- Experience level: beginner, intermediate, advanced
- Injuries (optional)
- Equipment access (optional)
- "Complete Setup" button
- "Skip for now" link

### Completion

- Creates/updates `ClientProfile`
- Sets `onboarding_completed_at` if completed (not skipped)
- Redirects to `/client/dashboard`

## Route Protection

### Guest-only routes

- `/join`
- `/join/{code}`

### Auth + Client routes

- `/client/welcome`
- `/client/onboarding`
- `/client/dashboard`

### Onboarding enforcement

Clients without a `ClientProfile` are redirected to `/client/welcome` when accessing other client pages.

## Files to Create/Modify

### New files

- `app/Http/Controllers/Auth/ClientRegistrationController.php`
- `app/Http/Controllers/Client/OnboardingController.php`
- `resources/views/auth/join.blade.php`
- `resources/views/auth/join-register.blade.php`
- `resources/views/client/welcome.blade.php`
- `resources/views/client/onboarding.blade.php`
- `database/migrations/xxxx_update_client_invitations_for_codes.php`

### Modified files

- `app/Models/ClientInvitation.php` - Update token generation
- `app/Http/Controllers/Coach/ClientController.php` - Simplify store method
- `resources/views/coach/clients/index.blade.php` - Show codes, add copy buttons
- `resources/views/coach/clients/create.blade.php` - Replace with generate button + modal
- `routes/web.php` - Add new routes
