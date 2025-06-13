<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationship with EstimateService
    public function estimateServices()
    {
        return $this->hasMany(EstimateService::class);
    }

    // Scope for active services only
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
