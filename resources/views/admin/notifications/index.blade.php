@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h4>Semua Notifikasi</h4>
    <ul class="list-group mt-3">
        @forelse ($notifications as $notif)
            <li class="list-group-item">
                {{ $notif->data['message'] ?? 'Notifikasi tanpa pesan' }}
                <small class="text-muted d-block">{{ $notif->created_at->diffForHumans() }}</small>
            </li>
        @empty
            <li class="list-group-item">Tidak ada notifikasi.</li>
        @endforelse
    </ul>

    {{ $notifications->links() }}
</div>
@endsection
