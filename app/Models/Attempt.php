<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attempt extends Model
{
    protected $primaryKey = 'attempt_id';
    protected $casts = [
        'answers' => 'array',
    ];
    protected $fillable = ['candidate_id', 'test_id', 'answers', 'score', 'certificate_id'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'candidate_id');
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id', 'certificate_id');
    }
}
