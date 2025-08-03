@extends('admin.layouts.app')

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">{{ $title }}</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-sale"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-sale"><span class="mx-1">/</span><a href="{{ route('riwayat.penjualan') }}">Riwayat</a></li>
            <li class="breadcrumb-sale active"><span class="mx-1">/</span>Penjualan</li>
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
                                    <th>Nama Pelanggan</th>
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
                                    @php $first = true; @endphp
                                    @foreach ($sale->saleItems as $item)
                                        <tr>
                                            @if ($first)
                                                <td rowspan="{{ $sale->saleItems->count() }}">
                                                    {{ $loop->parent->iteration + $sales->firstItem() - 1 }}</td>
                                                <td rowspan="{{ $sale->saleItems->count() }}">
                                                    {{ $sale->customer->nama ?? '-' }}</td>
                                                @php $first = false; @endphp
                                            @endif

                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->product->category->name ?? '-' }}</td>
                                            <td>{{ $item->quantity ?? '-' }}</td>
                                            <td>{{ $item->product->unit ?? '-' }}</td>
                                            <td>Rp{{ number_format($item->unit_price ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ number_format($sale->discount ?? 0, 2, ',', '.') }}%</td>
                                            <td>Rp{{ number_format($sale->total_price ?? 0, 0, ',', '.') }}</td>
                                            <td>{{ ucfirst($sale->payment_method ?? '-') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sale->created_at ?? now())->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <h5 class="text-end">
                            <strong>Total Keseluruhan Penjualan:</strong>
                            <span><strong>Rp {{ number_format($total_pendapatan, 0, ',', '.') }}</strong></span>
                        </h5>
                    </div>


                    <div class="d-flex justify-content-end mt-3">
                        {{ $sales->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
