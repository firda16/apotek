@extends('kasir.layouts.app')

@push('styles')
<style>
    /* Ikon Notifikasi */
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        transition: transform 0.2s;
    }
    .icon-shape:hover {
        transform: scale(1.1);
    }

    /* Warna Latar Ikon */
    .bg-soft-success { background-color: rgba(40, 199, 111, 0.1); }
    .text-success { color: #28c76f !important; }
    .bg-soft-primary { background-color: rgba(0, 123, 255, 0.1); }
    .text-primary { color: #007bff !important; }
    .bg-soft-info { background-color: rgba(23, 162, 184, 0.1); }
    .text-info { color: #17a2b8 !important; }

    /* List Group Item */
    .list-group-item {
        border: none;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .list-group-item:hover {
        background: #f8f9fa;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        text-decoration: none;
    }
    .list-group-item.unread {
        background-color: #e9f7ef;
    }

    /* Card Header */
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Tombol Hapus */
    .btn-sm {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }

    /* Waktu notifikasi */
    .text-muted {
        font-size: 0.85rem;
    }

    /* Responsif */
    @media (max-width: 576px) {
        .d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            align-items: flex-start;
        }
        .d-flex.justify-content-between.align-items-center form {
            margin-top: 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Semua Notifikasi</h4>

            @if($notifications->count() > 0)
                <form action="{{ route('kasir.notifications.destroyAll') }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus semua notifikasi?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fe fe-trash-2 me-1"></i> Hapus Semua
                    </button>
                </form>
            @endif
        </div>

        <div class="card-body">
            @forelse ($notifications as $notif)
                @php
                    $title = $notif->data['title'] ?? '';
                    $iconClass = 'fe-bell';
                    $iconBgClass = 'bg-soft-info';
                    $iconColorClass = 'text-info';

                    if (str_contains($title, 'Penjualan')) {
                        $iconClass = 'fe-check-circle';
                        $iconBgClass = 'bg-soft-success';
                        $iconColorClass = 'text-success';
                    } elseif (str_contains($title, 'Stok')) {
                        $iconClass = 'fe-box';
                        $iconBgClass = 'bg-soft-primary';
                        $iconColorClass = 'text-primary';
                    }

                    $isUnread = $notif->read_at == null;
                @endphp

                <a href="{{ $notif->data['url'] ?? '#' }}" class="list-group-item list-group-item-action py-3 {{ $isUnread ? 'unread' : '' }}">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="icon-shape {{ $iconBgClass }}">
                                <i class="fe {{ $iconClass }} fs-4 {{ $iconColorClass }}"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong class="{{ $isUnread ? 'fw-bold' : '' }}">{{ $title }}</strong>
                                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                            </div>
                            <small class="text-muted">
                                {{ $notif->data['message'] ?? '' }}
                                @if(isset($notif->data['product_names']))
                                    <em>{{ implode(', ', $notif->data['product_names']) }}</em>
                                @endif
                            </small>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-5">
                    <i class="fe fe-bell-off fs-1 text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada notifikasi untuk ditampilkan.</p>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
