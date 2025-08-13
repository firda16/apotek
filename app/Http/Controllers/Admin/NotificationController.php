<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function markAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $notification = notify('Notifikasi telah dibaca');
        return back()->with($notification);
    }

    public function read()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $notification = notify('Notifikasi telah dibaca');
        return back()->with($notification);
    }



    public function show()
    {
        $notifications = Auth::user()->notifications; // ini collection
        $perPage = 10;
        $currentPage = request()->input('page', 1);

        // Potong collection sesuai halaman
        $currentItems = $notifications->slice(($currentPage - 1) * $perPage, $perPage)->values();

        // Buat paginator manual
        $paginated = new LengthAwarePaginator(
            $currentItems,
            $notifications->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.notifications.index', ['notifications' => $paginated]);
    }


    

    public function destroyAll()
    {
        DatabaseNotification::where('notifiable_id', Auth::id())
            ->where('notifiable_type', get_class(Auth::user()))
            ->delete();

        $notification = notify('Semua notifikasi berhasil dihapus');
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
