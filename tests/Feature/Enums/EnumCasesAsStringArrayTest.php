<?php

namespace Enums;

use App\Enums\EnumCasesAsStringArray;
use PHPUnit\Framework\TestCase;

class EnumCasesAsStringArrayTest extends TestCase
{

    public function test_enum_cases_as_array_string()
    {
        $this->assertEquals(['a', 'b', 'c'], EnumTest::casesAsArrayString());
    }
}
