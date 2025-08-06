@extends('kasir.layouts.app')

@push('page-header')
    <div class="col-sm-12">
        <h3 class="page-title">Transaksi Penjualan</h3>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kasir.dashboard') }}">Dashboard Kasir</a></li>
            <li class="breadcrumb-item active">Transaksi Penjualan</li>
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

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Nama Pelanggan</label>
                                    <input type="text" name="customer_name" class="form-control"
                                        placeholder="Masukkan nama pelanggan">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="text" name="customer_phone" class="form-control"
                                        placeholder="Contoh: 08xxx">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div id="items-container">
                            <div class="row mb-3 item-row">
                                <div class="col-md-5">
                                    <label class="form-label">Produk</label>
                                    <select name="items[0][product_id]" class="form-select" required>
                                        <option value="">-- Pilih Produk --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                {{ $product->name }} - Stok: {{ $product->stock }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="items[0][qty]" class="form-control qty" value="1"
                                        min="1">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Subtotal</label>
                                    <input type="text" name="items[0][subtotal]" class="form-control subtotal" readonly>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger remove-item w-100">Hapus</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary mb-3" id="add-item">Tambah Produk</button>

                        <div class="form-group mb-3">
                            <label>Total</label>
                            <input type="text" id="total" class="form-control" name="total_price" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label>Metode Pembayaran</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">Pilih Metode</option>
                                <option value="Cash">Tunai</option>
                                <option value="Transfer">Transfer</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Simpan Transaksi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-js')
    <script>
        let index = 1;

        function updateSubtotal(row) {
            let qty = parseFloat(row.find('.qty').val()) || 0;
            let price = parseFloat(row.find('select option:selected').data('price')) || 0;
            let subtotal = qty * price;
            row.find('.subtotal').val(subtotal.toFixed(0));
            updateTotal();
        }

        function updateTotal() {
            let total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#total').val(total.toFixed(0));
        }

        $('#add-item').click(function() {
            let html = `
            <div class="row mb-3 item-row">
                <div class="col-md-5">
                    <select name="items[${index}][product_id]" class="form-select" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->name }} - Stok: {{ $product->stock }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="items[${index}][qty]" class="form-control qty" value="1" min="1">
                </div>
                <div class="col-md-3">
                    <input type="text" name="items[${index}][subtotal]" class="form-control subtotal" readonly>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-item w-100">Hapus</button>
                </div>
            </div>`;
            $('#items-container').append(html);
            index++;
        });

        $(document).on('input change', '.qty, select', function() {
            let row = $(this).closest('.item-row');
            updateSubtotal(row);
        });

        $(document).on('click', '.remove-item', function() {
            $(this).closest('.item-row').remove();
            updateTotal();
        });
    </script>
@endpush
