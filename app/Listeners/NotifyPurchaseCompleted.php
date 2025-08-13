<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\PurchaseCompletedNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPurchaseCompleted
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'manager']);
        })->get();

        foreach ($users as $user) {
            $user->notify(new PurchaseCompletedNotification($event->purchase, $event->totalAmount));
        }
    }
}
