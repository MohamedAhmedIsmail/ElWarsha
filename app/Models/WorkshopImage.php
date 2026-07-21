<?php

namespace App\Models;

use App\Enums\WorkshopImageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopImage extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'workshop_id',
        'image_path',
        'type',
        'sort_order',
    ];

    protected $casts = [
        'type' => WorkshopImageType::class,
        'sort_order' => 'integer',
        'created_at' => 'datetime',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }
}
