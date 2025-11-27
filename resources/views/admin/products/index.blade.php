@extends('admin.layouts.app')

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
        /* Agar gambar di tabel rapi */
        table.dataTable tbody td {
            vertical-align: middle;
        }
    </style>
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Daftar Produk</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Produk</li>
        </ul>
    </div>
    <div class="col-sm-5 col">
        <a href="{{ route('products.create') }}" class="btn btn-primary float-right mt-2 ml-2">
            <i class="fa fa-plus"></i> Tambah
        </a>
        <button type="button" class="btn btn-success float-right mt-2 ml-2" data-toggle="modal" data-target="#modalImport">
            <i class="fa fa-file-excel-o"></i> Import
        </button>
        <form action="{{ route('products.destroyAll') }}" method="POST" class="float-right mt-2">
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

            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <select id="filter-category" class="form-control select2">
                                <option value="">-- Semua Kategori --</option>
                                @foreach (\App\Models\Category::all() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="filter-unit" class="form-control select2">
                                <option value="">-- Semua Satuan --</option>
                                @foreach (\App\Models\Product::select('unit')->distinct()->get() as $u)
                                    <option value="{{ $u->unit }}">{{ $u->unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="product-table" class="table table-striped table-bordered table-hover w-100">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Foto</th> <th>Kode (SKU)</th>       <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Satuan</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data Produk</h5>
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
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
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
        $(document).ready(function() {
            // Init Select2
            $('.select2').select2({ width: '100%' });

            // Init DataTable
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
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'image', name: 'image', orderable: false, searchable: false }, // Kolom Gambar
                    { data: 'product_code', name: 'product_code' }, // Kolom Kode
                    { data: 'name', name: 'name' },
                    { data: 'category', name: 'category' },
                    { data: 'unit', name: 'unit' },
                    { data: 'unit_price', name: 'unit_price', orderable: false, searchable: false },
                    { data: 'price', name: 'price' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[3, 'asc']] // Default sort by Nama Produk
            });

            // Refresh tabel saat filter berubah
            $('#filter-category, #filter-unit').change(function() {
                table.draw();
            });
        });
    </script>
@endpush
