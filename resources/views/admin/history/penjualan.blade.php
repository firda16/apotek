@extends('admin.layouts.app')

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">{{ $title }}</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('riwayat.penjualan') }}">Riwayat</a></li>
            <li class="breadcrumb-item active">Penjualan</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4>Riwayat Penjualan</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>No Antrian</th>
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Harga per Produk</th>
                                    <th>Diskon</th>
                                    <th>Total Harga</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item['no_antrian'] ?? '-' }}</td>
                                        <td>{{ $item['produk'] }}</td>
                                        <td>{{ $item['kategori'] }}</td>
                                        <td>{{ $item['jumlah'] }}</td>
                                        <td>{{ $item['satuan'] ?? '-' }}</td>
                                        <td>Rp{{ number_format($item['harga'], 0, ',', '.') }}</td>
                                        <td>{{ $item['diskon'] ?? 0 }}%</td>
                                        <td>Rp{{ number_format($item['total'], 0, ',', '.') }}</td>
                                        <td>{{ ucfirst($item['metode_pembayaran']) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
