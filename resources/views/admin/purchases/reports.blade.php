@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
    <h3 class="page-title">Laporan Pembelian</h3>
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
        <li class="breadcrumb-item active">Laporan Pembelian</li>
    </ul>
</div>
<div class="col-sm-5 col">
    <a href="#generate_report" data-toggle="modal" class="btn btn-primary float-right mt-2">Buat Laporan</a>
</div>
@endpush

@section('content')
@if(isset($pembelians) && count($pembelians))
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <strong>Periode:</strong>
                {{ request('from_date') ? date('d M Y', strtotime(request('from_date'))) : '-' }} -
                {{ request('to_date') ? date('d M Y', strtotime(request('to_date'))) : '-' }}
            </div>

            <div class="table-responsive">
                <table id="purchase-table" class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Pemasok</th>
                            <th>Metode Pembayaran</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                            <th>Harga Satuan</th>
                            <th>Total</th>
                            <th>Tgl Expired</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; $row = 1; @endphp
                        @foreach($pembelians as $pembelian)
                            @foreach($pembelian->purchaseItems as $item)
                                <tr>
                                    <td>{{ $row++ }}</td>
                                    <td>{{ $pembelian->created_at->format('d M Y') }}</td>
                                    <td>{{ $pembelian->supplier->name ?? '-' }}</td>
                                    <td>{{ $pembelian->payment_method ?? '-' }}</td>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td>{{ $item->product->category->name ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->product->unit ?? '-' }}</td>
                                    <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                    <td>{{ $item->expiry_date ? date('d M Y', strtotime($item->expiry_date)) : '-' }}</td>
                                </tr>
                                @php $grandTotal += $item->total_price; @endphp
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="9" class="text-right">Total Keseluruhan</th>
                            <th colspan="2">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@else
   
@endif

<!-- Modal Buat Laporan -->
<div class="modal fade" id="generate_report" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Laporan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('purchases.report') }}">
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
                    <button type="submit" class="btn btn-primary btn-block submit_report">Kirim</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Modal Buat Laporan -->
@endsection

@push('page-js')
<script>
    $(document).ready(function(){
        $('#purchase-table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'collection',
                    text: 'Ekspor Data',
                    buttons: [
                        {
                            extend: 'pdf',
                            text: 'Ekspor ke PDF',
                            exportOptions: { columns: ':visible' }
                        },
                        {
                            extend: 'excel',
                            text: 'Ekspor ke Excel',
                            exportOptions: { columns: ':visible' }
                        },
                        {
                            extend: 'csv',
                            text: 'Ekspor ke CSV',
                            exportOptions: { columns: ':visible' }
                        },
                        {
                            extend: 'print',
                            text: 'Cetak',
                            exportOptions: { columns: ':visible' }
                        }
                    ]
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
            }
        });
    });
</script>
@endpush
