@extends('admin.layouts.app')

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">{{ $title }}</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('riwayat.pembelian') }}">Riwayat</a></li>
            <li class="breadcrumb-item active">Pembelian</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4>Riwayat Pembelian</h4>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y') }}</td>
                                        <td>{{ $item['nama'] }}</td>
                                        <td>{{ $item['produk'] }}</td>
                                        <td>{{ $item['kategori'] }}</td>
                                        <td>{{ $item['jumlah'] }}</td>
                                        <td>Rp{{ number_format($item['total'], 0, ',', '.') }}</td>
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
