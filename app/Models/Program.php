<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $primaryKey = 'program_id';
    protected $fillable = ['title', 'duration', 'cost', 'description', 'image', 'program_type_id', 'min_students'];

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class, 'program_id', 'program_id');
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class, 'program_id', 'program_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'program_id', 'program_id');
    }

    public function programType()
    {
        return $this->belongsTo(ProgramType::class, 'program_type_id', 'id');
    }

    // Add many-to-many relationship
    public function trainers(): BelongsToMany
    {
        return $this->belongsToMany(
            Trainer::class,
            'program_trainer',
            'program_id',
            'trainer_id'
        )->withTimestamps();

        // return $this->belongsToMany(
        //     Trainer::class,
        //     'program_trainer',
        //     'program_id',
        //     'trainer_id'
        // )->withPivot('years_experience', 'rating', 'is_primary')
        //   ->withTimestamps();
    }

    public function trainingRequirements()
    {
        return $this->hasMany(TrainingRequirement::class, 'program_id');
    }
}
