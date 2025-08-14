@extends('kasir.layouts.app')

{{-- <x-assets.datatables /> --}}

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Stok Produk Tersedia</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kasir.dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Produk</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            <!-- Daftar Produk -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="product-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Stok Tersedia</th>
                                    {{-- <th>Aksi</th> --}}
                                </tr>
                            </thead>
                        </table>

                    </div>
                    {{-- Pagination --}}
                    {{-- <div class="mt-3">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div> --}}
                    <x-assets.datatables />

                </div>
            </div>
            <!-- /Daftar Produk -->

        </div>
    </div>
@endsection

@push('page-js')
    <script>
        $(document).ready(function() {
            $('#product-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kasir.products.available') }}", // Ganti dengan route yg benar
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
                        data: 'available_stock',
                        name: 'available_stock'
                    },
                    // {
                    //     data: 'action',
                    //     name: 'action',
                    //     orderable: false,
                    //     searchable: false
                    // }
                ]
            });
        });
    </script>
@endpush
