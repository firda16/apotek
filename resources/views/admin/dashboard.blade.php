@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/chart.js/Chart.min.css') }}">
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
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-warning border-warning">
                            <i class="fe fe-money"></i>
                        </span>
                        <div class="dash-count">
                            <h3 class="text-center">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Total pengeluaran</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-warning w-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pendapatan --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-success border-success">
                            <i class="fe fe-money"></i>
                        </span>
                        <div class="dash-count">
                            <h3 class="text-center">Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Total pendapatan</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-success w-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
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
        </div>
        <div class="col-xl-3 col-sm-6 col-12">
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
        </div>

        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-primary border-primary">
                            <i class="fe fe-cart"></i>
                        </span>
                        <div class="dash-count">
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
                <a href="{{ route('expired') }}" class="text-decoration-none {{ route_is('expired') ? 'active' : '' }}">
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
                                                {{ number_format($purchase->cost_price, 0, ',', '.') }}</td>
                                            <td>{{ $purchase->created_at->format('d M Y H:i') }}</td>
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
                                            <td>{{ $sale->product->purchase->product ?? '-' }}</td>
                                            <td>{{ $sale->quantity }}</td>
                                            <td class="text-center">Rp
                                                {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                                            <td>{{ $sale->created_at->format('d M Y H:i') }}</td>
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
