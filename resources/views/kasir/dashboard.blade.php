@extends('kasir.layouts.app')

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
        {{-- Total Pendapatan Hari Ini --}}
        <div class="col-xl-4 col-sm-6 col-12">
            <a href="{{ route('kasir.riwayat.penjualan') }}" class="text-decoration-none text-dark">
                <div class="card">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-success border-success">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                            <div class="dash-count">
                                <h3>Rp {{ number_format($total_pendapatan_hari_ini ?? 0, 0, ',', '.') }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">

                            <h6 class="text-muted">Pendapatan Hari Ini</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success w-50"></div>
                            </div>
                        </div>
                    </div>
            </a>
        </div>
    </div>

    {{-- Total Pendapatan Bulan Ini --}}
    <div class="col-xl-4 col-sm-6 col-12">
        <a href="{{ route('kasir.riwayat.penjualan') }}" class="text-decoration-none text-dark">
            <div class="card">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-info border-info">
                            <i class="fas fa-credit-card"></i>
                        </span>
                        <div class="dash-count">
                            <h3>Rp {{ number_format($total_pendapatan_bulan_ini ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">

                        <h6 class="text-muted">Pendapatan Bulan Ini</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-info w-50"></div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Total Transaksi --}}
    <div class="col-xl-4 col-sm-6 col-12">
        <div class="card">
            <a href="{{ route('kasir.transaksi') }}" class="text-decoration-none text-dark">
                <div class="card-body">
                    <div class="dash-widget-header">
                        <span class="dash-widget-icon text-primary border-primary">
                            <i class="fe fe-folder"></i>
                        </span>
                        <div class="dash-count text-dark">
                            <h3>{{ $total_sales ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="dash-widget-info">
                        <h6 class="text-muted">Total Penjualan</h6>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-primary w-50"></div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    </div>

    <div class="row mt-3">
        {{-- Produk Tersedia --}}
        <div class="col-xl-4 col-sm-6 col-12">
            <div class="card">
                <a href="{{ route('kasir.products.available') }}" class="text-decoration-none text-dark">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-secondary border-secondary">
                                <i class="fas fa-box"></i>
                            </span>
                            <div class="dash-count">
                                <h3>{{ $stok_produk ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Produk Tersedia</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-secondary w-50"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Stok Habis --}}
        <div class="col-xl-4 col-sm-6 col-12">
            <div class="card">
                <a href="{{ route('kasir.products.outstock') }}" class="text-decoration-none">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-danger border-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </span>
                            <div class="dash-count text-dark">
                                <h3>{{ $out_of_stock_products ?? 0 }}</h3>
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

        {{-- Produk Kedaluwarsa --}}
        <div class="col-xl-4 col-sm-6 col-12">
            <div class="card">
                <a href="{{ route('kasir.products.expired') }}" class="text-decoration-none">
                    <div class="card-body">
                        <div class="dash-widget-header">
                            <span class="dash-widget-icon text-warning border-warning">
                                <i class="fe fe-calendar"></i>
                            </span>
                            <div class="dash-count text-dark">
                                <h3>{{ $total_expired_products ?? 0 }}</h3>
                            </div>
                        </div>
                        <div class="dash-widget-info">
                            <h6 class="text-muted">Produk Kedaluwarsa</h6>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning w-50"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- BAGIAN YANG DITAMBAHKAN --}}
    <div class="row">
        <div class="col-md-12 col-lg-6">
            <div class="card card-table p-3">
                <div class="card-header">
                    <h4 class="card-title">Penjualan Terakhir Hari Ini</h4>
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
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latest_sales as $sale)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sale->product->name ?? '-' }}</td>
                                        <td>{{ $sale->quantity }}</td>
                                        <td class="text-end">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                                        <td>{{ $sale->created_at->format('H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada penjualan hari ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-6">
            <div class="card card-chart">
                <div class="card-header">
                    <h4 class="card-title text-center">Status Produk</h4>
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
@endsection

@push('page-js')
    <script src="{{ asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>
    @if (isset($pieChart))
        {!! $pieChart->script() !!}
    @endif
@endpush
