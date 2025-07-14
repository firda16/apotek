<!-- Header -->
<div class="header">

	<!-- Logo -->
	<div class="header-left" style="padding-left: 15px;">
		<a href="{{ route('dashboard') }}" class="logo">
			<img src="@if(!empty(AppSettings::get('logo')))
				{{ asset('storage/' . AppSettings::get('logo')) }}
			@else
				{{ asset('assets/img/logo.png') }}
			@endif" alt="Logo">
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
		<li class="nav-item dropdown">
			<a href="#" data-target="#add_sales" title="Tambah Penjualan" data-toggle="modal" class="dropdown-toggle nav-link">
				<i class="fas fa-clipboard"></i>
			</a>
		</li>

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
						@foreach (auth()->user()->unReadNotifications as $notification)
							<li class="notification-message">
								<a href="{{ route('read') }}">
									<div class="media">
										<span class="avatar avatar-sm">
											<img class="avatar-img rounded-circle" alt="Gambar Produk" src="{{ asset('storage/purchases/' . $notification['image']) }}">
										</span>
										<div class="media-body">
											<h6 class="text-danger">Peringatan Stok</h6>
											<p class="noti-details">
												<span class="noti-title">{{ $notification->data['product_name'] }} sisa {{ $notification->data['quantity'] }}.</span>
												<span>Segera lakukan pembelian ulang.</span>
											</p>
											<p class="noti-time">
												<span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
											</p>
										</div>
									</div>
								</a>
							</li>
						@endforeach
					</ul>
				</div>
				<div class="topnav-dropdown-footer">
					<a href="#">Lihat Semua Notifikasi</a>
				</div>
			</div>
		</li>
		<!-- /Notifikasi -->

		<!-- Menu Pengguna -->
		<li class="nav-item dropdown has-arrow">
			<a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
				<span class="user-img">
					<img class="rounded-circle" src="{{ !empty(auth()->user()->avatar) ? asset('storage/users/' . auth()->user()->avatar) : asset('assets/img/avatar.png') }}" width="31" alt="Avatar">
				</span>
			</a>
			<div class="dropdown-menu">
				<div class="user-header">
					<div class="avatar avatar-sm">
						<img src="{{ !empty(auth()->user()->avatar) ? asset('storage/users/' . auth()->user()->avatar) : asset('assets/img/avatar.png') }}" alt="Foto Pengguna" class="avatar-img rounded-circle">
					</div>
					<div class="user-text">
						<h6>{{ auth()->user()->name }}</h6>
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
