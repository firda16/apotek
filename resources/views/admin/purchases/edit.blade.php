@extends('admin.layouts.app')

@push('page-css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

                    <form method="POST" action="{{ route('purchases.update', $purchase->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

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
                                        {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
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
                                @foreach (['Tunai', 'Transfer', 'QRIS', 'Ewallet'] as $method)
                                    <option value="{{ $method }}"
                                        {{ old('payment_method', $purchase->payment_method) == $method ? 'selected' : '' }}>
                                        {{ $method }}
                                    </option>
                                @endforeach
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
                                        <th>Pilih Produk</th>
                                        <th>Jumlah</th>
                                        <th>Harga Beli Satuan</th>
                                        <th>Tanggal Kedaluwarsa</th>
                                        <th>Sub Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="purchase-items">
                                    @foreach (old('purchase_items', $purchase->purchaseItems->toArray()) as $index => $item)
                                        <tr>
                                            <td>
                                                <select name="purchase_items[{{ $index }}][product_id]"
                                                    class="form-control product-select select2">
                                                    <option value="">-- Pilih Produk --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            {{ $item['product_id'] == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="purchase_items[{{ $index }}][quantity]"
                                                    class="form-control purchase-quantity" min="1" required
                                                    value="{{ $item['quantity'] }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01"
                                                    name="purchase_items[{{ $index }}][unit_price]"
                                                    class="form-control purchase-unit-price" required
                                                    value="{{ $item['unit_price'] }}">
                                            </td>
                                            <td>
                                                <input type="date"
                                                    name="purchase_items[{{ $index }}][expiry_date]"
                                                    class="form-control" value="{{ $item['expiry_date'] }}">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01"
                                                    name="purchase_items[{{ $index }}][total_price]"
                                                    class="form-control purchase-total_price" readonly
                                                    value="{{ $item['total_price'] }}">
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-danger btn-sm remove-row">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-secondary btn-sm" id="add-row">+ Tambah Produk</button>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="total_price">Total Harga</label>
                            <input type="number" step="0.01" name="total_price" id="total_price" class="form-control"
                                readonly required value="{{ old('total_price', $purchase->total_price) }}">
                        </div>

                        <div class="submit-section mt-4">
                            <button class="btn btn-primary submit-btn" type="submit">Perbarui</button>
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

        let i = {{ old('purchase_items') ? count(old('purchase_items')) : $purchase->purchaseItems->count() }};

        const productCategoryMap = @json($products->mapWithKeys(fn($p) => [$p->id => $p->category_id]));

        const productsOptions = `@foreach ($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach`;

        const categoriesOptions = `@foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach`;

        function calculatetotal_price(row) {
            const quantity = parseFloat(row.find('.purchase-quantity').val()) || 0;
            const unitPrice = parseFloat(row.find('.purchase-unit-price').val()) || 0;
            const total_price = quantity * unitPrice;
            row.find('.purchase-total_price').val(total_price.toFixed(2));
            updateTotalPrice();
        }

        function updateTotalPrice() {
            let total = 0;
            $('.purchase-total_price').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total_price').val(total.toFixed(2));
        }

        $(document).ready(function() {
            initializeSelect2();
            updateTotalPrice();

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

            $(document).on('input', '.purchase-quantity, .purchase-unit-price', function() {
                calculatetotal_price($(this).closest('tr'));
            });

            $('#add-row').on('click', function() {
                const newRow = `
                <tr>
                    <td>
                        <select name="purchase_items[${i}][product_id]" class="form-control product-select select2">
                            ${productsOptions}
                        </select>
                    </td>
                    <td>
                        <select name="purchase_items[${i}][category_id]" class="form-control" required>
                            ${categoriesOptions}
                        </select>
                    </td>
                    <td>
                        <input type="number" name="purchase_items[${i}][quantity]" class="form-control purchase-quantity" min="1" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="purchase_items[${i}][unit_price]" class="form-control purchase-unit-price" required>
                    </td>
                    <td>
                        <input type="date" name="purchase_items[${i}][expiry_date]" class="form-control">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="purchase_items[${i}][total_price]" class="form-control purchase-total_price" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                    </td>
                </tr>`;
                $('#purchase-items').append(newRow);
                initializeSelect2();
                i++;
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                updateTotalPrice();
            });
        });
    </script>
@endpush
