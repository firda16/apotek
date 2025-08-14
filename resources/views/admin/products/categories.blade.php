@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
    <style>
        table.dataTable td {
            vertical-align: middle !important;
        }
    </style>
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Kategori</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Kategori</li>
        </ul>
    </div>
    <div class="col-sm-5 col">
        <a href="#add_categories" data-toggle="modal" class="btn btn-primary float-right mt-2">Tambah Kategori</a>
    </div>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('edit_success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('edit_success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if ($errors->edit && $errors->edit->has('name'))
        <div class="alert alert-danger mt-2">
            {{ $errors->edit->first('name') }}
        </div>
    @endif
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    {{-- Form Pencarian --}}
                    {{-- <form method="GET" action="{{ route('categories.index') }}" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari kategori..."
                                value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Cari</button>
                                @if (request('search'))
                                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Reset</a>
                                @endif
                            </div>
                        </div>
                    </form> --}}

                    {{-- Tabel --}}
                    {{-- filepath: d:\magang\apotek\resources\views\admin\products\categories.blade.php --}}
                    <div class="table-responsive">
                        <table id="category-table" class="table table-striped table-bordered table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
                                    <th class="text-center action-btn">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    {{-- <div class="mt-3">
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div class="modal fade" id="add_categories" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>Nama Kategori</label>
                            <input type="text" name="name" class="form-control" required>
                            @error('name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="edit_category" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('categories.update') }}">
                        @csrf
                        <input type="hidden" name="id" id="edit_id">
                        <div class="form-group">
                            <label>Nama Kategori</label>
                            <input type="text" class="form-control edit_name" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
    <script>
        $(document).ready(function() {
            $('#category-table').on('click', '.editbtn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                $('#edit_id').val(id);
                $('.edit_name').val(name);

                $('#edit_category').modal('show');
            });
        });

        $('#category-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('categories.datatable') }}',
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
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [2, 'desc']
            ]
        });

        @if ($errors->has('name'))
            $('#add_categories').modal('show');
        @endif
        @if (session('edit_success') || (isset($errors->edit) && $errors->edit->has('name')))
            $('#edit_category').modal('show');
        @endif
    </script>
@endpush
