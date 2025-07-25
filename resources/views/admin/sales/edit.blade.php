@extends('admin.layouts.app')

@push('page-css')
@endpush

@push('page-header')
<div class="col-sm-12">
	<h3 class="page-title">Edit Sale</h3>
	<ul class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item active">Edit Sale</li>
	</ul>
</div>
@endpush

@section('content')
<div class="row">
	<div class="col-sm-12">
		<div class="card">
			<div class="card-body custom-edit-service">
                <!-- Edit Sale -->
                <form method="POST" action="{{ route('sales.update', $sale) }}">
					@csrf
					@method("PUT")
					<div class="row form-row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Nama Obat <span class="text-danger">*</span></label>
								<select class="select2 form-select form-control edit_product" name="product">
									{{-- @foreach ($sale->saleItems->product as $product)
										@if (!empty($product->quantity > 0))
											<option value="{{ $product->id }}" {{ ($product->id == $sale->product->id) ? 'selected' : '' }}>
												{{ $product->name }}
											</option>
										@endif
									@endforeach --}}
									
									 @foreach ($products as $product)
										{{-- @if (!empty($product->quantity > 0)) --}}
											<option value="{{ $product->id }}" {{ ($product->id == $sale->product_id) ? 'selected' : '' }}>
												{{ $product->name }}
											</option>
										{{-- @endif --}}
										{{-- <option value="{{ $item->product->id }}" {{ ($item->product->id == $item->product_id) ? 'selected' : '' }}>
											{{ $item->product->name }}
										</option> --}}
									@endforeach
									
									

								</select>
							</div>
						</div>

						{{-- <div class="col-md-6">
							<div class="form-group">
								<label>Kategori</label>
								<input type="text" class="form-control" value="{{ $sale->product->purchase->category->name ?? '-' }}" readonly>
							</div>
						</div> --}}

						<div class="col-md-4">
							<div class="form-group">
								<label>Jumlah</label>
								<input type="number" class="form-control edit_quantity" value="{{ $sale->quantity ?? '1' }}" name="quantity">
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label>Satuan</label>
								<input type="text" class="form-control" name="unit" value="{{ $sale->unit ?? 'pcs' }}">
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label>Harga jual</label>
								{{-- <input type="number" class="form-control" name="price_per_product" value="{{ number_format($sale->saleItems->first()->product->price ?? 0, 0, ',', '.') }}"> --}}
								<input type="number" class="form-control" name="price_per_product"
								    value="{{ number_format($sale->saleItems->first()->unit_price ?? 0, 0, ',', '.') }}">


							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Diskon (%)</label>
								<input type="number" class="form-control" name="discount" value="{{ $sale->discount ?? 0 }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Metode Pembayaran</label>
								<select name="metode_pembayaran" class="form-control">
									<option value="tunai" {{ $sale->metode_pembayaran == 'tunai' ? 'selected' : '' }}>Tunai</option>
									<option value="transfer" {{ $sale->metode_pembayaran == 'transfer' ? 'selected' : '' }}>Transfer</option>
									<option value="qris" {{ $sale->metode_pembayaran == 'qris' ? 'selected' : '' }}>QRIS</option>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Tanggal Penjualan</label>
								<input type="date" class="form-control" name="tanggal" value="{{ $sale->created_at->format('Y-m-d') }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Total Harga</label>
								<input type="number" class="form-control" name="total_price" value="{{ $sale->total_price ?? ($sale->quantity * $sale->price_per_product) }}">								
							</div>
						</div>
					</div>

					<button type="submit" class="btn btn-primary btn-block">Save Changes</button>
				</form>
                <!--/ Edit Sale -->
			</div>
		</div>
	</div>
</div>
@endsection

@push('page-js')
@endpush
