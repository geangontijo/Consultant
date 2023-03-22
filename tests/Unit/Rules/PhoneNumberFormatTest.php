<?php

namespace Rules;

use App\Rules\PhoneNumberFormat;
use Tests\TestCase;

class PhoneNumberFormatTest extends TestCase
{

    public function test_should_error_invalid_format()
    {
        $phoneNumberFormat = new PhoneNumberFormat();
        self::assertFalse($phoneNumberFormat->passes('phone_number', '123456789'));
        self::assertFalse($phoneNumberFormat->passes('phone_number', 'empty'));
        self::assertFalse($phoneNumberFormat->passes('phone_number', '8KJEYFnB6d@jEYaO5ThCY!wJV0J!5t@mfkZmB0hA%#6'));
        self::assertFalse($phoneNumberFormat->passes('phone_number', '3791524432'));
    }

    public function test_should_passes_valid_format()
    {
        $phoneNumberFormat = new PhoneNumberFormat();
        self::assertTrue($phoneNumberFormat->passes('phone_number', '37991524432'));
        self::assertTrue($phoneNumberFormat->passes('phone_number', '(37) 9 9152-4432'));
        self::assertTrue($phoneNumberFormat->passes('phone_number', '(37) 99152-4432'));
        self::assertTrue($phoneNumberFormat->passes('phone_number', '(37) 991524432'));
    }
}
