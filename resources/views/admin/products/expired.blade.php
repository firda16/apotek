@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
@endpush

@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Produk Kedaluwarsa</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('products.index')}}">Produk</a></li>
		<li class="breadcrumb-item active">Kedaluwarsa</li>
	</ul>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-md-12">

		<!-- Produk Kedaluwarsa -->
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table id="expired-product" class="datatable table table-striped table-bordered table-hover table-center mb-0">
						<thead>
							<tr>
								<th>Nama Merek</th>
								<th>Kategori</th>
								<th>Harga</th>
								<th>Jumlah</th>
								<th>Diskon</th>
								<th>Tanggal Kedaluwarsa</th>
								<th class="action-btn">Aksi</th>
							</tr>
						</thead>
						<tbody>
							{{-- Data akan diisi oleh DataTables --}}
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- /Produk Kedaluwarsa -->

	</div>
</div>
@endsection

@push('page-js')
<script>
    $(document).ready(function() {
        var table = $('#expired-product').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('expired')}}",
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
