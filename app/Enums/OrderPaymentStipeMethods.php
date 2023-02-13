<?php

namespace App\Enums;

enum OrderPaymentStipeMethods: string
{
    use EnumCasesAsStringArray;

    case CreditCard = 'card';
    case BankSlip = 'boleto';
}
