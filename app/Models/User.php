<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'coach_id',
        'phone',
        'bio',
        'description',
        'welcome_email_text',
        'onboarding_welcome_text',
        'avatar',
        'gym_name',
        'logo',
        'primary_color',
        'secondary_color',
        'dark_mode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dark_mode' => 'boolean',
        ];
    }

    public function logo(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Storage::temporaryUrl($value, now()->addDay()) : ''
        );
    }

    /**
     * Get the coach that this user belongs to.
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Get all clients for this coach.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(User::class, 'coach_id');
    }

    /**
     * Check if the user is a coach.
     */
    public function isCoach(): bool
    {
        return $this->role === 'coach';
    }

    /**
     * Check if the user is a client.
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Check if the user is a client.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the client profile for this user.
     */
    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }

    /**
     * Get all invitations sent by this coach.
     */
    public function sentInvitations(): HasMany
    {
        return $this->hasMany(ClientInvitation::class, 'coach_id');
    }

    /**
     * Get all programs created by this coach.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'coach_id');
    }

    /**
     * Get all program assignments for this client.
     */
    public function clientPrograms(): HasMany
    {
        return $this->hasMany(ClientProgram::class, 'client_id');
    }

    /**
     * Get the active program for this client.
     */
    public function activeProgram(): ?ClientProgram
    {
        return $this->clientPrograms()->active()->first();
    }

    /**
     * Get all workout logs for this client.
     */
    public function workoutLogs(): HasMany
    {
        return $this->hasMany(WorkoutLog::class, 'client_id');
    }

    /**
     * Get all messages sent by this user.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get all messages received by this user.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get unread message count.
     */
    public function unreadMessagesCount(): int
    {
        return $this->receivedMessages()->unread()->count();
    }

    /**
     * Get tracking metrics defined by this coach.
     */
    public function trackingMetrics(): HasMany
    {
        return $this->hasMany(TrackingMetric::class, 'coach_id')->orderBy('order');
    }

    /**
     * Get onboarding fields defined by this coach.
     */
    public function onboardingFields(): HasMany
    {
        return $this->hasMany(OnboardingField::class, 'coach_id')->orderBy('order');
    }

    /**
     * Get onboarding responses for this client.
     */
    public function onboardingResponses(): HasMany
    {
        return $this->hasMany(OnboardingResponse::class, 'client_id');
    }

    /**
     * Get tracking metric assignments for this client.
     */
    public function assignedTrackingMetrics(): HasMany
    {
        return $this->hasMany(ClientTrackingMetric::class, 'client_id')->orderBy('order');
    }

    /**
     * Get daily log entries for this client.
     */
    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class, 'client_id');
    }

    /**
     * Get macro goals for this client.
     */
    public function macroGoals(): HasMany
    {
        return $this->hasMany(MacroGoal::class, 'client_id');
    }

    /**
     * Get meals created by this coach.
     */
    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class, 'coach_id');
    }

    /**
     * Get meal log entries for this client.
     */
    public function mealLogs(): HasMany
    {
        return $this->hasMany(MealLog::class, 'client_id');
    }
}
