@extends('kasir.layouts.app')

@push('page-css')
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
        <h3 class="page-title">Edit Sale</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Tambah Sale</li>
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
                                            <select name="sale_items[{{ $index }}][nama_produk]"
                                                class="form-select select2" required>
                                                <option disabled value="">Pilih Produk</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        {{ $product->available_stock <= 0 && $item->product_id != $product->id ? 'disabled' : '' }}
                                                        data-category="{{ $product->category->name ?? '-' }}"
                                                        data-price="{{ $product->price }}"
                                                        {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }} -
                                                        {{ $product->available_stock > 0 ? $product->available_stock : 'Stok Kosong' }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                                        <option value="pending" {{ $sale->status == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="selesai" {{ $sale->status == 'selesai' ? 'selected' : '' }}>Selesai
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
    <script>
        let index = 1;

        // Tambah produk
        $('#add-product').click(function() {
            let html = `
        <div class="sale-items-card sale-items mt-4">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Produk <span class="required-asterisk">*</span></label>
                    <select name="sale_items[${index}][nama_produk]" class="form-select select2" required>
                        <option disabled selected>Pilih Produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                data-category="{{ $product->category->name ?? '-' }}"
                                data-price="{{ $product->price }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Jumlah <span class="required-asterisk">*</span></label>
                    <input type="number" name="sale_items[${index}][quantity]" class="form-control quantity" required value="1" min="1" placeholder="0">
                </div>            
                <div class="col-md-2">
                    <label class="form-label">Harga satuan <span class="required-asterisk">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" data-category="{{ $product->price }}" name="sale_items[${index}][unit_price]" class="form-control price" min="0" placeholder="0">                                            
                    </div>
                </div>                         
                <div class="col-md-3">
                    <label class="form-label">Subtotal</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="sale_items[${index}][total_price]" class="form-control subtotal" disabled placeholder="0">
                    </div>
                </div>
                <div class="col-md-1 d-flex justify-content-center">
                    <button type="button" class="btn btn-remove-product remove-product" title="Hapus produk">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>`;
            $('#sale-items-wrapper').append(html);
            index++;
        });

        $(document).on('change', 'select[name^="sale_items"]', function() {
            const selected = $(this).find(':selected');
            const price = selected.data('price') || 0;

            const row = $(this).closest('.sale-items');
            row.find('.price').val(price).trigger('input'); // supaya subtotal otomatis update
        });

        // Hapus produk
        $(document).on('click', '.remove-product', function() {
            $(this).closest('.sale-items').remove();
        });

        // Hitung subtotal otomatis
        $(document).on('input', '.quantity, .price', function() {
            const row = $(this).closest('.sale-items');
            const qty = parseFloat(row.find('.quantity').val()) || 0;
            const price = parseFloat(row.find('.price').val()) || 0;
            const subtotal = qty * price;
            row.find('.subtotal').val(subtotal);
        });

        // Hitung total semua subtotal
        function updateTotalPrice() {
            let total = 0;
            $('.subtotal').each(function() {
                const val = parseFloat($(this).val()) || 0;
                total += val;
            });

            // Update tampilan
            $('.form-control.total_price').val(total);
            $('.total-display.total_price').val('Rp ' + total.toLocaleString('id-ID'));
        }

        // Jalankan fungsi saat kuantitas atau harga berubah
        $(document).on('input', '.quantity, .price', function() {
            const row = $(this).closest('.sale-items');
            const qty = parseFloat(row.find('.quantity').val()) || 0;
            const price = parseFloat(row.find('.price').val()) || 0;
            const subtotal = qty * price;
            row.find('.subtotal').val(subtotal);

            updateTotalPrice();
        });

        // Jalankan ulang saat menghapus item
        $(document).on('click', '.remove-product', function() {
            $(this).closest('.sale-items').remove();
            updateTotalPrice();
        });

        // Jalankan juga saat produk dipilih (karena harga bisa berubah)
        $(document).on('change', 'select[name^="sale_items"]', function() {
            const selected = $(this).find(':selected');
            const price = selected.data('price') || 0;
            const row = $(this).closest('.sale-items');
            row.find('.price').val(price).trigger('input'); // Trigger input agar subtotal dan total update
        });


        $(document).on('change', 'select[name^="products"]', function() {
            const category = $(this).find(':selected').data('category') || '-';
            $(this).closest('.sale-items').find('.category').val(category);
        });

        function updateFinalTotal() {
            let total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            let discount = parseFloat($('#discount').val()) || 0;
            if (discount < 0) discount = 0;
            if (discount > 100) discount = 100;

            let discountedTotal = total - (discount / 100 * total);

            // Update tampilan display dan hidden input
            $('#final_total_display').val('Rp ' + discountedTotal.toLocaleString('id-ID'));
            $('#final_total').val(discountedTotal); // <-- ini penting agar bisa ditangkap controller
        }

        // Trigger saat diskon berubah
        $(document).on('input', '#discount', function() {
            updateFinalTotal();
        });

        // Integrasikan ke updateTotalPrice
        function updateTotalPrice() {
            let total = 0;
            $('.subtotal').each(function() {
                const val = parseFloat($(this).val()) || 0;
                total += val;
            });

            $('.form-control.total_price').val(total);
            updateFinalTotal(); // Update total setelah diskon juga
        }
        // Hitung total saat halaman pertama kali dimuat
        $(document).ready(function() {
            updateTotalPrice();
        });
    </script>
@endpush
