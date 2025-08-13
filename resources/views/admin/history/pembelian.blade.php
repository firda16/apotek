@extends('admin.layouts.app')


<style>
    /* Container form filter */
    form {
        background: #f9fafb;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }


    /* Label */
    form label {
        font-weight: 600;
        color: #374151;
    }

    /* Input & Select */
    form .form-control,
    form .form-select {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    form .form-control:focus,
    form .form-select:focus {
        border-color: #1A4D6D;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    /* Tombol filter */
    form .btn-primary {
        border-radius: 8px;
        background-color: #1A4D6D;
        border-color: #1A4D6D;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    form .btn-primary:hover {
        background-color: #1A4D6D;
        border-color: #1A4D6D;
    }

    /* Responsive spacing for inputs */
    @media (max-width: 575.98px) {

        form .col-md-4,
        form .col-md-3,
        form .col-md-1 {
            margin-bottom: 1rem;
        }
    }

    /* Pastikan tombol aksi punya jarak */
    .action-buttons button, .action-buttons a {
        margin-right: 8px !important;
    }
    .action-buttons a:last-child {
        margin-right: 0 !important;
    }
</style>


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

                    {{-- Form Filter --}}
                    <form action="{{ route('riwayat.pembelian') }}" method="GET">
                        <div class="row g-3 align-items-end form-filter-row">

                            {{-- Tanggal Mulai --}}
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ request('start_date') }}">
                            </div>

                            {{-- Tanggal Selesai --}}
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ request('end_date') }}">
                            </div>

                            {{-- Metode Pembayaran --}}
                            <div class="col-md-3">
                                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                <select name="payment_method" id="payment_method" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="tunai" {{ request('payment_method') == 'tunai' ? 'selected' : '' }}>
                                        Tunai</option>
                                    <option value="transfer"
                                        {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>QRIS
                                    </option>
                                </select>
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="col-lg-3 col-md-6 d-flex align-items-center justify-content-end">
                                <div class="d-flex action-buttons">
                                    <button type="submit" class="btn btn-primary" title="Filter Data">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('riwayat.penjualan') }}" class="btn btn-secondary"
                                        title="Reset Filter">
                                        <i class="fas fa-redo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Data Table --}}
                    @if ($filterApplied && ($purchases->count() > 0 || request()->has('start_date')))
                        <hr>

                        {{-- PENAMBAHAN: Tombol Cetak PDF --}}
                        <div class="text-end mb-3">
                            {{-- Tombol ini hanya akan muncul jika ada data hasil filter --}}
                            @if($purchases->count() > 0)
                                {{-- URL menyertakan parameter filter yang sedang aktif --}}
                                <a href="{{ route('riwayat.pembelian.pdf', request()->query()) }}" target="_blank" class="btn btn-success">
                                    <i class="fas fa-print me-1"></i> Cetak PDF
                                </a>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Pemasok</th>
                                        <th>Tanggal Pembelian</th>
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
                                    @forelse ($purchases as $index => $purchase)
                                        @foreach ($purchase->items as $item)
                                            <tr>
                                                <td>{{ $purchases->firstItem() + $index }}</td>
                                                <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y') }}
                                                </td>
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
                                                        $expired = \Carbon\Carbon::parse($item->expiry_date);
                                                        $isExpired = $expired->isPast();
                                                    @endphp
                                                    <span class="badge bg-{{ $isExpired ? 'danger' : 'success' }}">
                                                        {{ $expired->format('d-m-Y') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted">
                                                Tidak ada data pembelian yang cocok dengan kriteria filter.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Total & Pagination --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="alert alert-success mb-0 py-2 px-3 shadow-sm d-flex align-items-center">
                                <strong>Total Pembelian :</strong>
                                &nbsp;Rp{{ number_format($totalPembelian, 0, ',', '.') }}
                            </div>
                            <div>
                                {{ $purchases->appends(request()->query())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="text-center p-4 p-md-5 bg-light rounded mt-4">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Mulai Pencarian</h5>
                            <p class="text-muted small">Silakan filter untuk menampilkan data riwayat
                                pembelian.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
