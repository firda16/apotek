<!-- Header -->
<div class="header">

    <!-- Logo -->
    <div class="header-left" style="padding-left: 15px;">
        <a href="{{ route('dashboard') }}" class="logo">
            @if (!empty($setting?->image))
                <img src="{{ asset($setting->image) }}" alt="Logo" height="50">
            @else
                <span>{{ $setting?->nama ?? 'Website' }}</span>
            @endif
        </a>

        <a href="{{ route('dashboard') }}" class="logo logo-small">
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

        <!-- Tambah Penjualan -->
        {{-- <li class="nav-item dropdown">
			<a href="#" data-target="#add_sales" title="Tambah Penjualan" data-toggle="modal" class="dropdown-toggle nav-link">
				<i class="fas fa-clipboard"></i>
			</a>
		</li> --}}

        <!-- Notifikasi -->
        <li class="nav-item dropdown noti-dropdown">
            <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                <i class="fe fe-bell"></i>
                <span class="badge badge-pill">{{ auth()->user()->unReadNotifications->count() }}</span>
            </a>
            <div class="dropdown-menu notifications">
                <div class="topnav-dropdown-header">
                    <span class="notification-title">Notifikasi</span>
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
                                                            Kedaluwarsa:
                                                            {{ \Carbon\Carbon::parse($notification->data['expiry_date'])->format('d/m/Y') }}
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

                                                @case('sale_completed')
                                                    <h6 class="text-success">
                                                        <i class="fe fe-shopping-cart"></i> Penjualan Baru
                                                    </h6>
                                                    <p class="noti-details">
                                                        <span class="noti-title">
                                                            Invoice:
                                                            {{ $notification->data['invoice_number'] ?? 'Tidak diketahui' }}
                                                        </span>
                                                        <br>
                                                        <span class="badge badge-success">
                                                            Rp
                                                            {{ number_format($notification->data['total_amount'] ?? 0, 0, ',', '.') }}
                                                        </span>
                                                    </p>
                                                @break

                                                @case('purchase_completed')
                                                    <h6 class="text-info">
                                                        <i class="fe fe-shopping-bag"></i> Pembelian Baru
                                                    </h6>
                                                    <p class="noti-details">
                                                        <span class="noti-title">
                                                            Invoice:
                                                            {{ $notification->data['invoice_number'] ?? 'Tidak diketahui' }}
                                                        </span>
                                                        <br>
                                                        <span class="noti-title">Produk:
                                                            {{ implode(', ', $notification->data['product_names'] ?? []) }}</span>
                                                        <br>
                                                        <span class="badge badge-success p-1">
                                                            Rp
                                                            {{ number_format($notification->data['total_amount'] ?? 0, 0, ',', '.') }}
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
                                                <span
                                                    class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
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

            {{-- <li>
                <a href="javascript:void(0)" class="dropdown-item">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="btn logout-btn">
                            <i class="fe fe-logout" style="font-size: 20px;"></i>
                            <p class="logout-text">Keluar</p>
                        </button>
                    </form>
                </a>
            </li> --}}
            <!-- Menu Pengguna -->


            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                    <span class="user-img">
                        @if (!empty(auth()->user()->avatar))
                            <img class="rounded-circle" src="{{ asset('storage/users/' . auth()->user()->avatar) }}"
                                width="31" alt="Avatar">
                        @else
                            <i class="fe fe-user" style="font-size: 24px;"></i>
                        @endif
                    </span>

                </a>
                <div class="dropdown-menu dropdown-menu-right">
    <!-- User Info -->
    <div class="user-header d-flex align-items-center p-3 border-bottom">
        <div class="user-text">
            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
            <small class="text-muted">Role: {{ auth()->user()->role }}</small>
        </div>
    </div>

    <!-- Menu Items -->
    <a class="dropdown-item" href="{{ route('profile') }}">
        <i class="fe fe-user me-2 mr-2" style="font-size: 18px"></i> Profil Saya
    </a>
    <a class="dropdown-item" href="{{ route('settings.edit') }}">
        <i class="fa fa-cog me-2 mr-2" style="font-size: 15px"></i> Pengaturan
    </a>

    <div class="dropdown-divider"></div>

    <!-- Logout -->
    <form action="{{ route('logout') }}" method="POST" class="m-0">
        @csrf
        <button type="submit" class="dropdown-item text-danger">
            <i class="fe fe-log-out me-2"></i> Keluar
        </button>
    </form>
</div>

            </li>

            <!-- /Menu Pengguna -->

        </ul>
        <!-- /Menu Header Kanan -->

    </div>
    <!-- /Header -->
