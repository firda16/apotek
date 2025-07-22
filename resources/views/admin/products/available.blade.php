@extends('admin.layouts.app')

{{-- <x-assets.datatables /> --}}

@push('page-css')
@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
	<h3 class="page-title">Stok Produk yang tersedia</h3>
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
					<table id="product-table" class="table table-hover table-center mb-0">
						<thead>
							<tr>
								<th>No</th>
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
    {{-- Ingat: $product di sini adalah objek Purchase --}}
    @foreach($products as $product)
    <tr>
		<td>{{ $products->firstItem() + $loop->index }}</td>

        {{-- Nama produk dari field 'product' pada model Purchase --}}
        <td>{{ $product->purchase->product ?? '-' }}</td> 

        {{-- Nama kategori dari relasi 'category' pada model Purchase --}}
        <td>{{ $product->purchase->category->name ?? '-' }}</td> 

        {{-- Harga dari relasi 'purchaseProduct' (yang merupakan objek Product) --}}
        {{-- <td>{{ settings('app_currency','Rp').' '. $product->price }}</td> --}}
        <td class="text-center">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
        {{-- Quantity dari field 'quantity' pada model Purchase --}}
        <td>{{ $product->purchase->quantity ?? '0' }}</td> 

        {{-- Diskon dari relasi 'purchaseProduct' (yang merupakan objek Product) --}}
        <td>{{ ($product->discount ?? '0') }}%</td> 

        {{-- Tanggal kadaluarsa dari field 'expiry_date' pada model Purchase --}}        
		<td>{{ date_format(date_create($product->purchase->expiry_date),'d M, Y') }}</td>
        <td>
            {{-- Tombol Edit: link ke produk yang berelasi --}}
            <a href="{{ route('products.edit', $product->id ?? '#') }}" class="btn btn-sm btn-primary">Edit</a>
            {{-- Tombol Hapus: link ke produk yang berelasi --}}
            <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                    @csrf {{-- Wajib untuk CSRF Protection Laravel --}}
                    @method('DELETE') {{-- Method Spoofing untuk Laravel --}}
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.');">Hapus</button>
                </form>
        </td>
    </tr>
    @endforeach
</tbody>
					</table>
				</div>
                {{-- Pagination --}}
                <div class="mt-3">                        
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
			</div>
		</div>
		<!-- /Daftar Produk -->

	</div>
</div>
@endsection

{{-- @push('page-js')
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
@endpush --}}
