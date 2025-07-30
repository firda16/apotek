@extends('admin.layouts.app')

@push('page-css')
@endpush

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Tambah Produk Baru</h3>
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
                    <!-- Form Tambah Produk Baru -->
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf
                        <div class="service-fields mb-3">
                            <div class="row">
                                <!-- Nama Produk -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Nama Produk <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name') }}" required>
                                    </div>
                                </div>

                                <!-- Pilih Kategori -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Kategori <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-control select2" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Harga dan Stok -->
                        <div class="service-fields mb-3">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Harga Jual (Rp) <span class="text-danger">*</span></label>
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

                        <!-- Diskon dan Deskripsi -->
                        <div class="service-fields mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Diskon (%)</label>
                                        <input type="number" name="discount" class="form-control"
                                            value="{{ old('discount') }}">
                                    </div>
                                </div>

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
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endpush
