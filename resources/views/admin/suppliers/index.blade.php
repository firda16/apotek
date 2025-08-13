@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')

@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
    <h3 class="page-title">Pemasok</h3>
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Beranda</a></li>
        <li class="breadcrumb-item active">Pemasok</li>
    </ul>
</div>
<div class="col-sm-5 col">
    <a href="{{route('suppliers.create')}}" class="btn btn-primary float-right mt-2">Tambah Baru</a>
</div>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="supplier-table" class="datatable table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Email</th>
                                <th>Alamat</th>
                                <th>Perusahaan</th>
                                <th class="action-btn">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan dimuat di sini oleh DataTables melalui AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
</div>

@endsection

@push('page-js')
<script>
    $(document).ready(function() {
        $('#supplier-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('suppliers.index') }}",
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
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'company',
                    name: 'company'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            // Untuk menghilangkan paginasi bawaan Blade
            "paging": true
        });
    });
</script>
@endpush