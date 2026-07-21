<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case VodafoneCash = 'vodafone_cash';
    case Instapay = 'instapay';
    case BankTransfer = 'bank_transfer';
    case Cash = 'cash';
    case Paymob = 'paymob';
    case Fawry = 'fawry';
}
