@extends('admin.layouts.app')

@push('page-css')
    <style>
        /* Hilangkan spinner di input number */
        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }

        /* Style agar preview gambar rapi di tengah */
        .image-upload-container {
            text-align: center;
            border: 2px dashed #e5e5e5;
            padding: 20px;
            border-radius: 10px;
            background: #f9f9f9;
        }
    </style>
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

                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">

                            <div class="col-lg-4 col-md-12 mb-4">
                                <div class="form-group">
                                    <label class="font-weight-bold">Foto Produk</label>
                                    <div class="image-upload-container">
                                        <img id="img-preview"
                                             src="{{ asset('assets/img/medicine_no_picture.png') }}"
                                             alt="Preview"
                                             class="img-fluid mb-3"
                                             style="max-height: 200px; object-fit: cover; border-radius: 8px;">

                                        <div class="custom-file text-left">
                                            <input type="file" name="image" class="custom-file-input" id="input-image" accept="image/*" onchange="previewImage()">
                                            <label class="custom-file-label" for="input-image">Pilih Gambar...</label>
                                        </div>
                                        <small class="text-muted d-block mt-2 text-left">
                                            * Kosongkan jika ingin menggunakan gambar default.<br>
                                            * Format: JPG, PNG, JPEG.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-12">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Kode Produk (SKU) <span class="text-danger">*</span></label>
                                            <input type="text" name="product_code" class="form-control"
                                                value="{{ old('product_code') }}" placeholder="Cth: MED-001" required>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Nama Produk <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name') }}" placeholder="Masukkan nama produk" required>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kategori <span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-control select2" required>
                                                <option value="">-- Pilih Kategori --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Satuan <span class="text-danger">*</span></label>
                                            <input type="text" name="unit" class="form-control"
                                                value="{{ old('unit') }}" placeholder="Cth: Botol, Strip, Pcs" required>
                                            @error('unit')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Harga Jual (Rp) <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" name="price" class="form-control"
                                                    value="{{ old('price') }}" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Deskripsi</label>
                                            <textarea name="description" class="form-control" rows="3" placeholder="Tambahkan keterangan produk...">{{ old('description') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div> </div> <div class="submit-section text-right mt-3">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary mr-2">Batal</a>
                            <button class="btn btn-primary submit-btn" type="submit">Simpan Produk</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2').select2({
                width: '100%' // Memaksa select2 mengikuti lebar container
            });
        });

        // FUNGSI PREVIEW GAMBAR & Update Label File
        function previewImage() {
            const input = document.getElementById('input-image');
            const preview = document.getElementById('img-preview');
            const label = document.querySelector('.custom-file-label');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                // Update text label dengan nama file
                label.textContent = input.files[0].name;

                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
