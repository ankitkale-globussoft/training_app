<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';
    protected $fillable = [
        'requirement_id',
        'org_id',
        'trainer_id',
        'booking_status',
        'amount',
        'payment_status',
        'transaction_id',
        'org_review',
        'org_rating',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id', 'trainer_id');
    }

    public function requirement()
    {
        return $this->belongsTo(TrainingRequirement::class, 'requirement_id', 'requirement_id');
    }

    public function progress()
    {
        return $this->hasMany(BookingProgress::class, 'booking_id');
    }
}
