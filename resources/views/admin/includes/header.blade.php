<!-- Header -->
<div class="header">

    <!-- Logo -->
    <div class="header-left" style="padding-left: 15px;">
        <a href="{{ route('dashboard') }}" class="logo">
            <img src="@if (!empty(AppSettings::get('logo'))) {{ asset('storage/' . AppSettings::get('logo')) }}
			@else
				{{ asset('assets/img/logo.png') }} @endif"
                alt="Logo">
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

            <!-- Menu Pengguna -->
            <li class="nav-item dropdown has-arrow">
                <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                    {{-- <span class="user-img">
                        <img class="rounded-circle"
                            src="{{ !empty(auth()->user()->avatar) ? asset('storage/users/' . auth()->user()->avatar) : asset('assets/img/avatar.png') }}"
                            width="31" alt="Avatar">                   
                    </span> --}}
                    <span class="user-img">                        
                        <i class="fe fe-user" style="font-size: 25px;"></i>
                    </span>
                </a>
                <div class="dropdown-menu">
                    <div class="user-header">
                        {{-- <div class="avatar avatar-sm">
                            <img src="{{ !empty(auth()->user()->avatar) ? asset('storage/users/' . auth()->user()->avatar) : asset('assets/img/avatar.png') }}"
                                alt="Foto Pengguna" class="avatar-img rounded-circle">
                        </div> --}}
                        <div class="col">
                            <div class="user-text">
                                <h5>{{ auth()->user()->name }}</h5>
                            </div>
                            <div class="user-text">
                                <small>Role: {{ auth()->user()->role }}</small>
                            </div>
                        </div>
                    </div>

                    <a class="dropdown-item" href="{{ route('profile') }}">Profil Saya</a>
                    <a class="dropdown-item" href="{{ route('settings') }}">Pengaturan</a>


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
