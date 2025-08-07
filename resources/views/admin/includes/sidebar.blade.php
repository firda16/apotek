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

                <li class="{{ route_is('categories.*') ? 'active' : '' }}">
                    <a href="{{ route('categories.index') }}"><i class="fe fe-layout"></i> <span>Kategori</span></a>
                </li>


                <li class="submenu">
                    <a href="#"><i class="fe fe-document"></i> <span>Master Data Obat</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ route_is('products.*') ? 'active' : '' }}"
                                href="{{ route('products.index') }}">Data Produk</a></li>
                        <li><a class="{{ route_is('products.create') ? 'active' : '' }}"
                                href="{{ route('products.create') }}">Tambah Produk</a></li>
                        <li><a class="{{ route_is('products.available') ? 'active' : '' }}" href="{{ route('products.available') }}">Stok
                                Tersedia</a></li>
                        <li><a class="{{ route_is('outstock') ? 'active' : '' }}" href="{{ route('outstock') }}">Stok
                                Habis</a></li>
                        <li><a class="{{ route_is('expired') ? 'active' : '' }}" href="{{ route('expired') }}">Produk
                                Kedaluwarsa</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fe fe-user"></i> <span>Pemasok</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ route_is('suppliers.*') ? 'active' : '' }}"
                                href="{{ route('suppliers.index') }}">Data Pemasok</a></li>
                        <li><a class="{{ route_is('suppliers.create') ? 'active' : '' }}"
                                href="{{ route('suppliers.create') }}">Tambah Pemasok</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fe fe-star-o"></i> <span>Pembelian</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ route_is('purchases.*') ? 'active' : '' }}"
                                href="{{ route('purchases.index') }}">Data Pembelian</a></li>
                        <li><a class="{{ route_is('purchases.create') ? 'active' : '' }}"
                                href="{{ route('purchases.create') }}">Tambah Pembelian</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fe fe-activity"></i> <span>Penjualan</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ route_is('sales.*') ? 'active' : '' }}"
                                href="{{ route('sales.index') }}">Data Penjualan</a></li>
                        <li><a class="{{ route_is('sales.create') ? 'active' : '' }}"
                                href="{{ route('sales.create') }}">Transaksi Baru</a></li>
                        <li><a class="{{ route_is('customers.index') ? 'active' : '' }}"
                                href="{{ route('customers.index') }}">Data Pelanggan</a></li>
                    </ul>
                </li>



                <li class="submenu">
                    <a href="#"><i class="fe fe-document"></i> <span>Laporan</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ route_is('sales.report') ? 'active' : '' }}"
                                href="{{ route('sales.report') }}">Laporan Penjualan</a></li>
                        <li><a class="{{ route_is('purchases.report') ? 'active' : '' }}"
                                href="{{ route('purchases.report') }}">Laporan Pembelian</a></li>
                        <li><a class="{{ route_is('reports.stock') ? 'active' : '' }}"
                                href="{{ route('reports.stock') }}">Laporan Stok</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fas fa-history"></i> <span>Riwayat</span> <span
                            class="menu-arrow"></span></a>
                    <ul>
                        <li><a href="{{ route('riwayat.penjualan') }}">Riwayat Penjualan</a></li>
                        <li><a href="{{ route('riwayat.pembelian') }}">Riwayat Pembelian</a></li>
                    </ul>
                </li>


                <li class="{{ route_is('users.*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}"><i class="fe fe-users"></i> <span>Pengguna</span></a>
                </li>

                <li class="{{ route_is('profile') ? 'active' : '' }}">
                    <a href="{{ route('profile') }}"><i class="fe fe-user-plus"></i> <span>Profil</span></a>
                </li>

                {{-- <li class="{{ route_is('backup.index') ? 'active' : '' }}">
					<a href="{{ route('backup.index') }}"><i class="material-icons">backup</i> <span>Cadangan</span></a>
				</li> --}}

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
