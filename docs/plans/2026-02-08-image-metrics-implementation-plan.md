# Image Metrics Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add `image` as a 5th tracking metric type so coaches can define progress photo metrics and clients can upload images via the daily check-in, with gallery and before/after views in analytics.

**Architecture:** DailyLog model gains Spatie HasMedia to store one image per log entry. A dedicated controller serves private images with authorization checks. Client check-in form becomes multipart with HEIC client-side conversion. Analytics gets a new Progress Photos collapsible section.

**Tech Stack:** Laravel 12, Spatie MediaLibrary v11, Alpine.js, heic2any (npm), Tailwind CSS v3

---

### Task 1: Publish Spatie Media Library migration and run it

**Files:**
- Create: `database/migrations/xxxx_xx_xx_xxxxxx_create_media_table.php` (via artisan)

**Step 1: Publish the migration**

Run:
```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
```

**Step 2: Run the migration**

Run:
```bash
php artisan migrate
```

**Step 3: Commit**

```bash
git add database/migrations/*create_media_table*
git commit -m "chore: publish and run spatie media library migration"
```

---

### Task 2: Add `image` type to TrackingMetric and update DailyLog with HasMedia

**Files:**
- Modify: `app/Models/DailyLog.php`
- Modify: `app/Models/TrackingMetric.php` (only seedDefaults — no code change needed, type is just a string)
- Modify: `database/factories/TrackingMetricFactory.php`
- Test: `tests/Feature/Coach/TrackingMetricImageTest.php`

**Step 1: Write the failing test**

Create `tests/Feature/Coach/TrackingMetricImageTest.php`:

```php
<?php

use App\Models\User;
use App\Models\TrackingMetric;

beforeEach(function () {
    $this->coach = User::factory()->create(['role' => 'coach']);
});

it('can create an image type tracking metric', function () {
    $this->actingAs($this->coach)
        ->post(route('coach.tracking-metrics.store'), [
            'name' => 'Front Progress Photo',
            'type' => 'image',
        ])
        ->assertRedirect(route('coach.tracking-metrics.index'));

    $this->assertDatabaseHas('tracking_metrics', [
        'coach_id' => $this->coach->id,
        'name' => 'Front Progress Photo',
        'type' => 'image',
    ]);
});

it('can create a metric using the image factory state', function () {
    $metric = TrackingMetric::factory()->image()->create(['coach_id' => $this->coach->id]);

    expect($metric->type)->toBe('image');
});
```

**Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=TrackingMetricImageTest`
Expected: FAIL — validation rejects `image` type

**Step 3: Update TrackingMetricController validation to accept `image` type**

In `app/Http/Controllers/Coach/TrackingMetricController.php`, change both `store()` and `update()` validation rules:

```php
'type' => ['required', 'string', 'in:number,scale,boolean,text,image'],
```

**Step 4: Add `image()` factory state**

In `database/factories/TrackingMetricFactory.php`, add after the `text()` method:

```php
public function image(): static
{
    return $this->state(fn () => [
        'type' => 'image',
    ]);
}
```

**Step 5: Add HasMedia to DailyLog model**

In `app/Models/DailyLog.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DailyLog extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\DailyLogFactory> */
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'client_id',
        'tracking_metric_id',
        'date',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function trackingMetric(): BelongsTo
    {
        return $this->belongsTo(TrackingMetric::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('check-in-image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk(config('filesystems.default'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->nonQueued();

        $this->addMediaConversion('full')
            ->width(1920)
            ->height(1920)
            ->nonQueued();
    }
}
```

**Step 6: Run test to verify it passes**

Run: `php artisan test --compact --filter=TrackingMetricImageTest`
Expected: PASS

**Step 7: Commit**

```bash
git add app/Models/DailyLog.php app/Http/Controllers/Coach/TrackingMetricController.php database/factories/TrackingMetricFactory.php tests/Feature/Coach/TrackingMetricImageTest.php
git commit -m "feat: add image metric type with Spatie HasMedia on DailyLog"
```

---

### Task 3: Update coach tracking metrics UI to support image type

**Files:**
- Modify: `resources/views/coach/tracking-metrics/index.blade.php`

**Step 1: Add `image` option to create form type dropdown**

In the create form `<select name="type">`, add after the text option:

```html
<option value="image">Image (progress photo)</option>
```

**Step 2: Hide irrelevant fields when image is selected**

The unit field already shows only for `type === 'number'` and scale fields for `type === 'scale'`. No changes needed — image type naturally hides those fields.

**Step 3: Add `image` option to edit form type dropdown**

In the edit form `<select name="type" x-model="type">`, add:

```html
<option value="image">Image</option>
```

**Step 4: Update display mode type label**

In the active metrics list display, add an `@elseif` for image type:

```blade
@elseif($metric->type === 'image')
    Image (progress photo)
```

Also in the inactive metrics section.

**Step 5: Verify manually or run existing tests**

Run: `php artisan test --compact --filter=TrackingMetric`

**Step 6: Commit**

```bash
git add resources/views/coach/tracking-metrics/index.blade.php
git commit -m "feat: add image type to coach tracking metrics UI"
```

---

### Task 4: Add media serving route with authorization

**Files:**
- Create: `app/Http/Controllers/MediaController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/MediaAccessTest.php`

**Step 1: Write the failing test**

Create `tests/Feature/MediaAccessTest.php`:

```php
<?php

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');

    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $this->metric = TrackingMetric::factory()->image()->create(['coach_id' => $this->coach->id]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->metric->id,
    ]);

    $this->log = DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => 'uploaded',
    ]);

    $this->log->addMedia(UploadedFile::fake()->image('photo.jpg', 800, 600))
        ->toMediaCollection('check-in-image');
});

it('allows the client to view their own image', function () {
    $this->actingAs($this->client)
        ->get(route('media.daily-log', $this->log))
        ->assertOk()
        ->assertHeader('content-type', 'image/jpeg');
});

it('allows the coach to view their clients image', function () {
    $this->actingAs($this->coach)
        ->get(route('media.daily-log', $this->log))
        ->assertOk();
});

it('denies access to unrelated users', function () {
    $stranger = User::factory()->create(['role' => 'coach']);

    $this->actingAs($stranger)
        ->get(route('media.daily-log', $this->log))
        ->assertForbidden();
});

it('returns the thumb conversion when requested', function () {
    $this->actingAs($this->client)
        ->get(route('media.daily-log', [$this->log, 'thumb']))
        ->assertOk();
});

it('returns 404 for log without media', function () {
    $emptyLog = DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->metric->id,
        'date' => now()->subDay()->format('Y-m-d'),
        'value' => 'uploaded',
    ]);

    $this->actingAs($this->client)
        ->get(route('media.daily-log', $emptyLog))
        ->assertNotFound();
});

it('requires authentication', function () {
    $this->get(route('media.daily-log', $this->log))
        ->assertRedirect(route('login'));
});
```

**Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=MediaAccessTest`
Expected: FAIL — route not defined

**Step 3: Create MediaController**

Run: `php artisan make:controller MediaController --no-interaction`

Then implement `app/Http/Controllers/MediaController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function dailyLog(DailyLog $dailyLog, ?string $conversion = null): Response
    {
        $user = auth()->user();
        $isOwner = $dailyLog->client_id === $user->id;
        $isCoach = $dailyLog->client->coach_id === $user->id;

        if (! $isOwner && ! $isCoach) {
            abort(403);
        }

        $media = $dailyLog->getFirstMedia('check-in-image');

        if (! $media) {
            abort(404);
        }

        $validConversions = ['thumb', 'full'];
        if ($conversion && in_array($conversion, $validConversions)) {
            $path = $media->getPath($conversion);
        } else {
            $path = $media->getPath();
        }

        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => $media->mime_type,
        ]);
    }
}
```

**Step 4: Add route**

In `routes/web.php`, add after the `require __DIR__.'/auth.php';` line (or before it, just outside the role groups), inside an `auth` middleware group:

```php
// Media serving (private, authorized)
Route::middleware('auth')->group(function () {
    Route::get('media/daily-log/{dailyLog}/{conversion?}', [\App\Http\Controllers\MediaController::class, 'dailyLog'])->name('media.daily-log');
});
```

**Step 5: Run test to verify it passes**

Run: `php artisan test --compact --filter=MediaAccessTest`
Expected: PASS

**Step 6: Commit**

```bash
git add app/Http/Controllers/MediaController.php routes/web.php tests/Feature/MediaAccessTest.php
git commit -m "feat: add authorized media serving route for daily log images"
```

---

### Task 5: Install heic2any and update client check-in form for image upload

**Files:**
- Modify: `package.json` (npm install)
- Modify: `resources/views/client/check-in.blade.php`
- Modify: `app/Http/Controllers/Client/CheckInController.php`
- Test: `tests/Feature/Client/CheckInImageTest.php`

**Step 1: Install heic2any**

Run:
```bash
npm install heic2any
```

**Step 2: Write the failing test**

Create `tests/Feature/Client/CheckInImageTest.php`:

```php
<?php

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');

    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
    $this->imageMetric = TrackingMetric::factory()->image()->create(['coach_id' => $this->coach->id]);
    $this->numberMetric = TrackingMetric::factory()->number()->create(['coach_id' => $this->coach->id]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->imageMetric->id,
    ]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->numberMetric->id,
    ]);
});

it('displays image upload field for image metrics', function () {
    $this->actingAs($this->client)
        ->get(route('client.check-in'))
        ->assertOk()
        ->assertSee($this->imageMetric->name)
        ->assertSee('type="file"', false);
});

it('can upload an image for an image metric', function () {
    $file = UploadedFile::fake()->image('progress.jpg', 800, 600);

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [
                $this->numberMetric->id => '82.5',
            ],
            'images' => [
                $this->imageMetric->id => $file,
            ],
        ])
        ->assertRedirect(route('client.check-in', ['date' => now()->format('Y-m-d')]));

    $log = DailyLog::where('client_id', $this->client->id)
        ->where('tracking_metric_id', $this->imageMetric->id)
        ->first();

    expect($log)->not->toBeNull();
    expect($log->value)->toBe('uploaded');
    expect($log->getFirstMedia('check-in-image'))->not->toBeNull();
});

it('can replace an existing image', function () {
    $log = DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->imageMetric->id,
        'date' => now()->format('Y-m-d'),
        'value' => 'uploaded',
    ]);
    $log->addMedia(UploadedFile::fake()->image('old.jpg'))
        ->toMediaCollection('check-in-image');

    $newFile = UploadedFile::fake()->image('new.jpg', 1200, 900);

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [],
            'images' => [
                $this->imageMetric->id => $newFile,
            ],
        ])
        ->assertRedirect();

    $log->refresh();
    $media = $log->getFirstMedia('check-in-image');
    expect($media)->not->toBeNull();
    expect($media->file_name)->toContain('new');
});

it('can remove an image by sending remove flag', function () {
    $log = DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $this->imageMetric->id,
        'date' => now()->format('Y-m-d'),
        'value' => 'uploaded',
    ]);
    $log->addMedia(UploadedFile::fake()->image('photo.jpg'))
        ->toMediaCollection('check-in-image');

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [],
            'remove_images' => [
                $this->imageMetric->id => '1',
            ],
        ])
        ->assertRedirect();

    expect(DailyLog::where('client_id', $this->client->id)
        ->where('tracking_metric_id', $this->imageMetric->id)
        ->whereDate('date', now())
        ->first()
    )->toBeNull();
});

it('rejects non-image files', function () {
    $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [],
            'images' => [
                $this->imageMetric->id => $file,
            ],
        ])
        ->assertSessionHasErrors('images.' . $this->imageMetric->id);
});

it('rejects files over 10MB', function () {
    $file = UploadedFile::fake()->image('huge.jpg')->size(11000);

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [],
            'images' => [
                $this->imageMetric->id => $file,
            ],
        ])
        ->assertSessionHasErrors('images.' . $this->imageMetric->id);
});

it('ignores image uploads for non-image metric types', function () {
    $file = UploadedFile::fake()->image('trick.jpg');

    $this->actingAs($this->client)
        ->post(route('client.check-in.store'), [
            'date' => now()->format('Y-m-d'),
            'metrics' => [
                $this->numberMetric->id => '80',
            ],
            'images' => [
                $this->numberMetric->id => $file,
            ],
        ])
        ->assertRedirect();

    $log = DailyLog::where('client_id', $this->client->id)
        ->where('tracking_metric_id', $this->numberMetric->id)
        ->first();

    expect($log->getMedia('check-in-image'))->toHaveCount(0);
});
```

**Step 3: Run test to verify it fails**

Run: `php artisan test --compact --filter=CheckInImageTest`
Expected: FAIL

**Step 4: Update CheckInController**

Replace `app/Http/Controllers/Client/CheckInController.php`:

```php
<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckInController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $date = $request->get('date', now()->format('Y-m-d'));

        $assignedMetrics = $user->assignedTrackingMetrics()
            ->with('trackingMetric')
            ->get()
            ->pluck('trackingMetric')
            ->filter();

        $existingLogs = $user->dailyLogs()
            ->whereDate('date', $date)
            ->get()
            ->keyBy('tracking_metric_id');

        // Eager load media for image metrics
        $imageLogIds = $existingLogs->filter(fn ($log) => $log->value === 'uploaded')->pluck('id');
        if ($imageLogIds->isNotEmpty()) {
            $logsWithMedia = DailyLog::whereIn('id', $imageLogIds)->with('media')->get()->keyBy('id');
            foreach ($existingLogs as $metricId => $log) {
                if ($logsWithMedia->has($log->id)) {
                    $existingLogs[$metricId] = $logsWithMedia[$log->id];
                }
            }
        }

        return view('client.check-in', compact('assignedMetrics', 'existingLogs', 'date'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'metrics' => ['nullable', 'array'],
            'metrics.*' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['nullable', 'string'],
        ]);

        $assignedMetrics = $user->assignedTrackingMetrics()
            ->with('trackingMetric')
            ->get();

        $assignedMetricIds = $assignedMetrics->pluck('tracking_metric_id')->toArray();
        $imageMetricIds = $assignedMetrics
            ->filter(fn ($am) => $am->trackingMetric?->type === 'image')
            ->pluck('tracking_metric_id')
            ->toArray();

        // Handle regular metrics
        foreach (($validated['metrics'] ?? []) as $metricId => $value) {
            if (! in_array((int) $metricId, $assignedMetricIds)) {
                continue;
            }

            // Skip image metrics in regular handling
            if (in_array((int) $metricId, $imageMetricIds)) {
                continue;
            }

            if ($value === null || $value === '') {
                DailyLog::where('client_id', $user->id)
                    ->where('tracking_metric_id', $metricId)
                    ->whereDate('date', $validated['date'])
                    ->delete();

                continue;
            }

            DailyLog::updateOrCreate(
                [
                    'client_id' => $user->id,
                    'tracking_metric_id' => $metricId,
                    'date' => $validated['date'],
                ],
                ['value' => $value],
            );
        }

        // Handle image removals
        foreach (($validated['remove_images'] ?? []) as $metricId => $flag) {
            if (! in_array((int) $metricId, $imageMetricIds)) {
                continue;
            }

            $log = DailyLog::where('client_id', $user->id)
                ->where('tracking_metric_id', $metricId)
                ->whereDate('date', $validated['date'])
                ->first();

            if ($log) {
                $log->clearMediaCollection('check-in-image');
                $log->delete();
            }
        }

        // Handle image uploads
        foreach (($validated['images'] ?? []) as $metricId => $file) {
            if (! in_array((int) $metricId, $imageMetricIds)) {
                continue;
            }

            if (! $file) {
                continue;
            }

            $log = DailyLog::updateOrCreate(
                [
                    'client_id' => $user->id,
                    'tracking_metric_id' => $metricId,
                    'date' => $validated['date'],
                ],
                ['value' => 'uploaded'],
            );

            $log->addMedia($file)
                ->toMediaCollection('check-in-image');
        }

        return redirect()->route('client.check-in', ['date' => $validated['date']])
            ->with('success', 'Check-in saved!');
    }
}
```

**Step 5: Run test to verify it passes**

Run: `php artisan test --compact --filter=CheckInImageTest`
Expected: PASS

**Step 6: Commit**

```bash
git add app/Http/Controllers/Client/CheckInController.php tests/Feature/Client/CheckInImageTest.php package.json package-lock.json
git commit -m "feat: handle image upload/replace/remove in client check-in"
```

---

### Task 6: Update client check-in Blade view for image upload UI

**Files:**
- Modify: `resources/views/client/check-in.blade.php`

**Step 1: Make form multipart**

Change the `<form>` tag to include `enctype`:

```html
<form method="POST" action="{{ route('client.check-in.store') }}" enctype="multipart/form-data" class="space-y-4">
```

**Step 2: Add image metric input block**

After the `@elseif($metric->type === 'text')` block and before `@endif`, add:

```blade
@elseif($metric->type === 'image')
    @php $existingLog = $existingLogs->get($metric->id); @endphp
    <div x-data="imageUpload({{ $metric->id }}, {{ $existingLog && $existingLog->getFirstMedia('check-in-image') ? 'true' : 'false' }}, '{{ $existingLog && $existingLog->getFirstMedia('check-in-image') ? route('media.daily-log', [$existingLog, 'thumb']) : '' }}')" class="space-y-2">
        <!-- Existing image preview -->
        <template x-if="hasExisting && !removed && !previewUrl">
            <div class="relative inline-block">
                <img :src="existingThumbUrl" alt="Current photo" class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                <button type="button" @click="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow hover:bg-red-600">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>

        <!-- New image preview -->
        <template x-if="previewUrl">
            <div class="relative inline-block">
                <img :src="previewUrl" alt="New photo preview" class="w-32 h-32 object-cover rounded-lg border border-blue-300">
                <button type="button" @click="clearSelection()" class="absolute -top-2 -right-2 bg-gray-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow hover:bg-gray-600">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>

        <!-- Upload area -->
        <template x-if="!previewUrl && (!hasExisting || removed)">
            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-xs text-gray-500">Tap to upload photo</p>
                </div>
                <input type="file" class="hidden" accept="image/jpeg,image/png,image/webp,image/heic,image/heif" @change="handleFileSelect($event)">
            </label>
        </template>

        <!-- Hidden fields -->
        <input type="file" x-ref="fileInput" :name="'images[' + metricId + ']'" class="hidden">
        <template x-if="removed">
            <input type="hidden" :name="'remove_images[' + metricId + ']'" value="1">
        </template>

        <!-- Converting indicator -->
        <template x-if="converting">
            <p class="text-xs text-blue-600">Converting image...</p>
        </template>
    </div>
```

**Step 3: Add the Alpine.js imageUpload function and HEIC handling**

In the `@push('scripts')` section, add:

```html
<script type="module">
    import heic2any from '/node_modules/heic2any/dist/heic2any.js';
    window.heic2any = heic2any;
</script>
<script>
    function imageUpload(metricId, hasExisting, existingThumbUrl) {
        return {
            metricId,
            hasExisting,
            existingThumbUrl,
            previewUrl: null,
            removed: false,
            converting: false,

            async handleFileSelect(event) {
                let file = event.target.files[0];
                if (!file) return;

                // HEIC conversion
                if (file.type === 'image/heic' || file.type === 'image/heif' || file.name.toLowerCase().endsWith('.heic') || file.name.toLowerCase().endsWith('.heif')) {
                    if (window.heic2any) {
                        this.converting = true;
                        try {
                            const blob = await window.heic2any({ blob: file, toType: 'image/jpeg', quality: 0.9 });
                            file = new File([blob], file.name.replace(/\.heic$/i, '.jpg').replace(/\.heif$/i, '.jpg'), { type: 'image/jpeg' });
                        } catch (e) {
                            console.error('HEIC conversion failed:', e);
                            alert('Could not convert this image. Please try a JPEG or PNG instead.');
                            this.converting = false;
                            return;
                        }
                        this.converting = false;
                    }
                }

                // Set the file on the hidden file input
                const dt = new DataTransfer();
                dt.items.add(file);
                this.$refs.fileInput.files = dt.files;

                // Show preview
                this.previewUrl = URL.createObjectURL(file);
                this.removed = false;
            },

            removeImage() {
                this.removed = true;
                this.previewUrl = null;
                this.$refs.fileInput.value = '';
            },

            clearSelection() {
                this.previewUrl = null;
                this.$refs.fileInput.value = '';
            }
        };
    }
</script>
```

> **Note:** The `heic2any` import path may need adjustment after build. If using Vite, it would be better to import heic2any in the app's JS entry point and attach it to window. Check if the project bundles Alpine via Vite — if so, import heic2any there instead. If Alpine is loaded via CDN, the module script import above is the pragmatic approach. Adjust based on the project's actual bundling setup.

**Step 4: Run npm build and verify**

Run: `npm run build`

**Step 5: Commit**

```bash
git add resources/views/client/check-in.blade.php
git commit -m "feat: add image upload UI with HEIC conversion to client check-in"
```

---

### Task 7: Add Progress Photos section to coach analytics

**Files:**
- Modify: `app/Http/Controllers/Coach/AnalyticsController.php`
- Modify: `resources/views/coach/clients/analytics.blade.php`
- Test: `tests/Feature/Coach/AnalyticsImageTest.php`

**Step 1: Write the failing test**

Create `tests/Feature/Coach/AnalyticsImageTest.php`:

```php
<?php

use App\Models\ClientTrackingMetric;
use App\Models\DailyLog;
use App\Models\TrackingMetric;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');

    $this->coach = User::factory()->create(['role' => 'coach']);
    $this->client = User::factory()->create(['role' => 'client', 'coach_id' => $this->coach->id]);
});

it('displays progress photos section when image metrics exist', function () {
    $metric = TrackingMetric::factory()->image()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Front Progress Photo',
    ]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
    ]);

    $log = DailyLog::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
        'date' => now()->format('Y-m-d'),
        'value' => 'uploaded',
    ]);

    $log->addMedia(UploadedFile::fake()->image('front.jpg', 800, 600))
        ->toMediaCollection('check-in-image');

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('Progress Photos')
        ->assertSee('Front Progress Photo');
});

it('shows empty state when no progress photos exist', function () {
    $metric = TrackingMetric::factory()->image()->create([
        'coach_id' => $this->coach->id,
        'name' => 'Body Photo',
    ]);

    ClientTrackingMetric::factory()->create([
        'client_id' => $this->client->id,
        'tracking_metric_id' => $metric->id,
    ]);

    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertSee('No progress photos');
});

it('does not show progress photos section when no image metrics assigned', function () {
    $this->actingAs($this->coach)
        ->get(route('coach.clients.analytics', $this->client))
        ->assertOk()
        ->assertDontSee('Progress Photos');
});
```

**Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=AnalyticsImageTest`
Expected: FAIL

**Step 3: Update AnalyticsController**

In `app/Http/Controllers/Coach/AnalyticsController.php`, add after the line that sets `$tableMetrics`:

```php
$imageMetrics = $assignedMetrics->where('type', 'image');

$imageMetricData = [];
foreach ($imageMetrics as $metric) {
    $logs = $dailyLogs->where('tracking_metric_id', $metric->id)
        ->where('value', 'uploaded');

    // Eager load media for these logs
    if ($logs->isNotEmpty()) {
        $logIds = $logs->pluck('id');
        $logsWithMedia = DailyLog::whereIn('id', $logIds)
            ->with('media')
            ->orderBy('date')
            ->get();
    } else {
        $logsWithMedia = collect();
    }

    $images = [];
    foreach ($logsWithMedia as $log) {
        $media = $log->getFirstMedia('check-in-image');
        if ($media) {
            $images[] = [
                'date' => $log->date->format('Y-m-d'),
                'dateFormatted' => $log->date->format('M j, Y'),
                'thumbUrl' => route('media.daily-log', [$log, 'thumb']),
                'fullUrl' => route('media.daily-log', [$log, 'full']),
            ];
        }
    }

    $imageMetricData[] = [
        'name' => $metric->name,
        'images' => $images,
    ];
}
```

Add `$imageMetrics` and `$imageMetricData` to the `compact()` call in the return statement.

Also add `use App\Models\DailyLog;` at the top of the controller.

**Step 4: Update analytics view**

In `resources/views/coach/clients/analytics.blade.php`, add a new section after the Exercise Progression section (before `</div>` closing the main space-y-6):

```blade
<!-- Progress Photos -->
@if($imageMetrics->count() > 0)
    <div x-data="{ open: true }" class="bg-white rounded-lg shadow">
        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-left">
            <h2 class="text-lg font-semibold text-gray-900">Progress Photos</h2>
            <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" x-collapse class="px-4 pb-4 space-y-6">
            @foreach($imageMetricData as $metricData)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">{{ $metricData['name'] }}</h3>

                    @if(count($metricData['images']) > 0)
                        <div x-data="progressPhotos({{ json_encode($metricData['images']) }})" class="space-y-4">
                            <!-- View toggle -->
                            <div class="flex gap-2">
                                <button type="button" @click="view = 'gallery'"
                                    :class="view === 'gallery' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                    class="px-3 py-1.5 text-xs font-medium border rounded-md transition-colors">
                                    Gallery
                                </button>
                                <button type="button" @click="view = 'compare'"
                                    :class="view === 'compare' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                    class="px-3 py-1.5 text-xs font-medium border rounded-md transition-colors"
                                    x-show="images.length >= 2">
                                    Before / After
                                </button>
                            </div>

                            <!-- Gallery view -->
                            <div x-show="view === 'gallery'" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                                <template x-for="(img, index) in images" :key="img.date">
                                    <div class="cursor-pointer" @click="openLightbox(index)">
                                        <img :src="img.thumbUrl" :alt="img.dateFormatted" class="w-full aspect-square object-cover rounded-lg border border-gray-200 hover:border-blue-400 transition-colors">
                                        <p class="text-xs text-gray-500 text-center mt-1" x-text="img.dateFormatted"></p>
                                    </div>
                                </template>
                            </div>

                            <!-- Before/After view -->
                            <div x-show="view === 'compare'" class="space-y-3">
                                <div class="flex gap-4">
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Before</label>
                                        <select x-model="beforeIndex" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <template x-for="(img, index) in images" :key="'before-' + index">
                                                <option :value="index" x-text="img.dateFormatted"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">After</label>
                                        <select x-model="afterIndex" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                            <template x-for="(img, index) in images" :key="'after-' + index">
                                                <option :value="index" x-text="img.dateFormatted"></option>
                                            </template>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <img :src="images[beforeIndex]?.fullUrl" class="w-full rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-500 text-center mt-1" x-text="images[beforeIndex]?.dateFormatted"></p>
                                    </div>
                                    <div>
                                        <img :src="images[afterIndex]?.fullUrl" class="w-full rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-500 text-center mt-1" x-text="images[afterIndex]?.dateFormatted"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Lightbox -->
                            <div x-show="lightboxOpen" x-cloak @keydown.escape.window="lightboxOpen = false"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" @click.self="lightboxOpen = false">
                                <button @click="lightboxOpen = false" class="absolute top-4 right-4 text-white hover:text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                <button x-show="lightboxIndex > 0" @click="lightboxIndex--" class="absolute left-4 text-white hover:text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button x-show="lightboxIndex < images.length - 1" @click="lightboxIndex++" class="absolute right-12 text-white hover:text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                                <div class="max-h-[90vh] max-w-[90vw]">
                                    <img :src="images[lightboxIndex]?.fullUrl" class="max-h-[85vh] max-w-full object-contain rounded-lg">
                                    <p class="text-white text-center text-sm mt-2" x-text="images[lightboxIndex]?.dateFormatted"></p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No progress photos for this period.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif
```

**Step 5: Add the progressPhotos Alpine function**

In the `@push('scripts')` section, add:

```html
<script>
    function progressPhotos(images) {
        return {
            images,
            view: 'gallery',
            beforeIndex: 0,
            afterIndex: Math.max(0, images.length - 1),
            lightboxOpen: false,
            lightboxIndex: 0,

            openLightbox(index) {
                this.lightboxIndex = index;
                this.lightboxOpen = true;
            }
        };
    }
</script>
```

**Step 6: Run tests to verify**

Run: `php artisan test --compact --filter=AnalyticsImageTest`
Expected: PASS

Also run existing analytics tests:
Run: `php artisan test --compact --filter=AnalyticsTest`
Expected: PASS (existing tests unaffected)

**Step 7: Commit**

```bash
git add app/Http/Controllers/Coach/AnalyticsController.php resources/views/coach/clients/analytics.blade.php tests/Feature/Coach/AnalyticsImageTest.php
git commit -m "feat: add progress photos gallery and before/after view to analytics"
```

---

### Task 8: Run full test suite and fix any issues

**Step 1: Run Pint**

Run: `vendor/bin/pint --dirty --format agent`

**Step 2: Run full test suite**

Run: `php artisan test --compact`

**Step 3: Fix any failures**

Address any test failures or regressions.

**Step 4: Build frontend assets**

Run: `npm run build`

**Step 5: Final commit if needed**

```bash
git add -A
git commit -m "chore: formatting and build"
```
