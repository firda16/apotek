@extends('kasir.layouts.app')

<x-assets.datatables />

@push('page-css')
    <style>
        .badge {
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
        }

        .bg-secondary {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .btn.active {
            background-color: #007bff !important;
            /* Contoh warna latar belakang */
            color: white !important;
            /* Contoh warna teks */
            border-color: #007bff !important;
            /* Contoh warna border */
        }

        .btn-outline-warning.active {
            background-color: #ffc107 !important;
            color: black !important;
        }

        .btn-outline-danger.active {
            background-color: #dc3545 !important;
            color: white !important;
        }

        .btn-outline-secondary.active {
            background-color: #6c757d !important;
            color: white !important;
        }
    </style>
@endpush

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Produk Kadaluwarsa</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kasir.products.index') }}">Produk</a></li>
            <li class="breadcrumb-item active">Kadaluwarsa</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex justify-content-between mb-3">
                <h5>Produk Mendekati & Sudah Kadaluwarsa</h5>
                <div class="mb-3">
                    <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="Akan Kadaluarsa">Akan
                        Kadaluwarsa</button>
                    <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="Sudah Kadaluarsa">Sudah
                        Kadaluwarsa</button>
                    <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="All">Tampilkan Semua</button>
                </div>


            </div>
            <!-- Produk Kedaluwarsa -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="expired-product"
                            class="datatable table table-striped table-bordered table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Merek</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Kedaluwarsa</th>
                                    <th>Status</th> <!-- Kolom baru -->
                                    {{-- <th class="action-btn">Aksi</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
            <!-- /Produk Kedaluwarsa -->
            <div class="mt-3">
                <form method="POST" action="{{ route('products.deleteExpired') }}"
                    onsubmit="return confirm('Hapus semua produk kadaluarsa?')">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> Hapus Semua Produk Kadaluarsa
                    </button>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('page-js')
    <script>
        $(function() {
            var table = $('#expired-product').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('kasir.products.kadaluarsa.datatable') }}',
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
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'expiry_date',
                        name: 'expiry_date'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Berikan kelas 'active' pada tombol 'Tampilkan Semua' secara default
            $('[data-filter="All"]').addClass('active');

            // Filter tabel berdasarkan tombol
            $('.filter-btn').on('click', function() {
                var filterValue = $(this).data('filter');
                var statusColumnIndex = 6;

                // Hapus kelas 'active' dari semua tombol filter
                $('.filter-btn').removeClass('active');

                // Tambahkan kelas 'active' pada tombol yang baru saja diklik
                $(this).addClass('active');

                if (filterValue === 'All') {
                    table.columns(statusColumnIndex).search('').draw();
                } else {
                    table.columns(statusColumnIndex).search(filterValue).draw();
                }
            });
        });
    </script>
@endpush
