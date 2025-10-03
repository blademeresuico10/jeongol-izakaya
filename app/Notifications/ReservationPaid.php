<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\reservation;

class ReservationPaid extends Notification
{
    use Queueable;

    /**
     * The reservation instance.
     *
     * @var \App\Models\reservation
     */
    protected $reservation;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\reservation $reservation
     */
    public function __construct(reservation $reservation)
    {
        $this->reservation = $reservation;
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


    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'customer'       => $this->reservation->customer->name ?? 'Unknown',
            'table_number'   => $this->reservation->table_number,
            'time'           => $this->reservation->reservation_time->format('Y-m-d H:i'),
            'status'         => 'Paid',
        ];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'customer'       => $this->reservation->customer->name ?? 'Unknown',
            'table'          => optional($this->reservation->table)->number ?? 'N/A',
            'time'           => $this->reservation->started_at->format('Y-m-d H:i'),
            'status'         => 'Paid',
        ];
    }
}
