@extends('admin.layouts.app')

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">{{ $title }}</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-purchase"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-purchase"><a href="{{ route('riwayat.pembelian') }}">Riwayat</a></li>
            <li class="breadcrumb-purchase active">Pembelian</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4>Riwayat Pembelian</h4>
                    <form method="GET" class="row g-2 mb-4">
                        <div class="col-md-3">
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama obat / pemasok"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="metode_pembayaran" class="form-control">
                                <option value="">Semua Pembayaran</option>
                                <option value="tunai" {{ request('metode_pembayaran') == 'tunai' ? 'selected' : '' }}>Tunai
                                </option>
                                <option value="transfer" {{ request('metode_pembayaran') == 'transfer' ? 'selected' : '' }}>
                                    Transfer</option>
                                <option value="qris" {{ request('metode_pembayaran') == 'qris' ? 'selected' : '' }}>QRIS
                                </option>
                            </select>
                        </div>
                        <div class="col-md-1 d-grid">
                            <button class="btn btn-primary" type="submit">Filter</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Pemasok</th>
                                    <th>Tanggal</th>
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Satuan</th> {{-- baru --}}
                                    <th>Harga Beli</th>
                                    <th>Jumlah</th>                                    
                                    <th>Metode Pembayaran</th> {{-- baru --}}
                                    <th>Total Harga</th> {{-- baru --}}
                                    <th>Tanggal Kedaluwarsa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $purchase)
                                    <tr>
                                        <td>{{ $purchases->firstItem() + $loop->index }}</td>                                         
                                        <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y') }}</td>
                                        <td>{{ $purchase->product ?? '-' }}</td>
                                        <td>{{ $purchase->category->name ?? '-' }}</td>
                                        <td>{{ $purchase->unit ?? '-' }}</td> {{-- satuan --}}
                                        <td>Rp{{ number_format($purchase->price, 0, ',', '.') }}</td> {{-- harga beli per produk --}}
                                        <td>{{ $purchase->quantity }}</td>                                        
                                        {{-- diskon --}}
                                        <td>{{ ucfirst($purchase->payment_method ?? '-') }}</td> {{-- metode pembayaran --}}
                                        <td>
                                            Rp{{ number_format($purchase->cost_price ?? $purchase->quantity * $purchase->price, 0, ',', '.') }}
                                        </td> {{-- total akhir --}}
                                        <td>
                                            @php
                                                $expired = \Carbon\Carbon::parse($purchase['expired_at']);
                                                $isExpired = $expired->isPast();
                                            @endphp

                                            <span class="badge bg-{{ $isExpired ? 'danger' : 'success' }}">
                                                @if ($isExpired)
                                                    <span title="Obat kedaluwarsa">⏳</span> {{ $expired->format('d-m-Y') }}
                                                @else
                                                    {{ $expired->format('d-m-Y') }}
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="10" class="text-end fw-bold">Total Pembelian</td>
                                    <td colspan="3" class="fw-bold text-primary">
                                        Rp{{ number_format($purchases->sum('total_price'), 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>


                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        {{ $purchases->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
