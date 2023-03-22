<?php

namespace App\Notifications;

use App\Broadcasting\WhatsappChannel;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;

class ResetPassword extends ResetPasswordNotification
{

    public function via($notifiable): array
    {
        return ['mail', WhatsappChannel::class];
    }

    public function toWhatsapp(): string
    {
        return trans('auth.password.forget.notification', [
            'url' => route('password.reset', ['token' => $this->token]),
            'app' => env('APP_NAME'),
            'count' => config('auth.passwords.users.expire'),
        ]);
    }
}
