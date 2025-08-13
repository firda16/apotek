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
                            <br>
                            <strong>Metode Pembayaran:</strong>
                            {{ request('payment_method') ?: 'Semua Metode' }}
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
                                            <td>{{ \Carbon\Carbon::parse($sale->created_at)->translatedFormat('l, d F Y') }}
                                            </td>
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
                                            <input type="date" name="from_date" class="form-control from_date"
                                                value="{{ request('from_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Sampai Tanggal</label>
                                            <input type="date" name="to_date" class="form-control to_date"
                                                value="{{ request('to_date') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Filter Metode Pembayaran --}}
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Metode Pembayaran</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="">Semua Metode</option>
                                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>
                                            Cash</option>
                                        <option value="transfer"
                                            {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer
                                        </option>
                                        <option value="qris" {{ request('payment_method') == 'qris' ? 'selected' : '' }}>
                                            QRIS</option>
                                        {{-- Tambah metode lain jika ada --}}
                                    </select>
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
                            extend: 'pdfHtml5',
                            title: 'LAPORAN PENJUALAN',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            },
                            customize: function(doc) {
                                // Styling tabel
                                doc.styles.tableHeader.fontSize = 10;
                                doc.styles.tableHeader.bold = true;
                                doc.styles.tableHeader.alignment = 'center';
                                doc.styles.tableBodyOdd.alignment = 'center';
                                doc.styles.tableBodyEven.alignment = 'center';

                                // Margin halaman
                                doc.pageMargins = [40, 60, 40, 40];
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            title: 'Laporan Penjualan',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            title: 'Laporan Penjualan',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            }
                        },
                        {
                            extend: 'print',
                            title: 'Laporan Penjualan',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            },
                            customize: function(win) {
                                $(win.document.body)
                                    .css('font-size', '10pt')
                                    .prepend(
                                        '<h3 style="text-align:center; margin-bottom:20px;">LAPORAN PENJUALAN</h3>'
                                    );
                                $(win.document.body).find('table')
                                    .addClass('table table-bordered')
                                    .css('font-size', 'inherit');
                            }
                        }
                    ]
                }]
            });
        });
    </script>
@endpush
