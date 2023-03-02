<?php

namespace App\Enums;

enum QueueTypes: string
{
    use EnumCasesAsStringArray;

    case Whatsapp = 'whatsapp';
}
