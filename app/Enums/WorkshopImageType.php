<?php

namespace App\Enums;

enum WorkshopImageType: string
{
    case Workshop = 'workshop';
    case BeforeAfter = 'before_after';
    case Logo = 'logo';
    case Cover = 'cover';
}
