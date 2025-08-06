@extends('kasir.layouts.app')

@section('content')
    <div class="page-header">
        <h4>Laporan Penjualan Kasir</h4>
    </div>

    <!-- Filter Tanggal -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('kasir.laporan') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control"
                        value="{{ request('tanggal_selesai') }}">
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary"><i class="fe fe-filter"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Riwayat Transaksi -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Produk Terjual</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksis as $i => $transaksi)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                                <td>
                                    <ul>
                                        @foreach ($transaksi->details as $item)
                                            <li>{{ $item->produk->nama_produk }} (x{{ $item->jumlah }})</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('kasir.laporan.show', $transaksi->id) }}" class="btn btn-sm btn-info">
                                        🔍 Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
