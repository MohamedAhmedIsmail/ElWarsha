<?php

namespace App\Enums;

enum ServiceLedgerMediaType: string
{
    case Image = 'image';
    case Invoice = 'invoice';
    case Document = 'document';
}
