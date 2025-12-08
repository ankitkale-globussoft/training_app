<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Candidate extends Model
{
    protected $primaryKey = 'candidate_id';
    protected $fillable = ['name', 'phone', 'email', 'password', 'org_id', 'program_id'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id', 'org_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class, 'candidate_id', 'candidate_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'candidate_id', 'candidate_id');
    }
}
