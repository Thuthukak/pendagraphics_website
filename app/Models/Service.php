<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Service extends Model
{
    use HasFactory;

    protected $fillable = [
     'name',
     'description', 
     'price', 
     'duration'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
