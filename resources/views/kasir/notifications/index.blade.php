@extends('kasir.layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Semua Notifikasi</h4>
            <form action="{{ route('kasir.notifications.destroyAll') }}" method="POST"
                onsubmit="return confirm('Yakin hapus semua notifikasi?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fe fe-trash"></i> Hapus Semua
                </button>
            </form>
        </div>



        <div class="list-group mt-3">
            @forelse ($notifications as $notif)
                <div class="list-group-item d-flex align-items-start">
                    {{-- Icon di kiri --}}

                    <span class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 10px; height: 10px; margin-right: 10px;">
                    </span>


                    {{-- Konten notifikasi --}}
                    <div class="flex-grow-1">
                        {{-- Nama produk --}}
                        <strong class="d-block">
                            {{ $notif->data['title'] ?? 'Tidak ada nama' }}
                        </strong>

                        <p class="d-block">
                            {{ isset($notif->data['product_names']) ? implode(', ', $notif->data['product_names']) : 'Tidak ada nama' }}
                        </p>

                        {{-- Pesan pembelian --}}
                        <div class="text-muted small mb-1">
                            {{ $notif->data['message'] ?? 'Notifikasi tanpa pesan' }}
                        </div>

                        {{-- Waktu --}}
                        <small class="text-muted">
                            {{ $notif->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            @empty
                <div class="list-group-item">Tidak ada notifikasi.</div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $notifications->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
