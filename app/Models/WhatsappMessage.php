<?php

namespace App\Models;

use App\Enums\WhatsAppMessageStatus;
use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $fillable = [
        'user_id',
        'workshop_id',
        'phone',
        'message',
        'template_name',
        'provider_response',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'status' => WhatsAppMessageStatus::class,
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
