<!-- Sidebar -->
<div class="sidebar" id="sidebar">
	<div class="sidebar-inner slimscroll">
		<div id="sidebar-menu" class="sidebar-menu">
			<ul>
				<li class="menu-title">
					<span>Menu Utama</span>
				</li>

				<li class="{{ route_is('dashboard') ? 'active' : '' }}">
					<a href="{{ route('dashboard') }}"><i class="fe fe-home"></i> <span>Beranda</span></a>
				</li>												

				<li class="submenu">
					<a href="#"><i class="fe fe-activity"></i> <span>Penjualan</span> </a>
					<ul style="display: none;">
						<li><a class="{{ route_is('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">Data Penjualan</a></li>
						<li><a class="{{ route_is('sales.create') ? 'active' : '' }}" href="{{ route('sales.create') }}">Tambah Penjualan</a></li>
					</ul>
				</li>				

				<li class="submenu">
					<a href="#"><i class="fe fe-document"></i> <span>Laporan</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li><a class="{{ route_is('sales.report') ? 'active' : '' }}" href="{{ route('sales.report') }}">Laporan Penjualan</a></li>
						<li><a class="{{ route_is('purchases.report') ? 'active' : '' }}" href="{{ route('purchases.report') }}">Laporan Pembelian</a></li>
					</ul>
				</li>								

				<li class="{{ route_is('profile') ? 'active' : '' }}">
					<a href="{{ route('profile') }}"><i class="fe fe-user-plus"></i> <span>Profil</span></a>
				</li>				

				<li class="{{ route_is('settings') ? 'active' : '' }}">
					<a href="{{ route('settings') }}">
						<i class="material-icons">settings</i> <span>Pengaturan</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- /Sidebar -->