<?php

namespace App\Broadcasting;

use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Queue;

class WhatsappChannel
{
    /**
     * @throws Exception
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $phoneNumber = $notifiable->routeNotificationFor('whatsapp');

        if (!$phoneNumber) {
            throw new Exception('No phone number found for the given user.');
        }

        Queue::push('whatsapp.send.message', [
            'to' => $phoneNumber,
            'message' => $notification->toWhatsapp($notifiable),
        ], 'whatsapp');
    }
}
