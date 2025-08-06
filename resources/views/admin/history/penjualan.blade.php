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
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Tanggal Penjualan</th>
                                    <th>Nomor Invoice</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Nomor HP</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Total Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sales as $sale)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration + $sales->firstItem() - 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y') }}</td>
                                        <td>{{ $sale->invoice_number ?? '-' }}</td>
                                        <td>{{ $sale->customer->nama ?? '-' }}</td>
                                        <td>{{ $sale->customer->telepon ?? '-' }}</td>
                                        <td>{{ ucfirst($sale->payment_method ?? '-') }}</td>
                                        <td>Rp{{ number_format($sale->total_price ?? 0, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if (!empty($sale->invoice_number))
                                                <a href="{{ route('riwayat.penjualan.show', ['invoice_number' => $sale->invoice_number]) }}"
                                                    class="btn btn-sm btn-primary">🔍 Detail</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data penjualan.</td>
                                    </tr>
                                @endforelse
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
