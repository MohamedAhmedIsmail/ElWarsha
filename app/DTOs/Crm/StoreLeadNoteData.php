<?php

namespace App\DTOs\Crm;

class StoreLeadNoteData
{
    public function __construct(public readonly string $note)
    {
    }
}
