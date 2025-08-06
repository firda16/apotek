@extends('kasir.layouts.app')

@section('content')
    <div class="page-header">
        <h4>Detail Transaksi</h4>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h6><strong>Kode Transaksi:</strong> {{ $transaksi->kode_transaksi }}</h6>
            <h6><strong>Tanggal:</strong> {{ $transaksi->created_at->format('d-m-Y H:i') }}</h6>
            <h6><strong>Total Harga:</strong> Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</h6>
            <h6><strong>Metode Pembayaran:</strong> {{ $transaksi->payment_method ?? '-' }}</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Produk Terjual</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th>Harga Satuan</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaksi->details as $detail)
                        <tr>
                            <td>{{ $detail->produk->nama_produk }}</td>
                            <td>Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-end">
                <h5>Total: Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('kasir.laporan') }}" class="btn btn-secondary">⬅️ Kembali ke Laporan</a>
    </div>
@endsection
