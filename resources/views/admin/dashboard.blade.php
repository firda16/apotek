@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/chart.js/Chart.min.css') }}">

    <style>
        .card {
            min-height: 160px;
            /* samain tinggi semua card */
        }

        .card .form-select-sm {
            font-size: 12px;
            padding: 2px 6px;
        }

        /* Kustomisasi Dropdown Filter */
        .filter-dropdown {
            background-color: #f8f9fa;
            /* Warna latar sedikit abu-abu */
            border: 1px solid #dee2e6;
            /* Border lebih soft */
            border-radius: 20px;
            /* Membuat sudut sangat tumpul (pill shape) */
            font-size: 12px;
            /* Ukuran font */
            padding: 4px 30px 4px 12px; /* Atas, Kanan, Bawah, Kiri */

            /* Padding internal */
            -webkit-appearance: none;
            /* Menghilangkan tampilan default di Chrome/Safari */
            -moz-appearance: none;
            /* Menghilangkan tampilan default di Firefox */
            appearance: none;
            /* Menghilangkan tampilan default */
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            /* Menambahkan ikon panah kustom */
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 16px 12px;
            transition: all 0.2s ease-in-out;
            /* Animasi transisi halus */
        }

        .filter-dropdown:hover {
            border-color: #007bff;
            /* Ganti warna border saat cursor di atasnya */
            cursor: pointer;
        }

        .filter-dropdown:focus {
            outline: none;
            /* Hilangkan outline biru saat diklik */
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
            /* Ganti dengan shadow yang lebih soft */
            border-color: #80bdff;
        }
    </style>
@endpush

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Selamat Datang {{ auth()->user()->name }}!</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item active">Beranda</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">

        {{-- Total Pengeluaran --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <a href="{{ route('riwayat.pembelian') }}" class="text-decoration-none text-dark"
                style="position: relative; z-index: 2;">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-warning border-warning">
                                <i class="fe fe-money"></i>
                            </span>
                            <div class="dash-count">
                                <h3 class="text-center">Rp
                                    {{ number_format($total_pengeluaran_hari_ini ?? 0, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Total Pengeluaran Hari Ini</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning w-50"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Total Pendapatan --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <a href="{{ route('riwayat.penjualan') }}" class="text-decoration-none text-dark">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-success border-success">
                                <i class="fe fe-money"></i>
                            </span>
                            <div class="dash-count">
                                <h3 class="text-center">Rp {{ number_format($total_pendapatan_hari_ini ?? 0, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Total Pendapatan Hari Ini</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success w-50"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Total Pengeluaran Bulan INi --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <a href="{{ route('riwayat.pembelian') }}" class="text-decoration-none text-dark"
                style="position: relative; z-index: 2;">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-warning border-warning">
                                <i class="fe fe-money"></i>
                            </span>
                            <div class="dash-count">
                                <h3 class="text-center">
                                    Rp {{ number_format($total_pengeluaran_bulan_ini ?? 0, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Total Pengeluaran Bulan Ini</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning w-50"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        {{-- Total Pendapatan bulan ini --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <a href="{{ route('riwayat.penjualan') }}" class="text-decoration-none text-dark">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-success border-success">
                                <i class="fe fe-money"></i>
                            </span>
                            <div class="dash-count">
                                <h3 class="text-center">
                                    Rp {{ number_format($total_pendapatan_bulan_ini ?? 0, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Total Pendapatan Bulan Ini</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success w-50"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <a class="{{ route_is('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-info border-info">
                                <i class="fe fe-folder"></i>

                            </span>
                            <div class="dash-count text-dark">
                                <h3>{{ $total_products }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Total Produk yang dibeli</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-info w-50"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div> --}}
        {{-- <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <a class="{{ route_is('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-success border-success">
                                <i class="fe fe-folder"></i>

                            </span>
                            <div class="dash-count text-dark">
                                <h3>{{ $total_sales }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-success">
                            <h6 class="text-muted">Total Produk yang terjual</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success w-50"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div> --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a class="{{ route_is('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                            <div class="dash-widget-header">
                                <span class="dash-widget-icon text-success border-success">
                                    <i class="fe fe-folder"></i>
                                </span>
                                <div class="dash-count text-dark ml-3">
                                    <h3>{{ $total_sales }}</h3>
                                </div>
                            </div>
                        </a>
                        {{-- <select name="filter_sales" onchange="this.form.submit()" class="form-select form-select-sm">
                        </select> --}}


                        <form method="GET" action="{{ route('dashboard') }}">
                            <select name="filter_sales" onchange="this.form.submit()" class="filter-dropdown">
                                <option value="today" {{ $filter_sales == 'today' ? 'selected' : '' }}>Hari ini</option>
                                <option value="month" {{ $filter_sales == 'month' ? 'selected' : '' }}>Bulan ini</option>
                                <option value="all" {{ $filter_sales == 'all' ? 'selected' : '' }}>Semua</option>
                            </select>
                        </form>
                    </div>
                    <div class="dash-widget-success mt-2">
                        <h6 class="text-muted">Total Produk yang terjual</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success w-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produk Tersedia -->
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <a href="{{ route('products.available') }}"
                    class="text-decoration-none {{ route_is('products.*') ? 'active' : '' }}">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-primary border-primary">
                                <i class="fe fe-cart"></i>
                            </span>
                            <div class="dash-count text-dark">
                                <h3>{{ $stok_produk }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Produk Stok yang tersedia</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary w-50"></div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        {{-- stok habis --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <a href="{{ route('outstock') }}"
                    class="text-decoration-none {{ route_is('outstock') ? 'active' : '' }}">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-danger border-danger">
                                <i class="fe fe-warning"></i>
                            </span>
                            <div class="dash-count text-dark">
                                <h3>{{ $out_of_stock_products }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Stok Habis</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-danger w-50"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>



        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <a class="{{ route_is('expired') ? 'active' : '' }}" href="{{ route('expired') }}">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-danger border-danger">
                                <i class="fe fe-trash"></i>
                            </span>
                            <div class="dash-count text-dark">
                                <h3>{{ $total_expired_products }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Produk Kedaluwarsa</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-danger w-50"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <a class="{{ route_is('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-primary border-primary">
                                <i class="fe fe-users"></i>
                            </span>
                            <div class="dash-count text-dark">
                                <h3>{{ $total_suppliers }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Jumlah Pemasok</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary w-50"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- Tabel Pembelian Hari Ini -->
        <div class="col-md-12 col-lg-6">
            <div class="card card-table p-3">
                <div class="card-header">
                    <h4 class="card-title">Pembelian Hari Ini</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Obat</th>
                                    <th>Jumlah</th>
                                    <th>Total Harga</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($latest_purchases->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada pembelian hari ini</td>
                                    </tr>
                                @else
                                    @foreach ($latest_purchases as $purchase)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $purchase->product->name ?? '-' }}</td>
                                            <td>{{ $purchase->quantity }}</td>
                                            <td class="text-center">Rp
                                                {{ number_format($purchase->total_price, 0, ',', '.') }}</td>
                                            {{-- <td>{{ $purchase->created_at->format('d M Y H:i') }}</td> --}}
                                            <td>{{ \Carbon\Carbon::parse($purchase->created_at)->translatedFormat('l, d F Y H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Penjualan Hari Ini -->
        <div class="col-md-12 col-lg-6">
            <div class="card card-table p-3">
                <div class="card-header">
                    <h4 class="card-title">Penjualan Hari Ini</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Obat</th>
                                    <th>Jumlah</th>
                                    <th>Total Harga</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($latest_sales->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada penjualan hari ini</td>
                                    </tr>
                                @else
                                    @foreach ($latest_sales as $sale)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sale->product->name ?? '-' }}</td>
                                            <td>{{ $sale->quantity }}</td>
                                            <td class="text-center">Rp
                                                {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                                            {{-- <td>{{ $sale->created_at->format('d M Y H:i') }}</td> --}}
                                            <td>{{ \Carbon\Carbon::parse($sale->created_at)->translatedFormat('l, d F Y H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        <!-- Diagram Pie -->
        <div class="col-md-12 col-lg-6">
            <div class="card card-chart">
                <div class="card-header">
                    <h4 class="card-title text-center">Sumber Daya</h4>
                </div>
                <div class="card-body">
                    @if (isset($pieChart))
                        {!! $pieChart->container() !!}
                    @else
                        <p class="text-muted text-center">Diagram tidak tersedia</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">

    </div>
@endsection

@push('page-js')
    <script>
        $(document).ready(function() {
            $('#sales-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('sales.data') }}",
                columns: [{
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                ]
            });
        });
    </script>

    <script src="{{ asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>
    @if (isset($pieChart))
        {!! $pieChart->script() !!}
    @endif
@endpush
