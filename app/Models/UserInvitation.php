<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserInvitation extends Model
{
    protected $fillable = [
        'email',
        'token',
        'invited_by',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public static function createInvitation(string $email, int $invitedBy): self
    {
        return self::create([
            'email' => $email,
            'token' => Str::random(64),
            'invited_by' => $invitedBy,
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function isValid(): bool
    {
        return $this->expires_at->isFuture() && is_null($this->accepted_at);
    }

    public function markAsAccepted(): void
    {
        $this->update(['accepted_at' => now()]);
    }
}