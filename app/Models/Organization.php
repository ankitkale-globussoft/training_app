<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $primaryKey = 'org_id';
    protected $fillable = [
        'name', 'rep_designation', 'addr_line1', 'addr_line2', 'city', 'state', 'district',
        'pincode', 'email', 'mobile', 'alt_mobile', 'password'
    ];

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'org_id', 'org_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'org_id', 'org_id');
    }
}
