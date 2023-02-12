<?php

namespace App\Enums;

trait EnumCasesAsStringArray
{

    /**
     * @return array<string>
     */
    public static function casesAsArrayString(): array
    {
        return array_map(function (\UnitEnum $case) {
            return $case->value;
        }, self::cases());
    }
}
