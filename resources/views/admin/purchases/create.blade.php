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
                            <div class="form-group">
                                <label class="form-label">Nomor Invoice</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                                    <input type="text" name="invoice_number" class="form-control">
                                </div>
                            </div>
                        </div>


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
                                        <th>Pilih Produk</th>
                                        <th>Jumlah</th>
                                        <th>Harga Beli Satuan</th>
                                        <th>Tanggal Kedaluwarsa</th>
                                        <th>Sub Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="purchase-items">
                                    @if (old('purchase_items'))
                                        @foreach (old('purchase_items') as $index => $item)
                                            <tr>
                                                <td>
                                                    <select name="purchase_items[{{ $index }}][product_id]"
                                                        class="form-control product-select select2">
                                                        <option disabled selected>-- Pilih Produk --</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                {{ $item['product_id'] == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error("purchase_items.{$index}.product_id")
                                                        <div class="text-danger small">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number"
                                                        name="purchase_items[{{ $index }}][quantity]"
                                                        class="form-control purchase-quantity @error("purchase_items.{$index}.quantity") is-invalid @enderror"
                                                        min="1" required
                                                        value="{{ old("purchase_items.{$index}.quantity") }}">
                                                    @error("purchase_items.{$index}.quantity")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01"
                                                        name="purchase_items[{{ $index }}][unit_price]"
                                                        class="form-control purchase-unit-price @error("purchase_items.{$index}.unit_price") is-invalid @enderror"
                                                        required value="{{ old("purchase_items.{$index}.unit_price") }}">
                                                    @error("purchase_items.{$index}.unit_price")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="date"
                                                        name="purchase_items[{{ $index }}][expiry_date]"
                                                        class="form-control @error("purchase_items.{$index}.expiry_date") is-invalid @enderror"
                                                        value="{{ old("purchase_items.{$index}.expiry_date") }}">
                                                    @error("purchase_items.{$index}.expiry_date")
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01"
                                                        name="purchase_items[{{ $index }}][total_price]"
                                                        class="form-control purchase-total_price" readonly
                                                        value="{{ old("purchase_items.{$index}.total_price") }}">
                                                    @error("purchase_items.{$index}.total_price")
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
                                                <select name="purchase_items[0][product_id]"
                                                    class="form-control product-select select2">
                                                    <option value="">-- Pilih Produk --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" name="purchase_items[0][quantity]"
                                                    class="form-control purchase-quantity" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="purchase_items[0][unit_price]"
                                                    class="form-control purchase-unit-price" required>
                                            </td>
                                            <td>
                                                <input type="date" name="purchase_items[0][expiry_date]"
                                                    class="form-control">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="purchase_items[0][total_price]"
                                                    class="form-control purchase-total_price" readonly>
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
                        <div class="mb-3 mt-3">
                            <label for="total_price">Total Harga</label>
                            <input type="number" step="0.01" name="total_price" id="total_price"
                                class="form-control" readonly required>
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

        let i = {{ old('purchase_items') ? count(old('purchase_items')) : 1 }}; // Lanjutkan indeks jika ada old input

        // const productCategoryMap = @json($products->mapWithKeys(fn($p) => [$p->id => $p->category_id]));

        const productsOptions = `
        <option value="">-- Pilih Produk --</option>
        @foreach ($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
        @endforeach
    `;

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
            updateTotalPrice(); // Calculate initial total on page load if old data exists

            // Event listener for product selection to update category
            $(document).on('change', '.product-select', function() {
                const selectedProductId = $(this).val();
                const row = $(this).closest('tr');
                // const categorySelect = row.find('select[name$="[category_id]"]');
                // const categoryId = productCategoryMap[selectedProductId];

                // if (categoryId) {
                //     categorySelect.val(categoryId).trigger('change');
                // } else {
                //     categorySelect.val('');
                // }
            });

            // Event listener for quantity and unit price changes
            $(document).on('input', '.purchase-quantity, .purchase-unit-price', function() {
                calculatetotal_price($(this).closest('tr'));
            });

            // Add new row
            document.getElementById('add-row').addEventListener('click', function() {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                <td>
                    <select name="purchase_items[${i}][product_id]" class="form-control product-select select2">
                        ${productsOptions}
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
            `;
                document.getElementById('purchase-items').appendChild(newRow);
                initializeSelect2();
                i++;
                updateTotalPrice(); // Update total after adding a new row
            });

            // Remove row
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('tr').remove();
                    updateTotalPrice(); // Update total after removing a row
                }
            });

            $(document).on('change', '.product-select', function() {
                const row = $(this).closest('tr');
                const productId = $(this).val();
                const supplierId = $('select[name="supplier_id"]').val();

                if (!supplierId || !productId) return;

                $.ajax({
                    url: '{{ url('/get-last-price') }}',
                    method: 'GET',
                    data: {
                        supplier_id: supplierId,
                        product_id: productId
                    },
                    success: function(res) {
                        if (res.unit_price !== null) {
                            row.find('.purchase-unit-price').val(res.unit_price);
                            calculatetotal_price(row); // hitung ulang subtotal
                        }
                    }
                });
            });

        });
    </script>
@endpush
