<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingRequirement extends Model
{
    protected $primaryKey = 'requirement_id';

    protected $fillable = [
        'org_id',
        'program_id',
        'accepted_trainer_id',
        'mode',
        'location',
        'schedule_start',
        'schedule_end',
        'status',
        'payment'
    ];

    public function organisation()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function acceptedTrainer()
    {
        return $this->belongsTo(Trainer::class, 'accepted_trainer_id');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'requirement_id');
    }
}