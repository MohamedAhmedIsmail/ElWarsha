<?php

namespace App\Enums;

enum OtpPurpose: string
{
    case Register = 'register';
    case Login = 'login';
    case ResetPassword = 'reset_password';
}
