<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramType extends Model
{
    protected $fillable = ['name', 'description', 'image'];

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'program_type_id', 'id');
    }
}
