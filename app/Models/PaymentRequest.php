<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    protected $primaryKey = 'request_id';

    protected $fillable = [
        'trainer_id',
        'amount',
        'status',
        'admin_note',
        'payment_proof'
    ];

    public function trainer()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id', 'trainer_id');
    }
}
