<?php

namespace App\Enums;

enum OrderPaymentMethods: string
{
    use EnumCasesAsStringArray;

    case Card = 'card';
    case Boleto = 'boleto';

}
