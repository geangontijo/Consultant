<?php

namespace Casts;

use App\Casts\OnlyNumbers;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class OnlyNumbersTest extends TestCase
{

    public function test_convert_only_numbers()
    {
        $onlyNumbersCast = new OnlyNumbers();
        $set             = $onlyNumbersCast->set(
            $this->createMock(Model::class),
            'any',
            '105-0',
            []
        );
        $this->assertEquals('1050', $set);
    }
}
