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
        <h3 class="page-title">Transaksi Baru</h3>
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
                    <form method="POST" action="{{ route('kasir.transaksi.store') }}">
                        @csrf

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
                                            value="{{ $invoice_number }}" readonly>
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
                                        <input type="text" name="nama_customer" id="nama_customer" class="form-control"
                                            placeholder="Masukkan nama pelanggan" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Nomor Telepon <span class="required-asterisk">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="number" id="telepon_customer" name="nomor_telepon"
                                            class="form-control" placeholder="Contoh: 0876 5245 8976" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Item produk --}}
                        <div class="" id="item-produk">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="section-title"><i class="fas fa-box me-2"
                                            style="margin-right: 10px;"></i>Produk
                                        Penjualan</h6>
                                </div>
                            </div>

                            <div id="sale-items-wrapper">
                                <div class="card p-3 sale-items-card sale-items shadow-sm border-0">
                                    <div class="row align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label mb-1">Produk <span
                                                    class="required-asterisk">*</span></label>
                                            <select name="sale_items[0][nama_produk]" class="form-select select-product"
                                                required>
                                                <option disabled selected>Pilih Produk</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-stock="{{ $product->available_stock }}"
                                                        data-price="{{ $product->price }}"
                                                        {{ $product->available_stock <= 0 ? 'disabled' : '' }}>
                                                        {{ $product->name }} -
                                                        {{ $product->available_stock > 0 ? $product->available_stock : 'Stok Kosong' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <small class="text-danger stock-warning d-none">Jumlah lebih dari stok
                                                tersedia</small> <br>
                                            <label class="form-label mb-1">Jumlah <span
                                                    class="required-asterisk">*</span></label>
                                            <input type="number" name="sale_items[0][quantity]"
                                                class="form-control quantity" required value="1" min="1"
                                                placeholder="masukkan jumlah">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label mb-1">Harga Satuan <span
                                                    class="required-asterisk">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" name="sale_items[0][unit_price]"
                                                    class="form-control price" required min="0" placeholder="0">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label mb-1">Subtotal</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control subtotal" placeholder="0"
                                                    name="sale_items[0][total_price]" readonly>
                                            </div>
                                        </div>

                                        <div class="col-md-2 justify-content-center">
                                            <button type="button" class="btn btn-outline-danger remove-product"
                                                title="Hapus produk">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>



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
                                            <input type="number" class="form-control total_price" disabled
                                                placeholder="0" style="font-weight: 600; background: #f8fafc;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="section-divider">

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
                                                placeholder="0" min="0" max="100">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <small class="text-muted">Masukkan persentase diskon (0-100)</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Total Harga</label>
                                        <input type="text" name="total_price" id="final_total"
                                            class="form-control total-display total_price" disabled placeholder="Rp 0">
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
                                        <select name="payment_method" class="form-select" required>
                                            <option disabled selected>Pilih Metode Pembayaran</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Transfer">Transfer Bank</option>
                                            <option value="QRIS">QRIS</option>
                                            {{-- <option value="E-Wallet">E-Wallet</option> --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-2">
                                <label class="form-label">Status <span class="required-asterisk">*</span></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="pending" selected>Pending</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                        </div>



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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script>
        let index = 1;
        $(function() {
            $('#telepon_customer').on('input', function() {
                let phone = $(this).val();
                let name = $('#nama_customer').val();
                if (phone.length > 0 && name.length > 0) {
                    $.ajax({
                        url: "{{ url('customer-check-phone') }}",
                        data: {
                            phone: phone,
                            name: name
                        },
                        success: function(res) {
                            $('#phone-warning').remove();
                            if (res.exists) {
                                $('#telepon_customer').after(
                                    '<div id="phone-warning" class="text-danger mt-1">Nomor telepon ini sudah terdaftar atas nama <strong>' +
                                    res.real_name +
                                    '</strong>. Silakan cek kembali nama pelanggan!</div>'
                                );
                            }
                        }
                    });
                } else {
                    $('#phone-warning').remove();
                }
            });
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

            // Panggil fungsi ini saat halaman dimuat
            updateProductOptions();
        });

        // Tambah produk
        $('#add-product').click(function() {
            let html = `
        <div class="card mb-3 p-3 sale-items-card sale-items shadow-sm border-0">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1">Produk <span class="required-asterisk">*</span></label>
                    <select name="sale_items[${index}][nama_produk]" class="form-select select-product" required>
                        <option disabled selected>Pilih Produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}"
                                data-stock="{{ $product->available_stock }}"
                                data-price="{{ $product->price }}"
                                {{ $product->available_stock <= 0 ? 'disabled' : '' }}>
                                {{ $product->name }} - {{ $product->available_stock > 0 ? $product->available_stock : 'Stok Kosong' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                     <small class="text-danger stock-warning d-none">Jumlah lebih dari stok
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
            updateProductOptions();
            index++;
        });


        // Hapus produk
        $(document).on('click', '.remove-product', function() {
            $(this).closest('.sale-items').remove();
            updateTotalPrice();
            // Panggil fungsi untuk memperbarui opsi produk setelah baris dihapus
            updateProductOptions();
        });

        // Fungsi untuk memperbarui opsi produk
        function updateProductOptions() {
            let selectedProducts = [];
            // Kumpulkan semua ID produk yang sudah dipilih
            $('.select-product').each(function() {
                const selectedVal = $(this).val();
                if (selectedVal) {
                    selectedProducts.push(selectedVal);
                }
            });

            // Iterasi semua dropdown produk
            $('.select-product').each(function() {
                const currentSelect = $(this);
                const currentSelectedVal = currentSelect.val();

                currentSelect.find('option').each(function() {
                    const option = $(this);
                    const optionVal = option.val();

                    // Jika opsi produk ada di daftar produk yang sudah dipilih
                    // dan bukan opsi yang saat ini sedang dipilih di dropdown ini,
                    // maka nonaktifkan opsi tersebut.
                    if (optionVal && selectedProducts.includes(optionVal) && optionVal !==
                        currentSelectedVal) {
                        option.prop('disabled', true);
                    }
                    // Jika opsi produk tidak ada di daftar produk yang sudah dipilih,
                    // dan stoknya > 0, maka aktifkan opsi tersebut.
                    else if (optionVal && !selectedProducts.includes(optionVal) && parseInt(option.data(
                            'stock')) > 0) {
                        option.prop('disabled', false);
                    }
                });
            });
        }


        // Hitung subtotal otomatis
        $(document).on('input', '.quantity, .price', function() {
            const row = $(this).closest('.sale-items');
            const qty = parseFloat(row.find('.quantity').val()) || 0;
            const price = parseFloat(row.find('.price').val()) || 0;
            const subtotal = qty * price;
            row.find('.subtotal').val(subtotal);
            updateTotalPrice();
        });

        $(document).on('input change', '.quantity, .select-product', function() {
            let row = $(this).closest('.sale-items-card');
            let selectedOption = row.find('.select-product option:selected');
            let availableStock = parseInt(selectedOption.data('stock')) || 0;
            let quantity = parseInt(row.find('.quantity').val()) || 0;

            if (quantity > availableStock && availableStock > 0) {
                row.find('.stock-warning').removeClass('d-none');
            } else {
                row.find('.stock-warning').addClass('d-none');
            }
        });


        // Hitung total semua subtotal
        function updateTotalPrice() {
            let total = 0;
            $('.subtotal').each(function() {
                const val = parseFloat($(this).val()) || 0;
                total += val;
            });
            $('.form-control.total_price').val(total);
            updateFinalTotal();
        }

        // Jalankan juga saat produk dipilih (karena harga bisa berubah)
        $(document).on('change', '.select-product', function() {
            const selected = $(this).find(':selected');
            const price = selected.data('price') || 0;
            const row = $(this).closest('.sale-items');
            row.find('.price').val(price).trigger('input'); // Trigger input agar subtotal dan total update

            // Panggil fungsi untuk memperbarui opsi setelah pilihan berubah
            updateProductOptions();
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

            // Update tampilan
            $('.form-control.total_price').val(total); // input total di atas diskon
            $('#final_total').val('Rp ' + discountedTotal.toLocaleString('id-ID'));
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
