<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingContent extends Model
{
    protected $primaryKey = 'content_id';

    protected $fillable = [
        'booking_id',
        'trainer_id',
        'module_id',
        'mode',
        'content_type',
        'title',
        'description',
        'text_content',
        'file_path',
        'external_url',
        'is_visible_to_org',
        'is_visible_to_candidates',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id', 'trainer_id');
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(ContentModule::class, 'module_id', 'module_id');
    }

    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    // Accessor for content type icon
    public function getTypeIconAttribute()
    {
        return match ($this->content_type) {
            'video' => 'bx-video',
            'text' => 'bx-file',
            'pdf' => 'bxs-file-pdf',
            'link' => 'bx-link',
            'meeting' => 'bx-video-recording',
            default => 'bx-file'
        };
    }

    // Accessor for content type badge
    public function getTypeBadgeAttribute()
    {
        return match ($this->content_type) {
            'video' => 'bg-label-primary',
            'text' => 'bg-label-info',
            'pdf' => 'bg-label-danger',
            'link' => 'bg-label-success',
            'meeting' => 'bg-label-warning',
            default => 'bg-label-secondary'
        };
    }
}
