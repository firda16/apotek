@extends('admin.layouts.app')

{{-- <x-assets.datatables /> --}}

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Produk</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Produk</li>
        </ul>
    </div>
    <div class="col-sm-5 col">
        <a href="{{ route('products.create') }}" class="btn btn-primary float-right mt-2">Tambah Produk</a>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            <!-- Daftar Produk -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="product-table" class="table table-striped table-bordered table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Unit</th>
                                    <th>Harga beli</th>
                                    <th>Harga jual</th>
                                    <th>Deskripsi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    {{-- <div class="mt-3">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div> --}}
                </div>
            </div>
            <!-- /Daftar Produk -->

        </div>
    </div>
@endsection

@push('page-js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function() {
            $('#product-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('products.datatable') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'category',
                        name: 'category'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });
        });
    </script>
@endpush
