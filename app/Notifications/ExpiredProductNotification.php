<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ExpiredProductNotification extends Notification
{
    use Queueable;

    public $product;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($product)
    {
        $this->product = $product;
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
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Produk Kedaluwarsa')
            ->line('Produk telah kedaluwarsa: ' . $this->product->name)
            ->action('Lihat Produk', route('products.edit', $this->product->id));
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
            'type' => 'expired_product',
            'title' => 'Produk Kedaluwarsa',
            'message' => 'Produk ' . $this->product->name . ' telah kedaluwarsa pada ' . $this->product->expiry_date,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'expiry_date' => $this->product->expiry_date,
            'image' => $this->product->image ?? '',
        ];
    }
}
