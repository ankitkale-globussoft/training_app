<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'name', 'designation', 'about', 'image', 'linkedin_url',
         'is_executive', 'block1_value', 'block1_desc', 'block2_value', 'block2_desc', 'block3_value', 'block3_desc'
    ];

    protected $casts = [
        'is_executive' => 'boolean',
    ];
}
