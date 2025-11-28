<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockNotification extends Notification
{
    use Queueable;

    public $product;
    public $currentStock;
    public $minStock;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($product, $currentStock, $minStock)
    {
        $this->product = $product;
        $this->currentStock = $currentStock;
        $this->minStock = $minStock;
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
            'type' => 'low_stock',
            'title' => 'Peringatan Stok Rendah',
            'message' => 'Stok produk ' . $this->product->name . ' tinggal ' . $this->currentStock . ' (min: ' . $this->minStock . ')',
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->currentStock,
            'min_stock' => $this->minStock,
            'image' => $this->product->image ?? '',
        ];
    }
}
