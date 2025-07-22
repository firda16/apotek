@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-7 col-auto">
        <h3 class="page-title">Sales</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Sales</li>
        </ul>
    </div>
    <div class="col-sm-5 col">
        <a href="{{ route('sales.create') }}" class="btn btn-primary float-right mt-2">Add Sale</a>
    </div>
@endpush

@section('content')
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
                                    <th>Nama Obat</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Harga per Produk</th>
                                    <th>Diskon</th>
                                    <th>Total Harga</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Tanggal</th>
                                    <th class="action-btn">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ $sales->firstItem() + $loop->index }}</td>
                                        <td>{{ $sale->product->purchase->product ?? '-' }}</td>
                                        <td>{{ $sale->product->purchase->category->name ?? '-' }}</td>
                                        <td>{{ $sale->quantity }}</td>
                                        <td>{{ $sale->unit ?? '-' }}</td>
                                        <td>Rp {{ number_format($sale->price_per_product, 0, ',', '.') }}</td>
                                        <td>{{ $sale->discount ?? 0 }}%</td>
                                        <td>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
                                        <td>{{ $sale->payment_method ?? '-' }}</td>
                                        <td>{{ date('d M, Y', strtotime($sale->created_at)) }}</td>
                                        <td>
                                            <a href="{{ route('sales.edit', $sale->id) }}" class="editbtn">
                                                <button class="btn btn-primary"><i class="fas fa-edit"></i></button>
                                            </a>
                                            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $sales->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
            <!-- / sales -->

        </div>
    </div>
@endsection

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
