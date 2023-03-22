<?php

namespace Broadcasting;

use App\Broadcasting\WhatsappChannel;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class WhatsappChannelTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testSend()
    {
        $this->createApplication();
        $user = new User([
            'phone_number' => '37991524432',
        ]);

        App::bind('whatsapp.send.message', function () {
            return new class() {
                public function fire(): bool
                {
                    Assert::assertTrue(true);

                    return true;
                }
            };
        });

        $whatsappChannel = new WhatsappChannel();
        $notification = new class() extends Notification {
            public function toWhatsapp(): string
            {
                return 'Test message.';
            }
        };
        $whatsappChannel->send($user, $notification);
    }
}
