@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-header')
<div class="col-sm-12">
    <h3 class="page-title">{{ $title }}</h3>
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
                <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="Akan Kadaluarsa">Akan Kadaluarsa</button>
                <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="Sudah Kadaluarsa">Sudah Kadaluarsa</button>
                <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="All">Tampilkan Semua</button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="expired-table" class="table table-striped table-bordered table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Merek</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Tanggal Kedaluwarsa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <form method="POST" action="{{ route('products.deleteExpired') }}" onsubmit="return confirm('Hapus semua produk kadaluarsa?')">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fa fa-trash"></i> Hapus Semua Produk Kadaluarsa
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

@push('page-js')
<script>
$(function() {
    var table = $('#expired-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('products.expired.datatable') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'category', name: 'category' },
            { data: 'price', name: 'price' },
            { data: 'quantity', name: 'quantity' },
            { data: 'expiry_date', name: 'expiry_date' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
        ]
    });

    // filter by status
    $('.filter-btn').on('click', function() {
        var filter = $(this).data('filter');
        if (filter === 'All') {
            table.column(6).search('').draw();
        } else {
            table.column(6).search(filter).draw();
        }
    });
});
</script>
@endpush
