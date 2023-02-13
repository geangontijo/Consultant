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
            if (is_array($case->value)) {
                return $case->value[0];
            }

            return $case->value;
        }, self::cases());
    }
}
