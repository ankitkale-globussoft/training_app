<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';
    protected $fillable = [
        'org_id', 'trainer_id', 'trainer_status', 'training_status', 'org_review',
        'org_rating', 'payment_status', 'transaction_id'
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id', 'trainer_id');
    }
}
