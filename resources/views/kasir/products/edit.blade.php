@extends('admin.layouts.app')

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Edit Produk</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Edit Produk</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body custom-edit-service">
                    <!-- Form Edit Produk -->
                    <form method="POST" action="{{ route('products.update', $product) }}">
                        @csrf
                        @method('PUT')

                        <div class="service-fields mb-3">
                            <div class="row">
                                <!-- Nama Produk -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Nama Produk <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name', $product->name) }}" required>
                                    </div>
                                </div>

                                <!-- Kategori Bisa Dipilih -->
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="category_id">Kategori <span class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id" class="form-control select2" required>
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="service-fields mb-3">
                            <div class="row">
                                <!-- Satuan -->
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Satuan <span class="text-danger">*</span></label>
                                        <input type="text" name="unit" class="form-control"
                                            value="{{ old('unit', $product->unit) }}" required>
                                    </div>
                                </div>

                                <!-- Harga -->
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Harga Jual (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" name="price" class="form-control"
                                            value="{{ old('price', $product->price) }}">
                                    </div>
                                </div>

                                <!-- Stok -->
                                {{-- <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Stok <span class="text-danger">*</span></label>
                                        <input type="number" name="stock" class="form-control"
                                            value="{{ old('stock', $product->stock) }}" required>
                                    </div>
                                </div> --}}
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="service-fields mb-3">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea class="form-control" name="description">{{ old('description', $product->description) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn" type="submit">Simpan</button>
                        </div>
                    </form>
                    <!-- /Form Edit Produk -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
    <script>
        // Optional: Jika kamu pakai dropdown untuk pilih produk dan ingin update kategori
        $('#productSelect').on('change', function() {
            const selected = $(this).find(':selected');
            const category = selected.data('category') || '-';
            $('#categoryDisplay').val(category);
        });

        // Trigger awal jika data produk sudah diisi
        $('#productSelect').trigger('change');
    </script>
@endpush
