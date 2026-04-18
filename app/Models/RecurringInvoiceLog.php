<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringInvoiceLog extends Model
{
    protected $fillable = [
        'recurring_invoice_id',
        'invoice_id',
        'status',
        'message',
        'ran_at'
        ];
        
    protected $casts = [
        'ran_at' => 'datetime'
        ];

    public function recurringInvoice(): BelongsTo { 
        return $this->belongsTo(RecurringInvoice::class); 
    }

    public function invoice(): BelongsTo { 
        return $this->belongsTo(Invoice::class); 
    }
}