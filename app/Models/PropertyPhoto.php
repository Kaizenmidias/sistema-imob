<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PropertyPhoto extends Model
{
    protected $fillable = [
        'property_id',
        'arquivo',
        'url',
        'width',
        'height',
        'size',
        'mime_type',
        'thumb_small_path',
        'thumb_medium_path',
        'optimized',
        'processed_at',
        'processing_status',
        'processing_error',
        'principal',
        'ordem',
    ];

    protected $appends = [
        'thumb_small_url',
        'thumb_medium_url',
    ];

    protected function casts(): array
    {
        return [
            'principal' => 'boolean',
            'optimized' => 'boolean',
            'processed_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getThumbSmallUrlAttribute(): ?string
    {
        if (empty($this->thumb_small_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->thumb_small_path);
    }

    public function getThumbMediumUrlAttribute(): ?string
    {
        if (empty($this->thumb_medium_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->thumb_medium_path);
    }
}
