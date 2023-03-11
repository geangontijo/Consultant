<?php

namespace Tests\Browser;

use Illuminate\Support\Facades\Lang;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

/**
 * @internal
 *
 * @coversNothing
 */
class RegisterTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function test_should_register(): void
    {
        $this->browse(function (Browser $browser): void {
            $fakeName = fake()->unique()->name();
            $fakeEmail = fake()->unique()->email();
            $browser->visit('register')
                ->type('[name="name"]', $fakeName)
                ->type('[name="email"]', $fakeEmail)
                ->type('[name="password"]', 'password')
                ->type('[name="password_confirmation"]', 'password')
                ->press('form button[type="submit"]')
                ->waitForLocation('/')
                ->assertPathIs('/');
        });
    }

    /**
     * @throws Throwable
     */
    public function test_should_throw_error_on_register_with_duplicated_email(): void
    {
        $fakeName = fake()->unique()->name();
        $fakeEmail = fake()->unique()->email();
        $this->browse(function (Browser $browser, Browser $browser2) use ($fakeEmail, $fakeName): void {
            $browser->visit('register')
                ->type('[name="name"]', $fakeName)
                ->type('[name="email"]', $fakeEmail)
                ->type('[name="password"]', 'password')
                ->type('[name="password_confirmation"]', 'password')
                ->press('form button[type="submit"]')
                ->waitForLocation('/');

            $browser2->visit('register')
                ->type('[name="name"]', $fakeName)
                ->type('[name="email"]', $fakeEmail)
                ->type('[name="password"]', 'password')
                ->type('[name="password_confirmation"]', 'password')
                ->press('form button[type="submit"]')
                ->waitForText(trans('validation.unique', ['attribute' => 'email']));

            self::assertTrue(true);
        });
    }

    /**
     * @throws Throwable
     */
    public function test_should_throw_error_on_register_with_password_length_less_than_8(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('register')
                ->type('[name="name"]', fake()->unique()->name())
                ->type('[name="email"]', fake()->unique()->email())
                ->type('[name="password"]', 'pass')
                ->type('[name="password_confirmation"]', 'pass')
                ->press('form button[type="submit"]')
                ->waitForText(trans('validation.min.string', ['attribute' => Lang::attribute('password'), 'min' => 8]));

            self::assertTrue(true);
        });
    }

    /**
     * @throws Throwable
     */
    public function test_should_throw_error_on_register_with_wrong_password_confirmation(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('register')
                ->type('[name="name"]', fake()->unique()->name())
                ->type('[name="email"]', fake()->unique()->email())
                ->type('[name="password"]', 'password')
                ->type('[name="password_confirmation"]', 'password2')
                ->press('form button[type="submit"]')
                ->waitForText(trans('validation.confirmed', ['attribute' => Lang::attribute('password')]));

            self::assertTrue(true);
        });
    }

    protected function setUp(): void
    {
        self::$browsers = [];
        parent::setUp();
    }
}
