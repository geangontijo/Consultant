<?php

namespace App\Broadcasting;

use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Queue;
use Laravel\Telescope\Telescope;

class WhatsappChannel
{
    /**
     * @throws Exception
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $phoneNumber = $notifiable->routeNotificationFor('whatsapp');

        if (!$phoneNumber) {
            return;
        }

        $original = Telescope::$shouldRecord;
        Telescope::$shouldRecord = false;
        Queue::push('whatsapp.send.message', [
            'to' => $phoneNumber,
            'message' => $notification->toWhatsapp(),
        ], 'whatsapp');

        Telescope::$shouldRecord = $original;
    }
}
