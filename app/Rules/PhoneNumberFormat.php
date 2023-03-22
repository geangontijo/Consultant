<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumberFormat implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $numbers = preg_replace('/[^0-9]/', '', $value);
        return mb_strlen($numbers) === 11;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'O :attribute deve ser um telefone válido.';
    }
}
