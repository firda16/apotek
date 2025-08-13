@extends('kasir.layouts.app')

<x-assets.datatables />

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Kategori</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kasir.dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Kategori</li>
        </ul>
    </div>    
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    {{-- Tabel --}}
                    <div class="table-responsive">
                        <table id="category-table" class="table table-striped table-bordered table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kategori</th>                                                                    
                                </tr>
                            </thead>
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
            ajax: '{{ route('kasir.categories.datatable') }}',
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                }
                // {
                //     data: 'action',
                //     name: 'action',
                //     orderable: false,
                //     searchable: false
                // }
            ],
            order: [
                [1, 'desc']
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
