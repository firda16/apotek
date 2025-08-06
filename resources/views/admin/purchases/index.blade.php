@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
    <style>
        /* Spinner animation */

        .dataTables_processing {
            display: none !important;
        }


        /* .dataTables_processing {
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(255, 255, 255, 0.8);
                z-index: 999;
                font-size: 16px;
                color: #333;
                padding: 40px;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 100%;
            } */

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #ccc;
            border-top-color: #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush


@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Pembelian</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Pembelian</li>
        </ul>
    </div>
    <div class="col-sm-5 col">
        <a href="{{ route('purchases.create') }}" class="btn btn-primary float-right mt-2">Tambah Baru</a>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            {{-- Form Pencarian --}}
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('purchases.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari nama obat, kategori, atau supplier..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Cari</button>
                                @if (request('search'))
                                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Hapus
                                        Pencarian</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabel Pembelian --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="purchase-table" class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Pembelian</th>
                                    <th>Pemasok</th>
                                    <th>Pembayaran</th>
                                    <th>Item</th>
                                    <th>Total Harga</th>
                                    {{-- <th>Jumlah Produk</th>
                                    <th>Gambar</th>
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Harga Beli</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Kedaluwarsa</th>                                   --}}
                                    <th class="action-btn">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                        {{-- Pagination --}}
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@push('page-js')
    <script>
        $(document).ready(function() {
            $('#purchase-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('purchases.datatable') }}',
                language: {
                    processing: `<div class="spinner"></div>`
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, // Fix penting
                    {
                        data: 'tanggal',
                        name: 'created_at'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier.name'
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method'
                    },
                    {
                        data: 'items',
                        name: 'items',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'total',
                        name: 'total_price'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

        });
    </script>
@endpush
