<?php

namespace Models;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_should_cast_password_to_hash()
    {
        $user = new User();
        $user->password = 'peteca123';

        $this->assertTrue(Hash::check('peteca123', $user->password));
    }

    public function test_expect_error_password_length_smaller_then_8()
    {
        $this->expectException(ValidationException::class);
        $user = new User();
        $user->password = 'peteca';
    }
}
