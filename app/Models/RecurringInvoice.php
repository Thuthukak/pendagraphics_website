<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'client_id', 'frequency', 'interval', 'interval_unit',
        'start_date', 'end_date', 'next_run_date', 'last_run_date',
        'occurrences_limit', 'occurrences_count',
        'days_until_due', 'notes', 'terms', 'tax_rate', 'discount_rate',
        'action_on_create', 'notify_admin', 'is_active',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'next_run_date'  => 'date',
        'last_run_date'  => 'date',
        'tax_rate'       => 'decimal:2',
        'discount_rate'  => 'decimal:2',
        'notify_admin'   => 'boolean',
        'is_active'      => 'boolean',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RecurringInvoiceItem::class)->orderBy('sort_order');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(RecurringInvoiceLog::class)->latest('ran_at');
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueToRun($query, Carbon $asOf = null)
    {
        $date = $asOf ?? now()->toDateString();
        return $query->active()->where('next_run_date', '<=', $date);
    }

    // ─── Business Logic ──────────────────────────────────────────────────────────

    /**
     * Calculate the next run date after the given date based on the schedule.
     */
    public function calculateNextRunDate(Carbon $after = null): Carbon
    {
        $base = $after ?? Carbon::parse($this->next_run_date);

        return match ($this->frequency) {
            'weekly'    => $base->addWeeks($this->interval),
            'monthly'   => $base->addMonths($this->interval),
            'quarterly' => $base->addMonths(3 * $this->interval),
            'annually'  => $base->addYears($this->interval),
            'custom'    => $this->addCustomInterval($base),
            default     => $base->addMonths(1),
        };
    }

    private function addCustomInterval(Carbon $date): Carbon
    {
        return match ($this->interval_unit) {
            'day'   => $date->addDays($this->interval),
            'week'  => $date->addWeeks($this->interval),
            'month' => $date->addMonths($this->interval),
            'year'  => $date->addYears($this->interval),
            default => $date->addMonths($this->interval),
        };
    }

    /**
     * Whether this schedule should still run — checks limits and end dates.
     */
    public function shouldRun(): bool
    {
        if (!$this->is_active) return false;
        if ($this->end_date && $this->next_run_date->gt($this->end_date)) return false;
        if ($this->occurrences_limit && $this->occurrences_count >= $this->occurrences_limit) return false;
        return true;
    }

    /**
     * Build an Invoice (not yet saved) from this recurring template.
     */
    public function buildInvoice(): Invoice
    {
        $invoice = new Invoice([
            'client_id'    => $this->client_id,
            'invoice_date' => $this->next_run_date->toDateString(),
            'due_date'     => $this->next_run_date->addDays($this->days_until_due)->toDateString(),
            'status'       => 'draft',
            'notes'        => $this->notes,
            'terms'        => $this->terms,
            'tax_rate'     => $this->tax_rate,
            'discount_rate'=> $this->discount_rate,
        ]);

        return $invoice;
    }

    /**
     * Human-readable schedule description.
     */
    public function getScheduleDescriptionAttribute(): string
    {
        $freq = match ($this->frequency) {
            'weekly'    => $this->interval > 1 ? "every {$this->interval} weeks" : 'weekly',
            'monthly'   => $this->interval > 1 ? "every {$this->interval} months" : 'monthly',
            'quarterly' => 'quarterly',
            'annually'  => 'annually',
            'custom'    => "every {$this->interval} {$this->interval_unit}(s)",
            default     => $this->frequency,
        };

        $end = $this->end_date ? ' until ' . $this->end_date->format('d M Y') : '';
        $limit = $this->occurrences_limit ? " ({$this->occurrences_count}/{$this->occurrences_limit} runs)" : '';

        return ucfirst($freq) . $end . $limit;
    }

    /**
    * Determine if the schedule should remain active after this run.
    * Call AFTER incrementing occurrences_count.
    */
    public function shouldStillBeActiveAfterRun(): bool
    {
        $newCount = $this->occurrences_count + 1;
        if ($this->occurrences_limit && $newCount >= $this->occurrences_limit) return false;
        $nextDate = $this->calculateNextRunDate();
        if ($this->end_date && $nextDate->gt($this->end_date)) return false;
        return true;
    }

}