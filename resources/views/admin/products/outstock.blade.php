@extends('admin.layouts.app')

<x-assets.datatables />

@push('page-css')
{{-- No specific CSS changes needed here for this refactor --}}
@endpush

@push('page-header')
<div class="col-sm-12">
    <h3 class="page-title">Stok Habis</h3>
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('products.index')}}">Produk</a></li>
        <li class="breadcrumb-item active">Stok Habis</li>
    </ul>
</div>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="outstock-product" class="table table-hover table-center mb-0">
                        <thead>
                            <tr>
								<th>No</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                {{-- <th>Harga</th> --}}
                                <th>Jumlah</th>
                                {{-- <th>Diskon</th> --}}
                                {{-- <th>Kedaluwarsa</th> --}}
                                <th class="action-btn">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>                                
                                <td>{{ $products->firstItem() + $loop->index }}</td>
								<td>{{ $product->name ?? '-' }}</td> 
                                <td>{{ $product->category->name ?? '' }}</td>
                                {{-- <td>{{ settings('app_currency','Rp') }} {{ $product->price }}</td> --}}
                                <td>{{ $product->available_stock }}</td>
                                {{-- <td>{{ $product->discount }}</td> Assuming discount is a direct property of product or related --}}
                                {{-- <td>{{ !empty($product->purchase->expiry_date) ? date_format(date_create($product->purchase->expiry_date),'d M, Y') : '' }}</td> --}}
                                <td>
                                    @php
                                        $editbtn = '<a href="'.route("products.edit", $product->id).'" class="editbtn"><button class="btn btn-primary"><i class="fas fa-edit"></i></button></a>';
                                        $deletebtn = '<a data-id="'.$product->id.'" data-route="'.route('products.destroy', $product->id).'" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';
                                    @endphp
                                    
                                        {!! $editbtn !!} 
                                    
                                    
                                        {!! $deletebtn !!}
                                    
                                </td>
                            </tr>
                            @endforeach
                            @if($products->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada produk stok habis.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">                        
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        </div>
</div>
@endsection

{{-- @push('page-js')
<script>
    $(document).ready(function() {
        // Initialize DataTables without AJAX if data is pre-rendered
        $('#outstock-product').DataTable({
            // You can add other DataTables options here like searching, ordering, etc.
            // but remove 'processing', 'serverSide', and 'ajax' options.
        });
    });
</script>
@endpush --}}