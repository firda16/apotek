<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\LowStockAlert;
use App\Notifications\LowStockNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyLowStock
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LowStockAlert  $event
     * @return void
     */
    public function handle(LowStockAlert $event)
    {
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'manager']);
        })->get();

        foreach ($users as $user) {
            $user->notify(new LowStockNotification(
                $event->product,
                $event->currentStock,
                $event->minStock
            ));
        }
    }
}
