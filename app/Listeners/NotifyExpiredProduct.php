<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\PurchaseItem;
use App\Events\ProductExpired;
use App\Notifications\ExpiredProductNotification;

class NotifyExpiredProduct
{
    public function __construct()
    {
        //
    }

    public function handle(ProductExpired $event)
    {
        // Pastikan produk expired dan belum pernah dikirim notif
        $product = $event->product;

        if ($product->expiry_date < now() && is_null($product->expired_notified_at)) {
            // Ambil semua admin
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new ExpiredProductNotification($product));
            }

            // Tandai sudah dikirim
            $product->update([
                'expired_notified_at' => now()
            ]);
        }
    }
}
