@extends('admin.layouts.app')

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">{{ $title }}</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-sale"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-sale"><a href="{{ route('riwayat.penjualan') }}">Riwayat</a></li>
            <li class="breadcrumb-sale active">Penjualan</li>
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
                                @foreach ($sales as $sale)
                                    <tr>
                                      <td>{{ $sales->firstItem() + $loop->index }}</td>
                                        <td>{{ $sale->no_antrian ?? '-' }}</td>
                                        <td>{{ $sale->product->purchase->product ?? '-' }}</td>
                                        <td>{{ $sale->product->purchase->category->name ?? '-' }}</td>
                                        <td>{{ $sale->quantity ?? '-' }}</td>
                                        <td>{{ $sale->unit ?? '-' }}</td>
                                        <td>Rp{{ number_format($sale->price, 0, ',', '.') }}</td>
                                        <td>{{ $sale->discount ?? 0 }}%</td>
                                        <td>Rp{{ number_format($sale->total_price ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ ucfirst($sale->metode_pembayaran ?? '-') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sale->created_at ?? 'now')->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $sales->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
