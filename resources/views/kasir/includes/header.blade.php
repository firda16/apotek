<!-- Header -->
<div class="header">

    <!-- Logo -->
    <div class="header-left" style="padding-left: 15px;">
        <a href="{{ route('kasir.dashboard') }}" class="logo">
            <img src="@if (!empty(AppSettings::get('logo'))) {{ asset('storage/' . AppSettings::get('logo')) }}
			@else
				{{ asset('assets/img/logo.png') }} @endif"
                alt="Logo">
        </a>
        <a href="{{ route('kasir.dashboard') }}" class="logo logo-small">
            <img src="{{ asset('assets/img/logo-small.png') }}" alt="Logo" width="30" height="30">
        </a>
    </div>
    <!-- /Logo -->

    <a href="javascript:void(0);" id="toggle_btn">
        <i class="fe fe-text-align-left"></i>
    </a>

    <!-- Tombol Menu Mobile -->
    <a class="mobile_btn" id="mobile_btn">
        <i class="fa fa-bars"></i>
    </a>
    <!-- /Tombol Menu Mobile -->

    <!-- Menu Header Kanan -->
    <ul class="nav user-menu">

        <!-- Notifikasi -->
        <li class="nav-item dropdown noti-dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                <i class="fe fe-bell"></i>
                <span class="badge badge-pill">{{ auth()->user()->unReadNotifications->count() }}</span>
            </a>
            <div class="dropdown-menu notifications">
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Notifikasi Kasir</span>
                    <a href="{{ route('mark-as-read') }}" class="clear-noti">Tandai Semua Sudah Dibaca</a>
                </div>
                <div class="noti-content">
                    <ul class="notification-list">
                        @forelse (auth()->user()->unReadNotifications as $notification)
                            <li class="notification-message">
                                <a href="{{ route('read') }}">
                                    <div class="media">
                                        <div class="media-body">
                                            @switch($notification->data['type'] ?? 'default')
                                                @case('expired_product')
                                                    <h6 class="text-danger">
                                                        <i class="fe fe-alert-triangle"></i> Produk Kedaluwarsa
                                                    </h6>
                                                    <p class="noti-details">
                                                        <span class="noti-title">
                                                            {{ $notification->data['product_name'] ?? 'Produk tidak diketahui' }}
                                                        </span>
                                                        <br>
                                                        <span class="text-muted">
                                                            Kedaluwarsa: {{ \Carbon\Carbon::parse($notification->data['expiry_date'])->format('d/m/Y') }}
                                                        </span>
                                                    </p>
                                                    @break
                                                @case('low_stock')
                                                    <h6 class="text-warning">
                                                        <i class="fe fe-package"></i> Stok Rendah
                                                    </h6>
                                                    <p class="noti-details">
                                                        <span class="noti-title">
                                                            {{ $notification->data['product_name'] ?? 'Produk tidak diketahui' }}
                                                        </span>
                                                        <br>
                                                        <span class="badge badge-warning">
                                                            Stok: {{ $notification->data['current_stock'] ?? '0' }}
                                                        </span>
                                                    </p>
                                                    @break
                                                @case('stock_out')
                                                    <h6 class="text-danger">
                                                        <i class="fe fe-x-circle"></i> Stok Habis
                                                    </h6>
                                                    <p class="noti-details">
                                                        <span class="noti-title">
                                                            {{ $notification->data['product_name'] ?? 'Produk tidak diketahui' }}
                                                        </span>
                                                        <br>
                                                        <span class="text-danger">Stok habis</span>
                                                    </p>
                                                    @break
                                                @default
                                                    <h6 class="text-info">
                                                        <i class="fe fe-info"></i> Notifikasi
                                                    </h6>
                                                    <p class="noti-details">
                                                        {{ $notification->data['message'] ?? 'Pesan tidak tersedia' }}
                                                    </p>
                                            @endswitch
                                            <p class="noti-time">
                                                <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="notification-message">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="text-center text-muted">Tidak ada notifikasi baru</p>
                                    </div>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="topnav-dropdown-footer">
                    <a href="{{ route('show-all') }}">Lihat Semua Notifikasi</a>
                </div>
            </div>
        </li>
        <!-- /Notifikasi -->

        <!-- Menu Pengguna -->
        <li class="nav-item dropdown has-arrow">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                <span class="user-img">
                    <img class="rounded-circle"
                        src="{{ !empty(auth()->user()->avatar) ? asset('storage/users/' . auth()->user()->avatar) : asset('assets/img/avatar.png') }}"
                        width="31" alt="Avatar">
                </span>
            </a>
            <div class="dropdown-menu">
                <div class="user-header">
                    <div class="avatar avatar-sm">
                        <img src="{{ !empty(auth()->user()->avatar) ? asset('storage/users/' . auth()->user()->avatar) : asset('assets/img/avatar.png') }}"
                            alt="Foto Pengguna" class="avatar-img rounded-circle">
                    </div>
                    <div class="user-text">
                        <h6>{{ auth()->user()->name }}</h6>
                        <p class="text-muted mb-0">Kasir</p>
                    </div>
                </div>

                <a class="dropdown-item" href="{{ route('profile') }}">Profil Saya</a>
                
                <a href="javascript:void(0)" class="dropdown-item">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="btn">Keluar</button>
                    </form>
                </a>
            </div>
        </li>
        <!-- /Menu Pengguna -->

    </ul>
    <!-- /Menu Header Kanan -->

</div>
<!-- /Header -->
