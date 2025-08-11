<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SaleCompleted extends Notification
{
    use Queueable;

    /**
     * The sale ID for the notification.
     *
     * @var int
     */
    protected $sale_id;

    /**
     * The invoice number for the notification.
     *
     * @var string
     */
    protected $invoice_number;

    /**
     * Create a new notification instance.
     *
     * @param int $sale_id
     * @param string $invoice_number
     */
    public function __construct($sale_id, $invoice_number)
    {
        $this->sale_id = $sale_id;
        $this->invoice_number = $invoice_number;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Penjualan Selesai',
            'message' => 'Penjualan dengan invoice #' . $this->invoice_number . ' berhasil disimpan.',
            'url' => route('sales.invoice', $this->sale_id),
        ];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
