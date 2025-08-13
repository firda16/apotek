{{-- Menggunakan layout utama milik KASIR --}}
@extends('kasir.layouts.app')


<style>
    /* Container form filter */
    form.form-filter {
        background: #f9fafb;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    }

    /* Label */
    form.form-filter label {
        font-weight: 600;
        color: #374151;
    }

    /* Input & Select */
    form.form-filter .form-control,
    form.form-filter .form-select {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    form.form-filter .form-control:focus,
    form.form-filter .form-select:focus {
        border-color: #1A4D6D;
        box-shadow: 0 0 0 0.2rem rgba(26, 77, 109, 0.25);
    }

    /* Tombol filter */
    form.form-filter .btn-primary {
        border-radius: 8px;
        background-color: #1A4D6D;
        border-color: #1A4D6D;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    form.form-filter .btn-primary:hover {
        background-color: #153e5b;
        border-color: #153e5b;
    }

    /* Pastikan tombol aksi punya jarak */
    .action-buttons button {
        margin-right: 12px !important;
        /* jarak pasti */
    }
</style>

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">{{ $title }}</h3>
        <ul class="breadcrumb">
            {{-- PERBAIKAN: Mengarah ke dashboard kasir --}}
            <li class="breadcrumb-item"><a href="{{ route('kasir.dashboard') }}">Beranda</a></li>
            {{-- PERBAIKAN: Mengarah ke riwayat penjualan kasir --}}
            <li class="breadcrumb-item"><a href="{{ route('kasir.riwayat.penjualan') }}">Riwayat</a></li>
            <li class="breadcrumb-item active">Penjualan</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- PERBAIKAN: Form action mengarah ke route kasir --}}
                    <form action="{{ route('kasir.riwayat.penjualan') }}" method="GET" class="form-filter mb-4">
                        <div class="row g-3 align-items-end">

                            <div class="col-lg-3 col-md-6">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date"
                                    value="{{ request('start_date') }}">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label for="end_date" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="end_date" name="end_date"
                                    value="{{ request('end_date') }}">
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                <select name="payment_method" id="payment_method" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>
                                        Cash</option>
                                    <option value="Transfer"
                                        {{ request('payment_method') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="QRIS" {{ request('payment_method') == 'QRIS' ? 'selected' : '' }}>QRIS
                                    </option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 d-flex align-items-center justify-content-end">
                                <div class="d-flex action-buttons">
                                    <button type="submit" class="btn btn-primary" title="Filter Data">
                                        <i class="fas fa-filter me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('kasir.riwayat.penjualan') }}" class="btn btn-secondary"
                                        title="Reset Filter">
                                        <i class="fas fa-redo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if ($filterApplied)
                        <div class="text-end mb-3">
                            @if ($sales->count() > 0)
                                {{-- PERBAIKAN: Cetak PDF mengarah ke route kasir --}}
                                <a href="{{ route('kasir.riwayat.penjualan.pdf', request()->query()) }}" target="_blank"
                                    class="btn btn-success">
                                    <i class="fas fa-print me-1"></i> Cetak PDF
                                </a>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead class="table-primary">
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>Tanggal Penjualan</th>
                                        <th>Nomor Invoice</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Metode Pembayaran</th>
                                        <th class="text-end">Total Harga</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sales as $sale)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration + $sales->firstItem() - 1 }}</td>

                                            <td>{{ \Carbon\Carbon::parse($sale->created_at)->translatedFormat('l, d F Y') }}
                                            </td>
                                            <td>{{ $sale->invoice_number ?? '-' }}</td>
                                            <td>{{ $sale->customer->nama ?? 'Umum' }}</td>
                                            <td class="text-center">{{ ucfirst($sale->payment_method ?? '-') }}</td>
                                            <td class="text-end">
                                                Rp{{ number_format($sale->total_price ?? 0, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                @if (!empty($sale->invoice_number))
                                                    {{-- PERBAIKAN: Tombol detail mengarah ke route kasir --}}
                                                    <a href="{{ route('kasir.riwayat.penjualan.show', ['invoice_number' => $sale->invoice_number]) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center p-4">
                                                <p class="text-muted mb-0">Tidak ada data penjualan.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($sales->count() > 0)
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="alert alert-primary mb-0 py-2 px-3 shadow-sm d-flex align-items-center">
                                    <strong>Total Pendapatan :</strong>
                                    &nbsp;<span
                                        class="fw-bold">Rp{{ number_format($total_pendapatan, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    @if ($sales->hasPages())
                                        {{ $sales->appends(request()->query())->links('pagination::bootstrap-5') }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center p-5 bg-light rounded">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Mulai Pencarian</h5>
                            <p class="text-muted small">Gunakan filter di atas untuk menampilkan riwayat penjualan.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
