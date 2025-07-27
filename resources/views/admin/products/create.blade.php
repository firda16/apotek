@extends('admin.layouts.app')

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Tambah Produk</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Tambah Produk</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body custom-edit-service">
                    <!-- Form Tambah Produk -->
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf
                        <div class="service-fields mb-3">
                            <div class="row">
                                <!-- Pilih Produk (dari purchase/product) -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Nama Produk <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="product_id" id="productSelect" required>
                                            <option disabled selected>Pilih Produk</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-category="{{ $product->category->name ?? '-' }}">
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Kategori Otomatis -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Kategori</label>
                                        <input type="text" class="form-control" id="categoryDisplay" readonly>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Harga dan Stok -->
                        <div class="service-fields mb-3">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Harga Jual (Rp)<span class="text-danger">*</span></label>
                                        <input type="number" name="price" class="form-control"
                                            value="{{ old('price') }}" required>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Stok <span class="text-danger">*</span></label>
                                        <input type="number" name="stock" class="form-control"
                                            value="{{ old('stock') }}" required>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Satuan <span class="text-danger">*</span></label>
                                        <input type="text" name="unit" class="form-control"
                                            value="{{ old('unit') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diskon & Deskripsi -->
                        <div class="service-fields mb-3">
                            <div class="row">

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit">Simpan</button>
                        </div>
                    </form>
                    <!-- /Form Tambah Produk -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
@push('page-js')
<script>
    $(document).ready(function () {
        $('#productSelect').on('change', function () {
            const selected = $(this).find(':selected');
            const category = selected.data('category') || '-';
            $('#categoryDisplay').val(category);
        });

        // trigger sekali di awal kalau sudah dipilih
        $('#productSelect').trigger('change');
    });
</script>
@endpush

@endpush
