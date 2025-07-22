@extends('admin.layouts.app')


@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Tambah Sale</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Tambah Sale</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body custom-edit-service">
                    <!-- Create Sale -->
                    <form method="POST" action="{{ route('sales.store') }}">
                        @csrf
                        <div class="row">
                            <!-- Kiri -->
                            <div class="col-md-6">
                                <!-- Produk -->
                                <div class="form-group mb-3">
                                    <label>Produk <span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="product" required>
                                        <option disabled selected>Pilih Produk</option>
                                        @foreach ($products as $product)
                                            @if (!empty($product->purchase) && $product->purchase->quantity > 0)
                                                <option value="{{ $product->id }}">{{ $product->purchase->product }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Kategori -->
                                <div class="form-group mb-3">
                                    <label>Kategori <span class="text-danger">*</span></label>
                                    <select class="select2 form-control" name="category" required>
                                        <option disabled selected>Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Jumlah -->
                                <div class="form-group mb-3">
                                    <label>Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" value="1" class="form-control" min="1"
                                        required>
                                </div>

                                <!-- Satuan -->
                                <div class="form-group mb-3">
                                    <label>Satuan</label>
                                    <input type="text" name="unit" class="form-control"
                                        placeholder="misal: strip, botol, tablet">
                                </div>
                            </div>

                            <!-- Kanan -->
                            <div class="col-md-6">
                                <!-- Harga per Produk -->
                                <div class="form-group mb-3">
                                    <label>Harga per Produk <span class="text-danger">*</span></label>
                                    <input type="number" name="price_per_product" class="form-control"
                                        placeholder="contoh: 10000" required>
                                </div>

                                <!-- Diskon -->
                                <div class="form-group mb-3">
                                    <label>Diskon (%)</label>
                                    <input type="number" name="discount" class="form-control" placeholder="contoh: 10"
                                        min="0" max="100">
                                </div>

                                <!-- Metode Pembayaran -->
                                <div class="form-group mb-3">
                                    <label>Metode Pembayaran <span class="text-danger">*</span></label>
                                    <select name="payment_method" class="select2 form-control" required>
                                        <option disabled selected>Pilih Metode</option>
                                        <option value="Tunai">Tunai</option>
                                        <option value="Transfer">Transfer</option>
                                        <option value="QRIS">QRIS</option>
                                        <option value="E-Wallet">E-Wallet</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tombol -->
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100">Simpan Penjualan</button>
                            </div>
                        </div>
                    </form>
                    <!--/ Create Sale -->
                </div>
            </div>
        </div>
    </div>
@endsection


@push('page-js')
@endpush
