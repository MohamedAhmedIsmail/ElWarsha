<?php

namespace App\Enums;

enum DiagnosisStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';
    case ManualReview = 'manual_review';
}
