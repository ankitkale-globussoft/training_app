<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Organization extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $primaryKey = 'org_id';
    protected $fillable = [
        'name',
        'rep_designation',
        'addr_line1',
        'addr_line2',
        'city',
        'state',
        'district',
        'pincode',
        'email',
        'mobile',
        'alt_mobile',
        'org_image',
        'password'
    ];

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'org_id', 'org_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'org_id', 'org_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $link = route('org.password.reset', ['token' => $token, 'email' => $this->email]);
        \Illuminate\Support\Facades\Mail::to($this->email)->send(new \App\Mail\ResetPasswordMail($link));
    }
}