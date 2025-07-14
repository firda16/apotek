@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">Produk</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Beranda</a></li>
		<li class="breadcrumb-item active">Produk</li>
	</ul>
</div>
<div class="col-sm-5 col">
	<a href="{{route('products.create')}}" class="btn btn-primary float-right mt-2">Tambah Produk</a>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-md-12">

		<!-- Daftar Produk -->
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table id="product-table" class="datatable table table-hover table-center mb-0">
						<thead>
							<tr>
								<th>Nama Produk</th>
								<th>Kategori</th>
								<th>Harga</th>
								<th>Jumlah</th>
								<th>Diskon</th>
								<th>Tanggal Kedaluwarsa</th>
								<th class="action-btn">Aksi</th>
							</tr>
						</thead>
						<tbody>
							{{-- Data akan dimuat oleh DataTables --}}
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- /Daftar Produk -->

	</div>
</div>
@endsection

@push('page-js')
<script>
    $(document).ready(function() {
        var table = $('#product-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('products.index')}}",
            columns: [
                {data: 'product', name: 'product'},
                {data: 'category', name: 'category'},
                {data: 'price', name: 'price'},
                {data: 'quantity', name: 'quantity'},
                {data: 'discount', name: 'discount'},
				{data: 'expiry_date', name: 'expiry_date'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
    });
</script>
@endpush
