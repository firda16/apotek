@extends('admin.layouts.app')

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Edit Pembelian</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Edit Pembelian</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body custom-edit-service">

                    <form method="post" enctype="multipart/form-data" autocomplete="off"
                        action="{{ route('purchases.update', $purchase) }}">
                        @csrf
                        @method('PUT')

                        <div class="service-fields mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Pemasok <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="supplier_id" required>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Metode Pembayaran <span class="text-danger">*</span></label>
                                        <select class="form-control" name="payment_method" required>
                                            <option value="Tunai"
                                                {{ $purchase->payment_method == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                            <option value="Transfer"
                                                {{ $purchase->payment_method == 'Transfer' ? 'selected' : '' }}>Transfer
                                            </option>
                                            <option value="QRIS"
                                                {{ $purchase->payment_method == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3">Produk yang Dibeli</h5>

                        @foreach ($purchase->purchaseItems as $index => $item)
                            <div class="border p-3 mb-3 rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Produk</label>
                                        <select name="products[{{ $index }}][product_id]"
                                            class="form-control select2" required>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Jumlah</label>
                                        <input type="number" name="products[{{ $index }}][quantity]"
                                            class="form-control" value="{{ $item->quantity }}" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Harga Beli</label>
                                        <input type="number" name="products[{{ $index }}][unit_price]"
                                            class="form-control" value="{{ $item->unit_price }}" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Tanggal Kedaluwarsa</label>
                                        <input type="date" name="products[{{ $index }}][expiry_date]"
                                            class="form-control" value="{{ $item->expiry_date }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit">Simpan Perubahan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
@endpush
