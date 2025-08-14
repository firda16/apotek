@extends('admin.layouts.app')
{{-- <x-assets.datatables /> --}}

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Laporan Stok</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kasir.dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Laporan Stok</li>
        </ul>
    </div>
    <div class="col-sm-5 col">
        <a href="#generate_report" data-toggle="modal" class="btn btn-primary float-right mt-2">Pilih Periode</a>
    </div>
@endpush

@section('content')
    @if (request('from_date') && request('to_date'))
        <div class="card">
            <div class="card-body">

                {{-- Periode --}}
                <div class="mb-3">
                    <strong>Periode:</strong>
                    {{ date('d M Y', strtotime(request('from_date'))) }} -
                    {{ date('d M Y', strtotime(request('to_date'))) }}
                </div>

                {{-- Ringkasan Cepat --}}
                <div class="row text-center mb-4">
                    <div class="col-md-4 mb-2">
                        <div class="card bg-light border">
                            <div class="card-body">
                                <h6>Total Stok Saat Ini</h6>
                                <h4>{{ $currentStock->sum('available_stock') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card bg-light border">
                            <div class="card-body">
                                <h6>Total Stok Masuk</h6>
                                <h4>{{ $stockInSummary->sum('total_quantity') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card bg-light border">
                            <div class="card-body">
                                <h6>Total Stok Keluar</h6>
                                <h4>{{ $stockOutSummary->sum('total_quantity') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab Navigasi --}}
                <ul class="nav nav-tabs" id="stockTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="current-tab" data-toggle="tab" href="#current" role="tab">
                            <iconify-icon icon="mdi:package-variant" class="mr-1"></iconify-icon> Stok Saat Ini
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="in-tab" data-toggle="tab" href="#in" role="tab">
                            <iconify-icon icon="mdi:tray-arrow-down" class="mr-1"></iconify-icon> Stok Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="out-tab" data-toggle="tab" href="#out" role="tab">
                            <iconify-icon icon="mdi:tray-arrow-up" class="mr-1"></iconify-icon> Stok Keluar
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-3">
                    {{-- Tab Stok Saat Ini --}}
                    <div class="tab-pane fade show active" id="current" role="tabpanel">
                        <div class="table-responsive">
                            <table id="current-stock-table" class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Stok Tersedia</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($currentStock as $i => $row)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $row['product_name'] }}</td>
                                            <td>{{ $row['category_name'] }}</td>
                                            <td>{{ $row['available_stock'] }}</td>
                                            <td>{{ $row['unit'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab Stok Masuk --}}
                    <div class="tab-pane fade" id="in" role="tabpanel">
                        <div class="table-responsive">
                            <table id="stock-in-table" class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Jumlah Masuk</th>
                                        <th>Satuan</th>
                                        <th>Tgl Expired</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockIn as $i => $item)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $item->purchase->created_at->format('d M Y') }}</td>
                                            <td>{{ $item->product->name ?? '-' }}</td>
                                            <td>{{ $item->product->category->name ?? '-' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->product->unit ?? '-' }}</td>
                                            <td>{{ $item->expiry_date ? date('d M Y', strtotime($item->expiry_date)) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab Stok Keluar --}}
                    <div class="tab-pane fade" id="out" role="tabpanel">
                        <div class="table-responsive">
                            <table id="stock-out-table" class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Jumlah Keluar</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockOut as $i => $item)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $item->sale->created_at->format('d M Y') }}</td>
                                            <td>{{ $item->product->name ?? '-' }}</td>
                                            <td>{{ $item->product->category->name ?? '-' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->product->unit ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- Modal Pilih Periode --}}
    <div class="modal fade" id="generate_report">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Periode</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form method="get" action="{{ route('reports.stock') }}">
                        <div class="row">
                            <div class="col-6">
                                <label>Dari Tanggal</label>
                                <input type="date" name="from_date" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label>Sampai Tanggal</label>
                                <input type="date" name="to_date" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block mt-3">Tampilkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#current-stock-table, #stock-in-table, #stock-out-table').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'collection',
                text: 'Ekspor Data',
                className: 'btn btn-sm btn-primary dropdown-toggle',
                buttons: [
                    'pdfHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'print'
                ]
            }
        ]
    });
});
</script>
@endpush
