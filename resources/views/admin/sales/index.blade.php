@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')

@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">Sales</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Sales</li>
	</ul>
</div>
<div class="col-sm-5 col">
	<a href="{{route('sales.create')}}" class="btn btn-primary float-right mt-2">Add Sale</a>
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
								<th>Total Harga</th>
								<th>Tanggal</th>
								<th class="action-btn">Aksi</th>
							</tr>
						</thead>
						<tbody>
    @foreach ($sales as $sale)
    <tr>
        <td>{{ $loop->iteration }}</td>
		<td>{{ $sale->product->purchase->product ?? "-"  }}</td>
        <td>{{ $sale->product->purchase->category->name ?? '-' }}</td>

        {{-- Menampilkan kuantitas --}}
        <td>{{ $sale->quantity }}</td>

        {{-- Menggunakan total_price (sesuai model Sale) --}}
        <td>Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>

        {{-- Mengakses expiry_date melalui relasi purchase --}}
		<td>{{ date_format(date_create($sale->created_at),'d M, Y') }}</td>
        <td>
            <a href="{{ route('sales.edit', $sale->id) }}" class="editbtn">
    <button class="btn btn-primary"><i class="fas fa-edit"></i></button>
</a>

            {{-- <a href="{{ route('sales.destroy', $sale->id) }}" data-id="{{ $sale->id }}" class="deletebtn">
                <button class="btn btn-danger"><i class="fas fa-trash"></i></button>
            </a> --}}
			<form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="id" value="{{ $sale->id }}">
    <button class="btn btn-danger"><i class="fas fa-trash"></i></button>
</form>

        </td>
        {{-- ... kode lainnya ... --}}
    </tr>
    @endforeach
</tbody>
					</table>
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
