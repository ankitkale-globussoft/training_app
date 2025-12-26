<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerBankDetail extends Model
{
    protected $fillable = [
        'trainer_id',
        'account_holder_name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'upi_id'
    ];

    public function trainer()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id', 'trainer_id');
    }
}
