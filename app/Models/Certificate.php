<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Certificate extends Model
{
    protected $primaryKey = 'certificate_id';
    protected $fillable = ['candidate_id', 'program_id'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'candidate_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function attempt(): HasOne
    {
        return $this->hasOne(Attempt::class, 'certificate_id', 'certificate_id');
    }
}
