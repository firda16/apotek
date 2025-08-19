@extends('admin.layouts.app')

@push('styles')
{{-- Tambahkan CSS ini untuk warna latar belakang ikon yang lebih lembut --}}
<style>
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .bg-soft-success { background-color: rgba(40, 199, 111, 0.1); }
    .text-success { color: #28c76f !important; }
    .bg-soft-primary { background-color: rgba(0, 123, 255, 0.1); }
    .text-primary { color: #007bff !important; }
    .bg-soft-info   { background-color: rgba(23, 162, 184, 0.1); }
    .text-info    { color: #17a2b8 !important; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Semua Notifikasi</h4>

                @if($notifications->count() > 0)
                    <form action="{{ route('notifications.destroyAll') }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus semua notifikasi?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fe fe-trash-2 me-1"></i> Hapus Semua
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="list-group list-group-flush">
            @forelse ($notifications as $notif)
                <a href="{{ $notif->data['url'] ?? '#' }}" class="list-group-item list-group-item-action py-3 {{ $notif->read_at == null ? 'bg-light' : '' }}">
                    <div class="d-flex align-items-center">

                        {{-- Ikon Notifikasi Dinamis --}}
                        <div class="me-3">
                            @php
                                $title = $notif->data['title'] ?? '';
                                $iconClass = 'fe-bell'; // Default icon
                                $iconBgClass = 'bg-soft-info';
                                $iconColorClass = 'text-info';

                                if (str_contains($title, 'Penjualan')) {
                                    $iconClass = 'fe-check-circle';
                                    $iconBgClass = 'bg-soft-success';
                                    $iconColorClass = 'text-success';
                                } elseif (str_contains($title, 'Notifikasi Baru')) {
                                    $iconClass = 'fe-bell';
                                    $iconBgClass = 'bg-soft-primary';
                                    $iconColorClass = 'text-primary';
                                }
                            @endphp
                            <div class="icon-shape {{ $iconBgClass }}">
                                <i class="fe {{ $iconClass }} fs-4 {{ $iconColorClass }}"></i>
                            </div>
                        </div>

                        {{-- Konten Notifikasi --}}
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <strong class="mb-1 {{ $notif->read_at == null ? 'fw-bold' : '' }}">
                                    {{ $title }}
                                </strong>
                                <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                            </div>
                            <small class="text-muted">
                                {{ $notif->data['message'] ?? '' }}
                            </small>
                        </div>
                    </div>
                </a>
            @empty
                <div class="card-body text-center py-5">
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
