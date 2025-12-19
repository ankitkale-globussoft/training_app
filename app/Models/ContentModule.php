<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Model;

class ContentModule extends Model
{
    protected $primaryKey = 'module_id';

    protected $fillable = [
        'booking_id',
        'title',
        'order_no',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(TrainingContent::class, 'module_id', 'module_id');
    }

}
