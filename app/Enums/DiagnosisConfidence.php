<?php

namespace App\Enums;

enum DiagnosisConfidence: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
