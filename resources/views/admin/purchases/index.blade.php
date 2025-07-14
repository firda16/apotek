@extends('admin.layouts.app')

{{-- <x-assets.datatables /> --}}

@push('page-css')
    
@endpush

@push('page-header')
<div class="col-sm-7 col-auto">
    <h3 class="page-title">Purchase</h3>
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active">Purchase</li>
    </ul>
</div>
<div class="col-sm-5 col">
    <a href="{{route('purchases.create')}}" class="btn btn-primary float-right mt-2">Add New</a>
</div>
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
    
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('purchases.index') }}" method="GET">
                    <div class="input-group">
                        {{-- Input field untuk pencarian. 'name="search"' harus sesuai dengan yang digunakan di controller --}}
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari nama obat, kategori, atau supplier..." 
                               {{-- Menjaga nilai pencarian tetap di input setelah pencarian --}}
                               value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Cari</button>
                            
                            {{-- Tombol 'Hapus' hanya muncul jika ada pencarian --}}
                            @if(request('search'))
                                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Hapus Pencarian</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchase-table" class=" table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Medicine Name</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Purchase Cost</th>
                                <th>Quantity</th>
                                <th>Expire Date</th>
                                <th class="action-btn">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembelians as $pembelian)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pembelian->product}}</td>
                                <td>{{ $pembelian->category->name }}</td>
                                <td>{{ $pembelian->supplier->name ?? '-' }}</td>
                                <td>{{ $pembelian->cost_price }}</td>
                                <td>{{ $pembelian->quantity }}</td>
                                <td>{{ $pembelian->expiry_date }}</td>
                                <td>
                                    <a href="{{ route("purchases.edit", $pembelian->id) }}" class="editbtn">
                                        <button class="btn btn-primary"><i class="fas fa-edit"></i></button>
                                    </a>

                                    <form action="{{ route('purchases.destroy', $pembelian->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
</div>
@endsection