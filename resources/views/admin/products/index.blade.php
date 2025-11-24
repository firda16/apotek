@extends('admin.layouts.app')

{{-- <x-assets.datatables /> --}}

@push('page-css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .dataTables_filter {
            float: right !important;
            text-align: right !important;
        }

        .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
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
        {{-- Urutan coding float-right: Elemen pertama akan di paling KANAN --}}

        {{-- 1. Tombol Tambah (Paling Kanan) --}}
        <a href="{{ route('products.create') }}" class="btn btn-primary float-right mt-2">
            </i> Tambah Produk
        </a>

        {{-- 2. Tombol Import (Tengah) - Ada mr-2 biar gak nempel sama Tambah --}}
        <button type="button" class="btn btn-success float-right mt-2 mr-2" data-toggle="modal" data-target="#modalImport">
            <i class="fa fa-file-excel-o"></i> Import Excel
        </button>

        {{-- 3. Tombol Reset (Paling Kiri) - Ada mr-2 biar gak nempel sama Import --}}
        <form action="{{ route('products.destroyAll') }}" method="POST" class="float-right mt-2 mr-2">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"
                onclick="return confirm('BAHAYA! Yakin ingin menghapus SEMUA data? Data tidak bisa kembali!')">
                <i class="fa fa-trash"></i> Reset
            </button>
        </form>
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
    <div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportLabel">Import Data Produk</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pilih File Excel</label>
                            <input type="file" name="file" class="form-control" required accept=".xlsx, .xls, .csv">
                            <small class="text-muted">
                                Format wajib: <strong>nama_barang</strong> dan <strong>harga_jual</strong>.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload Sekarang</button>
                    </div>
                </form>
            </div>
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
                    url: '{{ route('products.datatable') }}',
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
                        data: 'unit_price', // HARUS SAMA dengan controller
                        name: 'unit_price', // HARUS SAMA dengan controller
                        orderable: false, // Matikan sort karena ini kolom custom
                        searchable: false // Matikan search bawaan (karena sudah dihandle custom query)
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

            // Event ketika filter berubah
            $('#filter-category, #filter-unit').change(function() {
                table.draw();
            });
            // Aktifkan Select2
            $('#filter-category').select2({
                placeholder: "-- Semua Kategori --",
                allowClear: true,
                width: '100%'
            });
            $('#filter-unit').select2({
                placeholder: "-- Semua Unit --",
                allowClear: true,
                width: '100%'
            });

        });
    </script>
@endpush
