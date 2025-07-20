@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')

@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">Laporan Pembelian</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Beranda</a></li>
		<li class="breadcrumb-item active">Buat Laporan Pembelian</li>
	</ul>
</div>
<div class="col-sm-5 col">
	<a href="#generate_report" data-toggle="modal" class="btn btn-primary float-right mt-2">Buat Laporan</a>
</div>
@endpush

@section('content')
    @isset($purchases)
    <div class="row">
        <div class="col-md-12">
            <!-- Laporan Pembelian -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="purchase-table" class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Pemasok</th>
                                    <th>Harga Beli</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal Kedaluwarsa</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($purchases as $purchase)
                                @if(!empty($purchase->supplier) && !empty($purchase->category))
                                <tr>
                                    <td>
                                        <h2 class="table-avatar">
                                            @if(!empty($purchase->image))
                                            <span class="avatar avatar-sm mr-2">
                                                <img class="avatar-img" src="{{asset('storage/purchases/'.$purchase->image)}}" alt="gambar produk">
                                            </span>
                                            @endif
                                            {{$purchase->product}}
                                        </h2>
                                    </td>
                                    <td>{{$purchase->category->name}}</td>
                                    <td>{{$purchase->supplier->name}}</td>
                                    <td>{{ AppSettings::get('app_currency', 'Rp') }}{{ number_format($purchase->price, 0, ',', '.') }}
                                                </td>
                                    <td>{{ number_format($purchase->quantity, 0, ',', '.') }}</td>
                                    <td>{{date_format(date_create($purchase->expiry_date),"d M, Y")}}</td>
                                </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /Laporan Pembelian -->
        </div>
    </div>
    @endisset

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
                    <form method="post" action="{{route('purchases.report')}}">
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
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            }
                        },
                        {
                            extend: 'excel',
                            text: 'Ekspor ke Excel',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            }
                        },
                        {
                            extend: 'csv',
                            text: 'Ekspor ke CSV',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Cetak',
                            exportOptions: {
                                columns: "thead th:not(.action-btn)"
                            }
                        }
                    ]
                }
            ]
        });
    });
</script>
@endpush
