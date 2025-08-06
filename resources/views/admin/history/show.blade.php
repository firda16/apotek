@extends('admin.layouts.app')

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Detail Penjualan</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('riwayat.penjualan') }}">Riwayat Penjualan</a></li>
            <li class="breadcrumb-item active">Detail Penjualan</li>
        </ul>
    </div>
@endpush

@push('page-css')
    <style>
        .info-box {
            background: #fff;
            /* dari sebelumnya #f8f9fa */
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
        }


        .info-box h5 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box .row+.row {
            margin-top: 10px;
        }

        .info-label {
            font-weight: 600;
            color: #000;
            /* TEKANKAN WARNA LABEL */
        }

        .info-value {
            color: #495057;
            /* Nilai tetap lembut agar kontras */
        }

        .product-table th {
            background-color: #f1f3f5;
            text-align: center;
        }

        .product-table td {
            vertical-align: middle;
        }

        .btn-back {
            padding: 8px 16px;
        }

        .icon-title {
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }
    </style>
@endpush


@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="info-box shadow-sm">
                <h5>
                    <iconify-icon icon="tabler:receipt" class="icon-title"></iconify-icon>
                    Informasi Transaksi
                </h5>
                <div class="row">
                    <div class="col-md-4">
                        <div><span class="info-label">No. Invoice:</span></div>
                        <div class="info-value">{{ $sale->invoice_number }}</div>
                    </div>
                    <div class="col-md-4">
                        <div><span class="info-label">Tanggal Penjualan:</span></div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div><span class="info-label">Metode Pembayaran:</span></div>
                        <div class="info-value">{{ ucfirst($sale->payment_method) }}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div><span class="info-label">Nama Pelanggan:</span></div>
                        <div class="info-value">{{ $sale->customer->nama ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div><span class="info-label">Nomor Telepon:</span></div>
                        <div class="info-value">{{ $sale->customer->telepon ?? '-' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div><span class="info-label">Diskon:</span></div>
                        <div class="info-value">{{ $sale->discount ?? 0 }}%</div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-end">
                        <span class="info-label">Total Harga:</span>
                        <span class="info-value fw-bold fs-5">Rp{{ number_format($sale->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="info-box shadow-sm">
                <h5>
                    <iconify-icon icon="tabler:shopping-cart" class="icon-title"></iconify-icon>
                    Daftar Produk Dibeli
                </h5>
                <div class="table-responsive">
                    <table class="table table-bordered product-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Satuan</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->saleItems as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->product->category->name ?? '-' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td>{{ $item->product->unit }}</td>
                                    <td>Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td>Rp{{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="text-end mt-3">
                <a href="{{ route('riwayat.penjualan') }}" class="btn btn-outline-secondary btn-back">
                    ← Kembali ke Riwayat
                </a>
            </div>

        </div>
    </div>
@endsection
