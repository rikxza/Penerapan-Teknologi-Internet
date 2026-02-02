<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetAlertNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $message;
    public $type;

    public function __construct($message, $type = 'warning')
    {
        $this->message = $message;
        $this->type = $type;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $emoji = $this->type == 'danger' ? '🚨' : '⚠️';

        return (new MailMessage)
            ->subject($emoji . ' Peringatan Budget - MoneyGement')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line($this->message)
            ->action('Cek Budget Sekarang', route('budgeting.index'))
            ->line('Kelola keuanganmu dengan bijak!')
            ->salutation('Salam, Tim MoneyGement');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Peringatan Budget!',
            'message' => $this->message,
            'url' => route('budgeting.index'),
            'icon' => $this->type == 'danger' ? '🚨' : '⚠️',
            'type' => $this->type
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

