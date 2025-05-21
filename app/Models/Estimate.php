<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'total_amount',
        'status',
        'notes'
    ];

    /**
     * Get the services associated with this estimate.
     */
    public function services()
    {
        return $this->hasMany(EstimateService::class);
    }
}
