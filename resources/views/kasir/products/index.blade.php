@extends('kasir.layouts.app')

{{-- <x-assets.datatables /> --}}

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Data Produk</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kasir.dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Produk</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="filter-category" class="form-control">
                        <option value="">-- Semua Kategori --</option>
                        @foreach (\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="filter-unit" class="form-control">
                        <option value="">-- Semua Unit --</option>
                        @foreach (\App\Models\Product::select('unit')->distinct()->get() as $u)
                            <option value="{{ $u->unit }}">{{ $u->unit }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

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
            let table = $('#product-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('kasir.products.datatable') }}',
                    data: function(d) {
                        d.category = $('#filter-category').val();
                        d.unit = $('#filter-unit').val();
                    }
                },
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
                ],
                order: [
                    [1, 'asc']
                ]
            });

            // Event ketika filter berubah
            $('#filter-category, #filter-unit').change(function() {
                table.draw();
            });
        });
    </script>
@endpush
