<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroSection extends Model
{
    use HasFactory;

    protected $table = 'hero_sections';

    protected $fillable = [
        'headline',
        'tagline',
        'image',
        'cta_text',
        'cta_link',
    ];

    protected $casts = [
        'cta_link' => 'string',
    ];
}
