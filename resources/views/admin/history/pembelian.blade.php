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
                                    <th>Pemasok</th>
                                    <th>Tanggal</th>
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Satuan</th>
                                    <th>Harga Beli</th>
                                    <th>Jumlah</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Total Harga</th>
                                    <th>Tanggal Kedaluwarsa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($purchases as $purchase)
                                    @foreach ($purchase->items as $item)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y') }}</td>
                                            <td>{{ $item->product->name ?? '-' }}</td>
                                            <td>{{ $item->product->category->name ?? '-' }}</td>
                                            <td>{{ $item->product->unit ?? '-' }}</td>
                                            <td>Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ ucfirst($purchase->payment_method ?? '-') }}</td>
                                            <td>Rp{{ number_format($item->subtotal ?? $item->quantity * $item->unit_price, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                @php
                                                    $expired = \Carbon\Carbon::parse($item->expiry_date); // BUKAN expired_at
                                                    $isExpired = $expired->isPast();
                                                @endphp

                                                <span class="badge bg-{{ $isExpired ? 'danger' : 'success' }}">
                                                    {{ $expired->format('d-m-Y') }}
                                                </span>

                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <h5 class="text-end">
                           <strong>Total Keseluruhan Pembelian:</strong>
                            <span><strong>Rp{{ number_format($totalPembelian, 0, ',', '.') }}</strong></span>
                        </h5>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $purchases->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
