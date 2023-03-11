<?php

use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class LangAttributeTest extends TestCase
{
    public function test_should_return_attribute_name()
    {
        $attributeName = Lang::attribute('password');

        self::assertEquals('senha', $attributeName);
    }

    public function test_should_not_return_attribute_name()
    {
        $attributeName = Lang::attribute('non-existent-attribute');

        self::assertEmpty($attributeName);
    }
}
