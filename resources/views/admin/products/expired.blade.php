@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
    <style>
        .badge {
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
        }

        .bg-secondary {
            background-color: #e2e3e5;
            color: #383d41;
        }
    </style>
@endpush

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Produk Kedaluwarsa</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
            <li class="breadcrumb-item active">Kedaluwarsa</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex justify-content-between mb-3">
                <h5>Produk Mendekati & Sudah Kadaluarsa</h5>
                <div class="mb-3">
                    <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="Akan Kadaluarsa">Akan
                        Kadaluarsa</button>
                    <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="Sudah Kadaluarsa">Sudah
                        Kadaluarsa</button>
                    <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="All">Tampilkan Semua</button>
                </div>


            </div>
            <!-- Produk Kedaluwarsa -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="expired-product"
                            class="datatable table table-striped table-bordered table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Merek</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Kedaluwarsa</th>
                                    <th>Status</th> <!-- Kolom baru -->
                                    <th class="action-btn">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    @php
                                        // Ambil semua purchaseItems yang akan/sudah expired
                                        $relevantItems = $product->purchaseItems->sortBy('expiry_date');
                                        $closestItem = $relevantItems->first();

                                        if (!$closestItem) {
                                            continue;
                                        } // Skip jika tidak ada

                                        $expiryDate = \Carbon\Carbon::parse($closestItem->expiry_date);
                                        $today = now();
                                        $daysDiff = $expiryDate->diffInDays($today, false); // negatif = sudah lewat

                                        if ($expiryDate->lt($today)) {
                                            $status =
                                                '<span class="badge text-white bg-danger">Sudah Kadaluarsa</span>';
                                        } elseif ($expiryDate->lte($today->copy()->addDays($soonExpiryDays))) {
                                            $status = '<span class="badge bg-warning text-dark">Akan Kadaluarsa</span>';
                                        } else {
                                            $status = '<span class="badge bg-secondary">Aktif</span>';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $products->firstItem() + $loop->index }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category?->name ?? '-' }}</td>
                                        <td>{{ (settings('app_currency') ?? 'Rp') . ' ' . number_format($product->price, 0, ',', '.') }}
                                        </td>
                                        <td>{{ $relevantItems->sum('quantity') }}</td>
                                        <td>{{ $expiryDate->translatedFormat('d F Y') }}</td>
                                        <td>{!! $status !!}</td> <!-- Status dengan badge -->
                                        <td>
                                            <a href="{{ route('products.edit', $product->id) }}"
                                                class="btn btn-sm btn-primary">Edit</a>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger" id="deletebtn"
                                                data-id="{{ $product->id }}"
                                                data-route="{{ route('products.destroy', $product->id) }}">Hapus</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            <!-- /Produk Kedaluwarsa -->

        </div>
    </div>
@endsection

@push('page-js')
    <script>
        document.querySelectorAll('.filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const filter = this.dataset.filter;

                const rows = document.querySelectorAll('#expired-product tbody tr');

                rows.forEach(function(row) {
                    const statusCell = row.querySelector('td:nth-child(7)').textContent.trim();

                    if (filter === 'All') {
                        row.style.display = '';
                    } else if (statusCell.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
@endpush


@push('page-js')
    {{-- <script>
    $(document).ready(function() {
        var table = $('#expired-product').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('expired')}}",
            columns: [
                {data: 'product', name: 'product'},
                {data: 'category', name: 'category'},
                {data: 'price', name: 'price'},
                {data: 'quantity', name: 'quantity'},
                {data: 'discount', name: 'discount'},
				{data: 'expiry_date', name: 'expiry_date'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });
</script> --}}
@endpush
