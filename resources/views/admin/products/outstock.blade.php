@extends('admin.layouts.app')

@push('page-header')
<div class="col-sm-12">
    <h3 class="page-title">{{ $title }}</h3>
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
        <li class="breadcrumb-item active">Stok Habis</li>
    </ul>
</div>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="outstock-table" class="table table-striped table-bordered table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                {{-- <th class="text-center">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('page-js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function() {
   $('#outstock-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('outstock.datatable') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'category', name: 'category' },
            { data: 'stock', name: 'stock' },
            // { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']]
    });
});
</script>
@endpush
