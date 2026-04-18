<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'tax_number',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}