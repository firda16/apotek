@extends('kasir.layouts.app')

<x-assets.datatables />

@push('page-css')
    <style>
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            color: white;
            text-align: center;
            display: inline-block;
        }

        .status-pending {
            background-color: #ffc107;
            /* Kuning untuk Pending */
        }

        .status-selesai {
            background-color: #28a745;
            /* Hijau untuk Selesai */
        }

        .status-dibatalkan {
            background-color: #dc3545;
            /* Merah untuk Dibatalkan */
        }
    </style>
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Penjualan</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Penjualan</li>
        </ul>
    </div>
    <div class="col-sm-5 col">
        <a href="{{ route('kasir.transaksi.create') }}" class="btn btn-primary float-right mt-2">Transaksi Baru</a>
    </div>
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">

            <!--  Sales -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="sales-table" class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Penjualan</th>
                                    <th>Nomor Invoice</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Nomor Hp</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Item</th>
                                    <th>Diskon</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    {{-- <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Harga per Produk</th>
                                     --}}
                                    <th class="action-btn">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>

                        </table>
                    </div>
                    {{-- <div class="mt-3">
                        {{ $sales->links('pagination::bootstrap-5') }}
                    </div> --}}
                </div>
            </div>
            <!-- / sales -->

        </div>
    </div>
@endsection

@push('page-js')
    @if (session('invoice_url'))
        <script>
            window.open("{{ session('invoice_url') }}", "_blank");
        </script>
    @endif

@endpush

@push('page-js')
    <script>
        $(document).ready(function() {
            $('#sales-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kasir.transaksi') }}",
                order: [
                    [1, 'desc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_penjualan',
                        name: 'tanggal_penjualan'
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'nama_pelanggan',
                        name: 'nama_pelanggan'
                    },
                    {
                        data: 'nomor_hp',
                        name: 'nomor_hp'
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method'
                    },
                    {
                        data: 'item',
                        name: 'item',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'discount',
                        name: 'discount'
                    },
                    {
                        data: 'total_price',
                        name: 'total_price'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false
                    }
                ],
            });
        });
    </script>
@endpush


{{-- @push('page-js')
<script>
    $(document).ready(function() {
        var table = $('#sales-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('sales.index')}}",
            columns: [
                {data: 'product', name: 'product'},
                {data: 'quantity', name: 'quantity'},
                {data: 'total_price', name: 'total_price'},
				{data: 'date', name: 'date'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

    });
</script>
@endpush --}}



{{-- ini sudah benar --}}
