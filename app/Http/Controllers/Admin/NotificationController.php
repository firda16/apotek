<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(){
        Auth::user()->unreadNotifications->markAsRead();
        $notification = notify('Notifications marked as read');
        return back()->with($notification);
    }

    public function read(){
        Auth::user()->unreadNotifications->markAsRead();
        $notification = notify('Notification marked as read');
        return back()->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     Auth::user()->notify()->delete();
    //     $notification = notify('Notification has been deleted');
    //     return back()->with($notification);
    // }
    // public function destroy($id)
    // {
    //     Auth::user()->notifications()->delete();
    //     $notification = notify('Notification has been deleted');
    //     return back()->with($notification);
    // }
}
