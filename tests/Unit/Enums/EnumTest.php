<?php

namespace Enums;

use App\Enums\EnumCasesAsStringArray;

enum EnumTest: string
{

    use EnumCasesAsStringArray;

    case A = 'a';

    case B = 'b';

    case C = 'c';

}
