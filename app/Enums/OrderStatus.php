<?php

namespace App\Enums;

enum OrderStatus: string
{
    use EnumCasesAsStringArray;

    case Pending = 'pending';
    case Paid = 'paid';
}
