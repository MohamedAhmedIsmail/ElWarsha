<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SosRequestLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['sos_request_id', 'old_status', 'new_status', 'changed_by', 'notes'];

    protected $casts = ['created_at' => 'datetime'];

    public function sosRequest(): BelongsTo
    {
        return $this->belongsTo(SosRequest::class);
    }
}
