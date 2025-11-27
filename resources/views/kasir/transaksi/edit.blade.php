@extends('kasir.layouts.app')

@push('page-css')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
        }

        .card-header {
            background: linear-gradient(135deg, #342af0 0%, #1637dc 100%);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0 !important;
            padding: 1.25rem 1.5rem;
            border: none;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .section-divider {
            border: none;
            height: 2px;
            background: linear-gradient(90deg, #e5e7eb 0%, #d1d5db 50%, #e5e7eb 100%);
            margin: 2rem 0 1.5rem 0;
        }

        .section-title {
            color: #374151;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.625rem 0.75rem;
            font-size: 0.875rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }

        .product-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .product-header .row>div {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
        }

        .btn-add-product {
            background: linear-gradient(135deg, #1728e2 0%, #1913c4 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .btn-add-product:hover {
            background: linear-gradient(135deg, #1728e2 0%, #0c088e 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(11, 73, 173, 0.4);
            color: white;
        }

        .btn-remove-product {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-weight: bold;
            transition: all 0.2s ease;
        }

        .btn-remove-product:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: scale(1.05);
            color: white;
        }

        .summary-section {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #3b82f6;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .summary-title {
            color: #1e40af;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .total-display {
            background: white;
            border: 2px solid #3b82f6;
            border-radius: 0.375rem;
            padding: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e40af;
            text-align: right;
        }

        .btn-submit {
            background: linear-gradient(135deg, #2219d2 0%, #200daf 100%);
            border: none;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 0.875rem 2rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #4338ca 0%, #0f119b 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
            color: white;
        }

        .required-asterisk {
            color: #ef4444;
            font-weight: bold;
        }

        .input-group-text {
            background: #f3f4f6;
            border-color: #d1d5db;
            color: #6b7280;
            font-weight: 600;
        }
    </style>
@endpush

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Edit Transaksi</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kasir.dashboard') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Penjualan</li>
        </ul>
    </div>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('kasir.transaksi.update', $sale->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Informasi Pelanggan -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="section-title"><i class="fas fa-user me-2"
                                        style="margin-right: 10px;"></i>Informasi Pelanggan</h6>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Nomor Invoice</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                                        <input type="text" name="invoice_number" class="form-control"
                                            value="{{ $sale->invoice_number }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Nama Pelanggan <span
                                            class="required-asterisk">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" name="nama_customer"
                                            value="{{ old('nama_customer', $customer->nama) }}" class="form-control"
                                            placeholder="Masukkan nama pelanggan" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Nomor Telepon <span class="required-asterisk">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="text" name="nomor_telepon"
                                            value="{{ old('nomor_telepon', $customer->telepon) }}" class="form-control"
                                            placeholder="Contoh: 0876 5245 8976" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Produk Penjualan -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="section-title">
                                    <i class="fas fa-box me-2" style="margin-right: 10px;"></i>Produk Penjualan
                                </h6>
                            </div>
                        </div>

                        <div id="sale-items-wrapper">
                            @foreach ($sale->saleItems as $index => $item)
                                <div class="sale-items-card sale-items">
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Produk <span
                                                    class="required-asterisk">*</span></label>
                                            <input type="text" name="sale_items[{{ $index }}][product_name]"
                                                class="form-control product-autocomplete" value="{{ $item->product->name }}"
                                                placeholder="Cari Produk" required>
                                            <input type="hidden" name="sale_items[{{ $index }}][product_id]"
                                                class="product-id" value="{{ $item->product_id }}">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Jumlah <span
                                                    class="required-asterisk">*</span></label>
                                            <input type="number" name="sale_items[{{ $index }}][quantity]"
                                                class="form-control quantity" required value="{{ $item->quantity }}"
                                                min="1" placeholder="1">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Harga satuan <span
                                                    class="required-asterisk">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" name="sale_items[{{ $index }}][unit_price]"
                                                    class="form-control price" required min="0"
                                                    value="{{ $item->unit_price }}" placeholder="0">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Subtotal</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control subtotal" placeholder="0"
                                                    name="sale_items[{{ $index }}][total_price]"
                                                    value="{{ $item->total_price }}" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-1 d-flex justify-content-center">
                                            <button type="button" class="btn btn-remove-product remove-product"
                                                title="Hapus produk">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Tombol tambah & total harga -->
                        <div class="row mb-4 mt-4">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary" id="add-product">
                                    <i class="fas fa-plus me-2" style="margin-right: 10px;"></i>Tambah Produk
                                </button>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Jumlah Harga (Total Semua Produk)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control total_price" disabled placeholder="0"
                                            style="font-weight: 600; background: #f8fafc;">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <hr class="section-divider">

                        <!-- Summary Section -->
                        <div class="summary-section">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="summary-title"><i class="fas fa-calculator me-2"
                                            style="margin-right: 10px;"></i>Ringkasan Pembayaran</h6>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Diskon (%)</label>
                                        <div class="input-group">
                                            <input type="number" name="discount" id="discount" class="form-control"
                                                placeholder="0" value="{{ old('discount', (int) $sale->discount) }}"
                                                min="0" max="100" step="any">
                                            <div class="input-group-append">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <small class="text-muted">Masukkan persentase diskon (0-100)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Total Harga</label>
                                        <input type="text" id="final_total_display"
                                            class="form-control total-display total_price" readonly placeholder="Rp 0">

                                        <!-- Hidden input untuk mengirimkan nilai sebenarnya ke controller -->
                                        <input type="hidden" name="total_price" id="final_total"
                                            value="{{ old('total_price', $sale->total_price) }}">
                                    </div>

                                </div>
                            </div>
                        </div>



                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="section-title"><i class="fas fa-user"
                                        style="margin-right: 10px;"></i>Pembayaran</h6>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Metode Pembayaran <span
                                            class="required-asterisk">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                        {{-- <select value="{{ old('payment_method', $sale->payment_method) }}" name="payment_method" class="form-select" required>
                                            <option disabled value="{{ old('payment_method', $sale->payment_method) }}" selected>Pilih Metode Pembayaran</option>
                                            <option value="Cash">Tunai</option>
                                            <option value="Transfer">Transfer Bank</option>
                                            <option value="QRIS">QRIS</option>
                                        </select> --}}
                                        <select name="payment_method" class="form-select" required>
                                            <option disabled
                                                {{ old('payment_method', $sale->payment_method) == null ? 'selected' : '' }}>
                                                Pilih Metode Pembayaran</option>
                                            <option value="Cash"
                                                {{ old('payment_method', $sale->payment_method) == 'Cash' ? 'selected' : '' }}>
                                                Cash</option>
                                            <option value="Transfer"
                                                {{ old('payment_method', $sale->payment_method) == 'Transfer' ? 'selected' : '' }}>
                                                Transfer Bank</option>
                                            <option value="QRIS"
                                                {{ old('payment_method', $sale->payment_method) == 'QRIS' ? 'selected' : '' }}>
                                                QRIS</option>
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label">Status <span class="required-asterisk">*</span></label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="selesai" {{ $sale->status == 'selesai' ? 'selected' : '' }}>Selesai
                                        </option>
                                        <option value="pending" {{ $sale->status == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        {{-- <option value="piutang" {{ $sale->status == 'piutang' ? 'selected' : '' }}>Piutang --}}
                                        </option>
                                        {{-- <option value="dikembalikan" {{ $sale->status == 'dikembalikan' ? 'selected' : '' }}>
                                    Dikembalikan</option> --}}
                                        <option value="dibatalkan" {{ $sale->status == 'dibatalkan' ? 'selected' : '' }}>
                                            Dibatalkan</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-2" style="margin-right: 10px;"></i>Simpan Penjualan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        let index = {{ $sale->saleItems->count() > 0 ? $sale->saleItems->count() : 1 }};

        $(function() {
            $('#nama_customer').on('input', function() {
                $('#telepon_customer').trigger('input');
            });
            $("#nama_customer").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ url('kasir/customer-autocomplete') }}",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    label: item.nama,
                                    value: item.nama,
                                    telepon: item.telepon
                                };
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    $("#telepon_customer").val(ui.item.telepon);
                }
            });

            // Initialize product autocomplete for existing rows
            initProductAutocomplete();
            updateTotalPrice();
        });
        // Initialize product autocomplete function
        function initProductAutocomplete() {
            $('.product-autocomplete').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('kasir.product.autocomplete') }}",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    label: item.name + ' (' + item.available_stock + ' in stock)',
                                    value: item.name,
                                    product_id: item.id,
                                    stock: item.available_stock,
                                    price: item.price
                                };
                            }));
                        }
                    });
                },
                select: function(event, ui) {
                    const row = $(this).closest('.sale-items');
                    row.find('.product-id').val(ui.item.product_id);
                    row.find('.price').val(ui.item.price).trigger('input');
                    row.find('.quantity').trigger('input'); // Trigger quantity to check stock
                    row.find('.stock-warning').data('available-stock', ui.item.stock);
                    checkStock(row); // Initial stock check
                },
                change: function(event, ui) {
                    // Clear product id and price if no valid product is selected
                    if (!ui.item) {
                        const row = $(this).closest('.sale-items');
                        row.find('.product-id').val('');
                        row.find('.price').val(0).trigger('input');
                        row.find('.stock-warning').addClass('d-none');
                    }
                }
            });
        }

        // Add product
        $('#add-product').click(function() {
            let html = `
        <div class="card mb-3 p-3 sale-items-card sale-items shadow-sm border-0">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1">Produk <span class="required-asterisk">*</span></label>
                    <input type="text" name="sale_items[${index}][product_name]"
                        class="form-control product-autocomplete" placeholder="Cari Produk" required>
                    <input type="hidden" name="sale_items[${index}][product_id]" class="product-id">
                </div>

                <div class="col-md-2">
                     <small class="text-danger stock-warning d-none" data-available-stock="0">Jumlah lebih dari stok
                                                tersedia</small> <br>
                    <label class="form-label mb-1">Jumlah <span class="required-asterisk">*</span></label>
                    <input type="number" name="sale_items[${index}][quantity]" class="form-control quantity" required value="1" min="1" placeholder="masukkan jumlah">
                </div>

                <div class="col-md-2">
                    <label class="form-label mb-1">Harga Satuan <span class="required-asterisk">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="sale_items[${index}][unit_price]" class="form-control price" required min="0" placeholder="0">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label mb-1">Subtotal</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="sale_items[${index}][total_price]" class="form-control subtotal" placeholder="0" readonly>
                    </div>
                </div>

                <div class="col-md-2 d-flex justify-content-center">
                    <button type="button" class="btn btn-outline-danger remove-product" title="Hapus produk">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>`;

            $('#sale-items-wrapper').append(html);
            initProductAutocomplete(); // Initialize autocomplete for the new row
            index++;
            updateTotalPrice(); // Update total price after adding a new row
        });

        // Remove product
        $(document).on('click', '.remove-product', function() {
            $(this).closest('.sale-items').remove();
            updateTotalPrice();
        });

        // Check stock and update subtotal when quantity or price changes
        $(document).on('input', '.quantity, .price', function() {
            const row = $(this).closest('.sale-items');
            checkStock(row);
            updateSubtotal(row);
            updateTotalPrice();
        });

        function checkStock(row) {
            const availableStock = parseInt(row.find('.stock-warning').data('available-stock')) || 0;
            const quantity = parseInt(row.find('.quantity').val()) || 0;
            const stockWarningElement = row.find('.stock-warning');

            if (quantity > availableStock && availableStock > 0) {
                stockWarningElement.text(`Jumlah lebih dari stok tersedia (${availableStock} di stok)`).removeClass('d-none');
            } else if (availableStock === 0) {
                stockWarningElement.text('Produk tidak tersedia').removeClass('d-none');
            } else {
                stockWarningElement.addClass('d-none');
            }
        }

        function updateSubtotal(row) {
            const qty = parseFloat(row.find('.quantity').val()) || 0;
            const price = parseFloat(row.find('.price').val()) || 0;
            const subtotal = qty * price;
            row.find('.subtotal').val(subtotal);
        }

        // Calculate total of all subtotals
        function updateTotalPrice() {
            let total = 0;
            $('.subtotal').each(function() {
                const val = parseFloat($(this).val()) || 0;
                total += val;
            });
            $('.form-control.total_price').val(total);
            updateFinalTotal();
        }


        function updateFinalTotal() {
            let total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            let discount = parseFloat($('#discount').val()) || 0;
            if (discount < 0) discount = 0;
            if (discount > 100) discount = 100;

            let discountedTotal = total - (discount / 100 * total);

            // Update tampilan
            $('.form-control.total_price').val(total); // input total di atas diskon
            $('#final_total_display').val('Rp ' + discountedTotal.toLocaleString('id-ID'));
            $('#final_total').val(discountedTotal);
        }

        // Trigger saat diskon berubah
        $(document).on('input', '#discount', function() {
            updateFinalTotal();
        });

        // Jalankan fungsi saat kuantitas atau harga berubah
        $(document).on('input', '.quantity, .price', function() {
            const row = $(this).closest('.sale-items');
            const qty = parseFloat(row.find('.quantity').val()) || 0;
            const price = parseFloat(row.find('.price').val()) || 0;
            const subtotal = qty * price;
            row.find('.subtotal').val(subtotal);
            updateTotalPrice();
        });
    </script>
@endpush
