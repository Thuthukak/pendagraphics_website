<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Client;
use App\Models\Booking;
use Illuminate\Database\Eloquent\SoftDeletes;


class Booking extends Model
{

    
    use SoftDeletes;
    
    protected $fillable = [
        'client_id',
        'reference',
        'service_id',
        'barber_id',
        'bookingSlot',
        'status',
        'skipCount',
    ];



    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function numberOfSkips() {
        return $this->skipCount >= 1;
        $this->increment('skipCount');
    }
    //Function to confirm a clients booking
    public function bookingComfirmation() {
        $this->notify(new BookingConfirmation($this));
    }
    //send the client a reminder for thier booking
    public function sendReminder() {
        $this->notify(new Reminder($this));
    }
    //if a client has been skipped more 3  or more times they have the option to rebook for another slot
    public function rebook() {
        $this->update([
            'skipCount' => 3,
            'status' => 'queued',
            'bookingSlot' => now(),
        ]);
    }
}
