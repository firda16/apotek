@extends('kasir.layouts.app')

<x-assets.datatables />

@push('page-css')
{{-- No specific CSS changes needed here for this refactor --}}
@endpush

@push('page-header')
<div class="col-sm-12">
    <h3 class="page-title">Stok Habis</h3>
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('kasir.products.index')}}">Produk</a></li>
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
                    <table id="outstock-product" class="table table-hover table-center mb-0">
                        <thead>
                            <tr>
								<th>No</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                {{-- <th>Harga</th> --}}
                                <th>Jumlah</th>
                                {{-- <th>Diskon</th> --}}
                                {{-- <th>Kedaluwarsa</th> --}}
                                {{-- <th class="action-btn">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
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
   $('#outstock-product').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('kasir.products.outstock.datatable') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'category', name: 'category' },
            { data: 'stok', name: 'stok' },
            // { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[1, 'asc']]
    });
});
</script>
@endpush