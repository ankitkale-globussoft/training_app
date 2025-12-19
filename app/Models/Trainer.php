<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Trainer extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $primaryKey = 'trainer_id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'addr_line1',
        'addr_line2',
        'city',
        'state',
        'district',
        'pincode',
        'resume_link',
        'profile_pic',
        'biodata',
        'achievements',
        'for_org_type',
        'availability',
        'training_mode',
        'signed_form_pdf',
        'verified'
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'trainer_id', 'trainer_id');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(
            Program::class,
            'program_trainer',
            'trainer_id',
            'program_id'
        )->withPivot('years_experience', 'rating', 'is_primary')
            ->withTimestamps();
    }

    public function contents()
    {
        return $this->hasMany(TrainingContent::class, 'trainer_id', 'trainer_id');
    }
}
