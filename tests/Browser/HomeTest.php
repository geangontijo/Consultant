<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class HomeTest extends DuskTestCase
{
    /**
     * @throws Throwable
     */
    public function test_should_open_home_click_navbar_link(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click("a")
                ->assertSee('Consultant')
                ->assertPathIs('/');
        });
    }
}
