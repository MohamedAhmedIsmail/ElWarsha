<?php

namespace App\Models;

use App\Enums\ServiceLedgerMediaType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceLedgerMedia extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'service_ledger_media';

    protected $fillable = ['service_ledger_id', 'media_type', 'file_path'];

    protected $casts = [
        'media_type' => ServiceLedgerMediaType::class,
        'created_at' => 'datetime',
    ];

    public function ledger(): BelongsTo
    {
        return $this->belongsTo(ServiceLedger::class, 'service_ledger_id');
    }
}
