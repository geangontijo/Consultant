<?php

use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class LangAttributeTest extends TestCase
{
    public function testShouldReturnAttributeName()
    {
        $attributeName = Lang::attribute('password');

        self::assertEquals('senha', $attributeName);
    }

    public function testShouldReturnAttributeKey()
    {
        $attributeName = Lang::attribute('non-existent-attribute');

        self::assertEquals('non-existent-attribute', $attributeName);
    }
}
