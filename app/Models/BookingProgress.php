<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingProgress extends Model
{
    protected $primaryKey = 'progress_id';

    protected $fillable = [
        'booking_id',
        'status',
        'percentage',
        'note',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
