<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'requirement_id',
        'booking_id',
        'payer_type',
        'payee_type',
        'amount',
        'transaction_id',
        'transaction_type',
        'payment_status',
    ];

    public function requirement()
    {
        return $this->belongsTo(TrainingRequirement::class, 'requirement_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
