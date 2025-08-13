<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SaleCompletedNotification extends Notification
{
    use Queueable;

    public $sale;
    public $totalAmount;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($sale, $totalAmount)
    {
        $this->sale = $sale;
        $this->totalAmount = $totalAmount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'sale_completed',
            'title' => 'Penjualan Baru',
            'message' => 'Penjualan baru dengan invoice #' . $this->sale->invoice_number . ' sebesar Rp ' . number_format($this->totalAmount, 0, ',', '.'),
            'sale_id' => $this->sale->id,
            'invoice_number' => $this->sale->invoice_number,
            'total_amount' => $this->totalAmount,
            'customer_name' => $this->sale->customer->nama ?? 'Umum',
            'created_at' => $this->sale->created_at,
        ];
    }
}
