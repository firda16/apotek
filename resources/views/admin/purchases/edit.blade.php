@extends('admin.layouts.app')

@push('page-css')

@endpush

@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Edit Pembelian</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Beranda</a></li>
		<li class="breadcrumb-item active">Edit Pembelian</li>
	</ul>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-body custom-edit-service">

			<!-- Edit Pembelian -->
			<form method="post" enctype="multipart/form-data" autocomplete="off" action="{{route('purchases.update',$purchase)}}">
				@csrf
				@method("PUT")
				<div class="service-fields mb-3">
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group">
								<label>Nama Obat <span class="text-danger">*</span></label>
								<input class="form-control" type="text" value="{{$purchase->product}}" name="product" >
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label>Kategori <span class="text-danger">*</span></label>
								<select class="select2 form-select form-control" name="category">
									@foreach ($categories as $category)
										<option {{($purchase->category->id == $category->id) ? 'selected': ''}} value="{{$category->id}}">{{$category->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group">
								<label>Pemasok <span class="text-danger">*</span></label>
								<select class="select2 form-select form-control" name="supplier">
									@foreach ($suppliers as $supplier)
										<option @if($purchase->supplier->id == $supplier->id) selected @endif value="{{$supplier->id}}">{{$supplier->name}}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="service-fields mb-3">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Harga Beli <span class="text-danger">*</span></label>								
							<div class="input-group">
    <span class="input-group-text">Rp</span>
	<input class="form-control" type="text" name="cost_price" value="{{ old('cost_price', intval($purchase->cost_price)) }}">
	</div>



							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Jumlah <span class="text-danger">*</span></label>
								<input class="form-control" value="{{$purchase->quantity}}" type="text" name="quantity">
							</div>
						</div>
					</div>
				</div>

				<div class="service-fields mb-3">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Tanggal Kedaluwarsa <span class="text-danger">*</span></label>
								<input class="form-control" value="{{$purchase->expiry_date}}" type="date" name="expiry_date">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Gambar Obat</label>
								<input type="file" name="image" class="form-control">
							</div>
						</div>
					</div>
				</div>

				<div class="submit-section">
					<button class="btn btn-primary submit-btn" type="submit">Simpan Perubahan</button>
				</div>
			</form>
			<!-- /Edit Pembelian -->

			</div>
		</div>
	</div>
</div>
@endsection

@push('page-js')
	<!-- Select2 JS -->
	<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}"></script>
	<script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>
<script>
    new AutoNumeric('#cost_price', {
    digitGroupSeparator: '.',
    decimalCharacter: ',',
    decimalPlaces: 0,
    currencySymbol: 'Rp ',
    currencySymbolPlacement: 'p',
    unformatOnSubmit: true
});

</script>

@endpush
