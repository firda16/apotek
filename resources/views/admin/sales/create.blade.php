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
                            <!-- No Antrian -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>No Antrian (Queue Number)</label>
                                    <input type="text" name="queue_number" class="form-control"
                                        placeholder="Contoh: A001" required>
                                </div>
                            </div>

                            <!-- Metode Pembayaran -->
                            <div class="col-md-6">
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

                            <!-- Produk dinamis -->
                            <div class="col-12">
                                <hr>
                                <h5>Produk Penjualan</h5>

                                <div id="product-wrapper">
                                    <div class="row product-item mb-3">
                                        <div class="col-md-3">
                                            <label>Produk</label>
                                            <select name="products[0][product_id]" class="form-control select2" required>
                                                <option disabled selected>Pilih Produk</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-category="{{ $product->category->name ?? '-' }}">
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <div class="col-md-2">
                                                <label>Kategori</label>
                                                <input type="text" class="form-control category" value="" readonly>
                                            </div>

                                        </div>
                                        <div class="col-md-2">
                                            <label>Jumlah</label>
                                            <input type="number" name="products[0][quantity]" class="form-control quantity"
                                                required min="1">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Satuan</label>
                                            <input type="text" name="products[0][unit]" class="form-control"
                                                placeholder="misal: pcs, botol">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Harga per Produk</label>
                                            <input type="number" name="products[0][unit_price]"
                                                class="form-control unit_price" required min="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Subtotal</label>
                                            <input type="number" class="form-control subtotal" disabled>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger remove-product">-</button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary" id="add-product">+ Tambah Produk</button>
                            </div>

                            <!-- Diskon -->
                            <div class="col-md-4 mt-4">
                                <div class="form-group mb-3">
                                    <label>Diskon (%)</label>
                                    <input type="number" name="discount" class="form-control" placeholder="contoh: 10"
                                        min="0" max="100">
                                </div>
                            </div>

                            <div class="col-12 mt-4">
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
    <script>
        let index = 1;

        // Tambah produk
        $('#add-product').click(function() {
            let html = `
        <div class="row product-item mb-3">
            <div class="col-md-3">
                <select name="products[${index}][product_id]" class="form-control select2" required>
                    <option disabled selected>Pilih Produk</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="products[${index}][quantity]" class="form-control quantity" required min="1">
            </div>
            <div class="col-md-2">
                <input type="text" name="products[${index}][unit]" class="form-control" placeholder="Satuan">
            </div>
            <div class="col-md-2">
                <input type="number" name="products[${index}][unit_price]" class="form-control unit_price" required min="0">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control subtotal" disabled>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-product">-</button>
            </div>
        </div>`;
            $('#product-wrapper').append(html);
            index++;
        });

        // Hapus produk
        $(document).on('click', '.remove-product', function() {
            $(this).closest('.product-item').remove();
        });

        // Hitung subtotal otomatis
        $(document).on('input', '.quantity, .unit_price', function() {
            const row = $(this).closest('.product-item');
            const qty = parseFloat(row.find('.quantity').val()) || 0;
            const price = parseFloat(row.find('.unit_price').val()) || 0;
            const subtotal = qty * price;
            row.find('.subtotal').val(subtotal);
        });

        $(document).on('change', 'select[name^="products"]', function() {
            const category = $(this).find(':selected').data('category') || '-';
            $(this).closest('.product-item').find('.category').val(category);
        });
    </script>
@endpush
