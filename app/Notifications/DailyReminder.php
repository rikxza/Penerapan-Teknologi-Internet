<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyReminder extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Jangan Lupa Catat Transaksi!',
            'message' => 'Halo ' . $notifiable->name . ', sudahkah kamu mencatat pengeluaran hari ini?',
            'url' => route('transactions.create'),
            'icon' => '📝',
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
