<?php

namespace App\Enums;

enum OrderPaymentMethods: string
{
    use EnumCasesAsStringArray;

    case CreditCard = 'credit_card';
    case BankSlip = 'bank_slip';
}
