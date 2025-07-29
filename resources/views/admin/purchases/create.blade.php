@extends('admin.layouts.app')

@push('page-css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Tambah Pembelian</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Tambah Pembelian</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body custom-edit-service">

                    <form method="POST" action="{{ route('purchases.store') }}" enctype="multipart/form-data">
                        @csrf

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label>Pemasok <span class="text-danger">*</span></label>
                            <select class="select2 form-select form-control @error('supplier_id') is-invalid @enderror"
                                name="supplier_id" required>
                                <option value="">-- Pilih Pemasok --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="payment_method" id="payment_method"
                                class="form-control @error('payment_method') is-invalid @enderror" required>
                                <option value="">-- Pilih Metode Pembayaran --</option>
                                <option value="Tunai" {{ old('payment_method') == 'Tunai' ? 'selected' : '' }}>Tunai
                                </option>
                                <option value="Transfer" {{ old('payment_method') == 'Transfer' ? 'selected' : '' }}>
                                    Transfer</option>
                                <option value="QRIS" {{ old('payment_method') == 'QRIS' ? 'selected' : '' }}>QRIS
                                </option>
                                <option value="Ewallet" {{ old('payment_method') == 'Ewallet' ? 'selected' : '' }}>Ewallet
                                </option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <h5 class="mb-3">Produk yang Dibeli</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="purchase-table">
                                <thead>
                                    <tr>
                                        <th>Pilih Produk (Opsional)</th>
                                        {{-- <th>Nama Produk Baru (Jika Tidak Memilih Produk)</th> --}}
                                        <th>Kategori</th>
                                        <th>Jumlah</th>
                                        <th>Harga Beli Satuan</th>
                                        <th>Tanggal Kedaluwarsa</th>
                                        <th>Gambar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="purchase-items">
                                    @if (old('products'))
                                        @foreach (old('products') as $index => $item)
                                            <tr>
                                                <td>
                                                    <select name="products[{{ $index }}][product_id]"
                                                        class="form-control product-select select2">
                                                        <option value="">-- Pilih Produk --</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                {{ $item['product_id'] == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error("products.{$index}.product_id")
                                                        <div class="text-danger small">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <select name="products[{{ $index }}][category_id]"
                                                        class="form-control @error("products.{$index}.category_id") is-invalid @enderror"
                                                        required>
                                                        <option value=""></option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ old("products.{$index}.category_id") == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error("products.{$index}.category_id")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" name="products[{{ $index }}][quantity]"
                                                        class="form-control @error("products.{$index}.quantity") is-invalid @enderror"
                                                        min="1" required
                                                        value="{{ old("products.{$index}.quantity") }}">
                                                    @error("products.{$index}.quantity")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01"
                                                        name="products[{{ $index }}][unit_price]"
                                                        class="form-control @error("products.{$index}.unit_price") is-invalid @enderror"
                                                        required value="{{ old("products.{$index}.unit_price") }}">
                                                    @error("products.{$index}.unit_price")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="date" name="products[{{ $index }}][expiry_date]"
                                                        class="form-control @error("products.{$index}.expiry_date") is-invalid @enderror"
                                                        value="{{ old("products.{$index}.expiry_date") }}">
                                                    @error("products.{$index}.expiry_date")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="file" name="products[{{ $index }}][image]"
                                                        class="form-control @error("products.{$index}.image") is-invalid @enderror"
                                                        accept="image/*">
                                                    @error("products.{$index}.image")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm remove-row">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <select name="products[0][product_id]"
                                                    class="form-control product-select select2">
                                                    <option value="">-- Pilih Produk --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="products[0][category_id]" class="form-control" required>
                                                    <option value=""></option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="products[0][quantity]" class="form-control"
                                                    min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="products[0][unit_price]"
                                                    class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="date" name="products[0][expiry_date]" class="form-control">
                                            </td>
                                            <td>
                                                <input type="file" name="products[0][image]" class="form-control"
                                                    accept="image/*">
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-danger btn-sm remove-row">Hapus</button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-secondary btn-sm" id="add-row">+ Tambah
                                Produk</button>
                        </div>

                        <div class="submit-section mt-4">
                            <button class="btn btn-primary submit-btn" type="submit">Simpan</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function initializeSelect2() {
        $('.select2').select2();
    }

    let i = {{ old('products') ? count(old('products')) : 1 }}; // Lanjutkan indeks jika ada old input

    $(document).on('change', '.product-select', function() {
        const selectedProductId = $(this).val();
        const row = $(this).closest('tr');
        const categorySelect = row.find('select[name$="[category_id]"]');
        const categoryId = productCategoryMap[selectedProductId];

        if (categoryId) {
            categorySelect.val(categoryId).trigger('change');
        } else {
            categorySelect.val('');
        }
    });

    const productCategoryMap = @json($products->mapWithKeys(fn($p) => [$p->id => $p->category_id]));

    const productsOptions = `
        <option value="">-- Pilih Produk --</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    `;

    const categoriesOptions = `
        <option value="">-- Pilih Kategori --</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    `;

    $(document).ready(function () {
        initializeSelect2();
    });

    document.getElementById('add-row').addEventListener('click', function () {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="products[${i}][product_id]" class="form-control product-select select2">
                    ${productsOptions}
                </select>
            </td>
            <td>
                <select name="products[${i}][category_id]" class="form-control" required>
                    ${categoriesOptions}
                </select>
            </td>
            <td>
                <input type="number" name="products[${i}][quantity]" class="form-control" min="1" required>
            </td>
            <td>
                <input type="number" step="0.01" name="products[${i}][unit_price]" class="form-control" required>
            </td>
            <td>
                <input type="date" name="products[${i}][expiry_date]" class="form-control">
            </td>
            <td>
                <input type="file" name="products[${i}][image]" class="form-control" accept="image/*">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
            </td>
        `;
        document.getElementById('purchase-items').appendChild(newRow);
        initializeSelect2();
        i++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
</script>
@endpush
