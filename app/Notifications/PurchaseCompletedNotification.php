<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PurchaseCompletedNotification extends Notification
{
    use Queueable;

    public $purchase;
    public $totalAmount;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($purchase, $totalAmount)
    {
        $this->purchase = $purchase;
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
            'type' => 'purchase_completed',
            'title' => 'Pembelian Baru',
            'message' => 'Pembelian baru dengan invoice #' . $this->purchase->invoice_number . ' sebesar Rp ' . number_format($this->totalAmount, 0, ',', '.'),
            'purchase_id' => $this->purchase->id,
            'invoice_number' => $this->purchase->invoice_number,
            'product_names' => $this->purchase->purchaseItems
                ->map(function ($item) {
                    return $item->product->name ?? 'Produk tidak diketahui';
                })->toArray(),
            'total_amount' => $this->totalAmount,
            'supplier_name' => $this->purchase->supplier->name ?? 'Tidak diketahui',
            'created_at' => $this->purchase->created_at,
        ];
    }
}
