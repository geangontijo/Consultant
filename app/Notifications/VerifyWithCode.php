<?php

namespace App\Notifications;

use App\Broadcasting\WhatsappChannel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyWithCode extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', WhatsappChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(User $notifiable)
    {
        return (new MailMessage)
            ->line(trans('Verifique sua autenticação.'))
            ->line(trans('Digite o seguinte código para verificar sua autenticação'))
            ->line(trans('Código: ' . $notifiable->verification->code));
    }

    public function toWhatsapp(User $notifiable)
    {
        return 'Seja bem vindo ao sistema de agendamento de consultas, para verificar sua autenticação digite o seguinte código: ' . $notifiable->verification->code . ' no aplicativo.';
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
