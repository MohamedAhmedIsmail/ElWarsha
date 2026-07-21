<?php

namespace App\Enums;

enum DiagnosisMediaType: string
{
    case Image = 'image';
    case Audio = 'audio';
    case Video = 'video';
}
