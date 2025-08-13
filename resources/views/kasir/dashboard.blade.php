@extends('kasir.layouts.app')

@push('page-css')
<link rel="stylesheet" href="{{ asset('assets/plugins/chart.js/Chart.min.css') }}">
@endpush

@push('page-header')
<div class="col-sm-12 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="page-title">Selamat Datang, {{ auth()->user()->name }}!</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard Kasir</li>
        </ul>
    </div>
    <a href="{{ route('kasir.transaksi.create') }}" class="btn btn-primary">
        <i class="fe fe-plus"></i> Transaksi Baru
    </a>
</div>
@endpush

@section('content')
<div class="row">
    <!-- Total Penjualan Hari Ini -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card border-start border-primary border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Penjualan Hari Ini</h6>
                        {{-- <h3 class="fw-bold">{{ AppSettings::get('app_currency', 'Rp') }} {{ $today_sales }}</h3> --}}
                        <h3 class="fw-bold text-primary">Rp 0</h3>
                    </div>
                    <i class="fe fe-trending-up text-primary fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori Produk -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card border-start border-success border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Kategori Produk</h6>
                        <h3 class="fw-bold text-success">{{ $total_categories }}</h3>
                    </div>
                    <i class="fe fe-layers text-success fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Obat Kedaluwarsa -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card border-start border-danger border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Produk Kedaluwarsa</h6>
                        {{-- <h3 class="fw-bold text-danger">{{ $total_expired_products }}</h3> --}}
                        <h3 class="fw-bold text-danger">0</h3>
                    </div>
                    <i class="fe fe-alert-triangle text-danger fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Pengguna -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card border-start border-warning border-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Pengguna Sistem</h6>
                        <h3 class="fw-bold text-warning">{{ \DB::table('users')->count() }}</h3>
                    </div>
                    <i class="fe fe-users text-warning fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ringkasan Transaksi Hari Ini + Chart -->
<div class="row">
    <!-- Tabel Penjualan Hari Ini -->
    <div class="col-md-12 col-lg-6">
        <div class="card card-table p-3 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Transaksi Hari Ini</h4>
                <span class="badge bg-info">Live</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="sales-table" class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>Nama Obat</th>
                                <th>Jumlah</th>
                                <th>Total Harga</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables akan diisi otomatis --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagram Pie -->
    <div class="col-md-12 col-lg-6">
        <div class="card card-chart shadow-sm">
            <div class="card-header text-center">
                <h4 class="card-title mb-0">Statistik Penjualan</h4>
            </div>
            <div class="card-body">
                @if(isset($pieChart))
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
<script>
    $(document).ready(function () {
        $('#sales-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('sales.data') }}",
            columns: [
                { data: 'product', name: 'product' },
                { data: 'quantity', name: 'quantity' },
                { data: 'total_price', name: 'total_price' },
                { data: 'date', name: 'date' },
            ]
        });
    });
</script>

<script src="{{ asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>
@if(isset($pieChart))
    {!! $pieChart->script() !!}
@endif
@endpush
