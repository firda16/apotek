<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Menu Utama Kasir</span>
                </li>

                {{-- Beranda --}}
                <li class="{{ Request::is('kasir/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('kasir.dashboard') }}">
                        <i class="fas fa-home"></i> <span>Beranda</span>
                    </a>
                </li>

                <li class="{{ route_is('kasircategories.index') ? 'active' : '' }}">
                    <a href="{{ route('kasir.categories.index') }}"><i class="fe fe-layout"></i>
                        <span>Kategori</span></a>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fe fe-document"></i> <span>Produk</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ route_is('kasir.products.index') ? 'active' : '' }}"
                                href="{{ route('kasir.products.index') }}">Data Produk</a></li>
                        <li><a class="{{ route_is('kasir.products.available') ? 'active' : '' }}"
                                href="{{ route('kasir.products.available') }}">Stok
                                Tersedia</a></li>
                        <li><a class="{{ route_is('kasir.products.outstock') ? 'active' : '' }}"
                                href="{{ route('kasir.products.outstock') }}">Stok
                                Habis</a></li>
                        <li><a class="{{ route_is('kasir.products.expired') ? 'active' : '' }}"
                                href="{{ route('kasir.products.expired') }}">Produk
                                Kedaluwarsa</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fe fe-add-cart"></i> <span>Penjualan</span> <span
                            class="menu-arrow"></span></a>
                    <ul style="display: none;">
                        <li><a class="{{ route_is('kasir.transaksi') ? 'active' : '' }}"
                                href="{{ route('kasir.transaksi') }}">Data
                                Transaksi</a></li>
                        <li>
                            <a class="{{ route_is('kasir.transaksi.create') ? 'active' : '' }}"
                                href="{{ route('kasir.transaksi.create') }}">Transaksi Baru</a>
                        </li>
                        {{-- <li><a class="{{ route_is('kasir.customers.index') ? 'active' : '' }}"
                                href="{{ route('kasir.customers.index') }}">Data Pelanggan</a></li> --}}
                    </ul>
                </li>
                <li class="{{ route_is('kasir.customers') ? 'active' : '' }}">
                    <a href="{{ route('kasir.customers') }}">
                        <i class="fe fe-user"></i>
                        <span>Data Pelanggan</span>
                    </a>
                </li>
                <li class="{{ route_is('kasir.riwayat.penjualan*') ? 'active' : '' }}">
                    <a href="{{ route('kasir.riwayat.penjualan') }}">
                        <i class="fe fe-book"></i>
                        <span>Riwayat Penjualan</span>
                    </a>
                </li>
                <li class="{{ Request::is('kasir/sales/reports*') ? 'active' : '' }}">
                    <a href="{{ route('kasir.sales.report') }}">
                        <i class="fe fe-document"></i> <span>Laporan Penjualan</span>
                    </a>
                </li>
                {{-- <li class="{{ Request::is('kasir/panduan*') ? 'active' : '' }}">
                    <a href="{{ route('kasir.panduan') }}">
                       <i class="fas fa-info"></i> <span>Panduan</span>
                    </a>
                </li> --}}
                <li>
                    <a href="{{ asset('assets/Panduan Kasir - Apotek.pdf') }}" target="_blank">
                        <i class="fe fe-info"></i>
                        <span>Panduan Kasir</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
