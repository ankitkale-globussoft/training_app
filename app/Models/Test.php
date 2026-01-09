<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    protected $primaryKey = 'test_id';
    protected $fillable = ['program_id', 'duration', 'title', 'total_marks'];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class, 'test_id', 'test_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class, 'test_id', 'test_id');
    }
}
