@extends('admin.layouts.app')

@push('page-css')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
                            <label for="invoice_number">No Invoice <span class="text-danger">*</span></label>
                            <input type="text" name="invoice_number" id="invoice_number"
                                class="form-control @error('invoice_number') is-invalid @enderror"
                                value="{{ old('invoice_number', $purchase->invoice_number) }}" required>
                            <small id="invoice-warning" class="text-danger" style="display:none;">
                                Nomor invoice sudah ada
                            </small>
                            @error('invoice_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label>Pemasok <span class="text-danger">*</span></label>
                            <select class="form-select form-control @error('supplier_id') is-invalid @enderror"
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
                                @foreach (['Cash', 'Transfer', 'QRIS'] as $method)
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
                                                <input type="text"
                                                    name="purchase_items[{{ $index }}][product_name]"
                                                    class="form-control product-autocomplete" placeholder="Cari Produk..."
                                                    value="{{ old("purchase_items.{$index}.product_name", \App\Models\Product::find($item['product_id'])->name ?? '') }}">
                                                <input type="hidden"
                                                    name="purchase_items[{{ $index }}][product_id]"
                                                    class="product-id" value="{{ old("purchase_items.{$index}.product_id", $item['product_id']) }}">
                                            </td>
                                            <td>
                                                <input type="number" name="purchase_items[{{ $index }}][quantity]"
                                                    class="form-control purchase-quantity" min="1" required
                                                    value="{{ $item['quantity'] }}">
                                            </td>
                                            <td>
                                                <input type="number"
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
                                                <input type="number"
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
                            <input type="number" name="total_price" id="total_price" class="form-control"
                                readonly required value="{{ old('total_price', $purchase->total_price) }}">
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="pending"
                                    {{ old('status', $purchase->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="selesai"
                                    {{ old('status', $purchase->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="dibatalkan"
                                    {{ old('status', $purchase->status) == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan
                                </option>

                            </select>
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
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        $(document).ready(function() {
            $('#invoice_number').on('input', function() {
                let invoiceNumber = $(this).val();

                if (invoiceNumber.trim() === '') {
                    $('#invoice-warning').hide();
                    return;
                }

                $.ajax({
                    url: '{{ route('check.invoice') }}',
                    type: 'GET',
                    data: {
                        invoice_number: invoiceNumber
                    },
                    success: function(res) {
                        if (res.exists) {
                            $('#invoice-warning').show();
                        } else {
                            $('#invoice-warning').hide();
                        }
                    }
                });
            });
        });


        let i = {{ old('purchase_items') ? count(old('purchase_items')) : $purchase->purchaseItems->count() }};

        function initializeAutocomplete(row) {
            row.find('.product-autocomplete').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('products.search') }}", // Create this route
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    row.find('.product-id').val(ui.item.id);
                    row.find('.product-autocomplete').val(ui.item.value); // Set the selected value to the input
                    const supplierId = $('select[name="supplier_id"]').val();
                    if (supplierId) {
                        $.ajax({
                            url: '{{ url('/get-last-price') }}',
                            method: 'GET',
                            data: {
                                supplier_id: supplierId,
                                product_id: ui.item.id
                            },
                            success: function(res) {
                                if (res.unit_price !== null) {
                                    row.find('.purchase-unit-price').val(parseInt(res.unit_price));
                                    calculatetotal_price(row);
                                }
                            }
                        });
                    }
                    return false; // Prevent the default behavior of replacing the input's value
                }
            });
        }


        function calculatetotal_price(row) {
            const quantity = parseFloat(row.find('.purchase-quantity').val()) || 0;
            const unitPrice = parseFloat(row.find('.purchase-unit-price').val()) || 0;
            const total_price = quantity * unitPrice;
            row.find('.purchase-total_price').val(total_price.toFixed(0)); // Keep as integer
            updateTotalPrice();
        }

        function updateTotalPrice() {
            let total = 0;
            $('.purchase-total_price').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total_price').val(total.toFixed(0)); // Keep as integer
        }

        $(document).ready(function() {
            // Initialize autocomplete for existing rows
            $('#purchase-items').find('tr').each(function() {
                initializeAutocomplete($(this));
            });
            updateTotalPrice();

            $(document).on('input', '.purchase-quantity, .purchase-unit-price', function() {
                calculatetotal_price($(this).closest('tr'));
            });

            $('#add-row').on('click', function() {
                const newRow = `
                <tr>
                    <td>
                        <input type="text" name="purchase_items[${i}][product_name]" class="form-control product-autocomplete" placeholder="Cari Produk...">
                        <input type="hidden" name="purchase_items[${i}][product_id]" class="product-id">
                    </td>
                    <td>
                        <input type="number" name="purchase_items[${i}][quantity]" class="form-control purchase-quantity" min="1" required>
                    </td>
                    <td>
                        <input type="number" name="purchase_items[${i}][unit_price]" class="form-control purchase-unit-price" required>
                    </td>
                    <td>
                        <input type="date" name="purchase_items[${i}][expiry_date]" class="form-control">
                    </td>
                    <td>
                        <input type="number" name="purchase_items[${i}][total_price]" class="form-control purchase-total_price" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">Hapus</button>
                    </td>
                </tr>`;
                $('#purchase-items').append(newRow);
                initializeAutocomplete($('#purchase-items').find('tr').last());
                i++;
                updateTotalPrice();
            });

            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
                updateTotalPrice();
            });

            $(document).on('change', '.product-id', function() {
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
                            row.find('.purchase-unit-price').val(parseInt(res
                                .unit_price));
                            calculatetotal_price(row);
                        }
                    }
                });
            });
        });
    </script>
@endpush
