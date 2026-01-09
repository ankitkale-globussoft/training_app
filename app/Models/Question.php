<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $primaryKey = 'ques_id';
    protected $fillable = ['test_id', 'ques_text', 'opt_a', 'opt_b', 'opt_c', 'opt_d', 'ans_opt', 'marks'];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class, 'test_id', 'test_id');
    }
}
