@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Laporan Penjualan</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Laporan Penjualan</li>
        </ul>
    </div>
    <div class="col-sm-5 col">
        <a href="#generate_report" data-toggle="modal" class="btn btn-primary float-right mt-2">Cetak Laporan</a>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">

            @isset($sales)
                <!-- Sales Report -->
                <div class="card">
                    <div class="card-body">

                        <div class="mb-3">
                            <strong>Periode:</strong>
                            {{ request('from_date') ? date('d M Y', strtotime(request('from_date'))) : '-' }} -
                            {{ request('to_date') ? date('d M Y', strtotime(request('to_date'))) : '-' }}
                        </div>

                        <div class="table-responsive">
                            <table id="sales-table" class="datatable table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Penjualan</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Nomor Hp</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Item</th>
                                        <th>Diskon</th>
                                        <th>Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $key => $sale)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y') }}</td>
                                            <td>{{ $sale->customer->nama ?? '-' }}</td>
                                            <td>{{ $sale->customer->telepon ?? '-' }}</td>
                                            <td>{{ $sale->payment_method ?? '-' }}</td>
                                            <td>
                                                <ul>
                                                    @foreach ($sale->saleItems as $item)
                                                        <li>{{ $item->product->name ?? '-' }} ({{ $item->quantity }}x)</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>{{ $sale->discount ?? 0 }}%</td>
                                            <td>{{ AppSettings::get('app_currency', 'Rp') }}
                                                {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /Sales Report -->
            @endisset

        </div>
    </div>

    <!-- Generate Modal -->
    <div class="modal fade" id="generate_report" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cetak Laporan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('sales.report') }}">
                        @csrf
                        <div class="row form-row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Dari Tanggal</label>
                                            <input type="date" name="from_date" class="form-control from_date">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Sampai Tanggal</label>
                                            <input type="date" name="to_date" class="form-control to_date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block submit_report">Tampilkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Generate Modal -->
@endsection

@push('page-js')
    <script>
        $(document).ready(function() {
            $('#sales-table').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'collection',
                    text: 'Ekspor Data',
                    buttons: [{
                            extend: 'pdf',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            }
                        },
                        {
                            extend: 'csv',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            }
                        }
                    ]
                }]
            });
        });
    </script>
@endpush
