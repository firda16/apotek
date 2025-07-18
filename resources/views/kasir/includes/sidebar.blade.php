<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Menu Utama Kasir</span>
                </li>

                <li class="{{ route_is('kasir.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('kasir.dashboard') }}">
                        <i class="fe fe-layout"></i>
                        <span>Dashboard Kasir</span>
                    </a>
                </li>

                <li class="submenu {{ route_is('kasir.transaksi') || route_is('outstock') || route_is('expired') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fe fe-shopping-bag"></i>
                        <span>Transaksi</span>
                        <span class="menu-arrow"></span>
                    </a>

                    <ul>
                        <li class="{{ route_is('kasir.transaksi') ? 'active' : '' }}">
                            <a href="{{ route('kasir.transaksi') }}">Input Transaksi</a>
                        </li>
                        <li class="{{ route_is('outstock') ? 'active' : '' }}">
                            <a href="{{ route('outstock') }}">Stok Habis</a>
                        </li>
                        <li class="{{ route_is('expired') ? 'active' : '' }}">
                            <a href="{{ route('expired') }}">Produk Kedaluwarsa</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ route_is('kasir.laporan') ? 'active' : '' }}">
                    <a href="{{ route('kasir.laporan') }}">
                        <i class="fe fe-file-text"></i>
                        <span>Riwayat</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
