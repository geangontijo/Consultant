<?php

namespace Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Password;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class PasswordResetLinkControllerTest extends TestCase
{
    public function test_should_create_link()
    {
        $this->partialMock(User::class, function (MockInterface $mock) {
            $mock->shouldReceive('where')->once()->andReturn(Mockery::mock(Builder::class,
                function (MockInterface $mock) {
                    $mock->shouldReceive('exists')->once()->andReturnTrue();
                })->makePartial()
            );
            $mock->shouldReceive('sendPasswordResetNotification')->once()->andReturnUndefined();
        });

        $this->partialMock(ConnectionInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('table')->times(2)->andReturn(Mockery::mock(
                Builder::class,
                function (MockInterface $mock) {
                    $mock->shouldReceive('exists')->once()->andReturnTrue();
                    $mock->shouldReceive('value')->once()->andReturn(null);
                    $mock->shouldReceive('insert')->once()->andReturnTrue();
                    $mock->shouldReceive('where')->times(2)->andReturn($mock->makePartial());
                })->makePartial()
            );
            $mock->shouldReceive('transaction')->once()->andReturnUsing(function ($callback) {
                return $callback();
            });
        });

        $testResponse = $this->post(route('password.forgot'), [
            'email' => fake()->email(),
            'phone_number' => '(31) 63830-5936'
        ]);

        $testResponse->assertSessionHas('status', trans(Password::RESET_LINK_SENT));

        self::assertEquals(302, $testResponse->getStatusCode());
//        self::assertEquals(route('password.forgot'), $testResponse->headers->get('location'));
    }
}
