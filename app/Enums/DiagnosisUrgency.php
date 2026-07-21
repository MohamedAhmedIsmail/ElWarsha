<?php

namespace App\Enums;

enum DiagnosisUrgency: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
