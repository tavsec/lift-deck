<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ClientInvitation extends Model
{
    protected $fillable = [
        'coach_id',
        'email',
        'name',
        'token',
        'accepted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isPending(): bool
    {
        return ! $this->isAccepted() && ! $this->isExpired();
    }

    public static function generateToken(): string
    {
        $characters = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $token = '';

        for ($i = 0; $i < 8; $i++) {
            $token .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $token;
    }

    public static function generateUniqueToken(): string
    {
        do {
            $token = self::generateToken();
        } while (self::where('token', $token)->whereNull('accepted_at')->where('expires_at', '>', now())->exists());

        return $token;
    }
}
