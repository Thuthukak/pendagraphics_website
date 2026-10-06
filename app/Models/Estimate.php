<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'name',
        'email',
        'total_amount',
        'expiry_date',
        'status',
        'notes',
        'terms',
        'invoice_id',
        'converted_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'expiry_date' => 'date',
        'converted_at' => 'datetime',
    ];

    // Statuses that come from the public quote-request flow and email delivery.
    public const STATUS_PENDING = 'pending';
    public const STATUS_EMAILED = 'emailed';
    public const STATUS_EMAIL_FAILED = 'email_failed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_CONVERTED = 'converted';

    public const EDITABLE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_EMAILED,
        self::STATUS_EMAIL_FAILED,
        self::STATUS_COMPLETED,
    ];

    // Relationships
    public function services(): HasMany
    {
        return $this->hasMany(EstimateService::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('quote_number', 'like', "%{$term}%");
        });
    }

    public function scopeByDateRange($query, $from, $to)
    {
        if ($from) {
            $query->where('created_at', '>=', $from);
        }
        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        return $query;
    }

    public function scopeExpired($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CONVERTED, self::STATUS_CANCELLED])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now());
    }

    // Accessors
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date
            && !in_array($this->status, [self::STATUS_CONVERTED, self::STATUS_CANCELLED])
            && $this->expiry_date->isPast();
    }

    public function getIsConvertedAttribute(): bool
    {
        return $this->status === self::STATUS_CONVERTED;
    }

    public function getIsEditableAttribute(): bool
    {
        return in_array($this->status, self::EDITABLE_STATUSES);
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return 'R' . number_format((float) $this->total_amount, 2);
    }

    // Methods
    public function generateQuoteNumber(): string
    {
        $year = now()->year;
        $lastEstimate = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastEstimate && $lastEstimate->quote_number
            ? intval(substr($lastEstimate->quote_number, -4)) + 1
            : 1;

        return 'QUO-' . $year . '-' . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Link this quotation to the invoice it was converted into and lock it.
     */
    public function markAsConverted(Invoice $invoice): void
    {
        $this->update([
            'invoice_id' => $invoice->id,
            'status' => self::STATUS_CONVERTED,
            'converted_at' => now(),
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($estimate) {
            if (empty($estimate->quote_number)) {
                $estimate->quote_number = $estimate->generateQuoteNumber();
            }
        });
    }
}