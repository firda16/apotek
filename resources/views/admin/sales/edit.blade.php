@extends('admin.layouts.app')

@push('page-css')
@endpush

@push('page-header')
<div class="col-sm-12">
    <h3 class="page-title">Edit Sale</h3>
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Edit Sale</li>
    </ul>
</div>
@endpush

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body custom-edit-service">

                <form method="POST" action="{{ route('sales.update', $sale->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- No Antrian -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No Antrian</label>
                                <input type="text" name="queue_number" class="form-control"
                                    value="{{ $sale->queue_number }}" required>
                            </div>
                        </div>

                        <!-- Metode Pembayaran -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Metode Pembayaran</label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="Tunai" {{ $sale->payment_method == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                    <option value="Transfer" {{ $sale->payment_method == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="QRIS" {{ $sale->payment_method == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                                    <option value="E-Wallet" {{ $sale->payment_method == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                                </select>
                            </div>
                        </div>

                        <!-- Produk Multi Item -->
                        <div class="col-12">
                            <hr>
                            <h5>Detail Produk</h5>

                            <div id="product-wrapper">
                                @foreach ($sale->saleItems as $index => $item)
                                    <div class="row product-item mb-3">
                                        <div class="col-md-3">
                                            <label>Produk</label>
                                            <select name="products[{{ $index }}][product_id]"
                                                class="form-control select2" required>
                                                <option disabled>Pilih Produk</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-category="{{ $product->category->name ?? '-' }}"
                                                        {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label>Kategori</label>
                                            <input type="text" class="form-control category"
                                                value="{{ $item->product->category->name ?? '-' }}" readonly>
                                        </div>

                                        <div class="col-md-2">
                                            <label>Jumlah</label>
                                            <input type="number" name="products[{{ $index }}][quantity]"
                                                class="form-control" value="{{ $item->quantity }}" min="1">
                                        </div>

                                        <div class="col-md-2">
                                            <label>Satuan</label>
                                            <input type="text" name="products[{{ $index }}][unit]"
                                                class="form-control" value="{{ $item->unit }}">
                                        </div>

                                        <div class="col-md-2">
                                            <label>Harga per Produk</label>
                                            <input type="number" name="products[{{ $index }}][unit_price]"
                                                class="form-control" value="{{ $item->unit_price }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Diskon Total (opsional) -->
                        <div class="col-md-4 mt-3">
                            <label>Diskon (%)</label>
                            <input type="number" name="discount" class="form-control" value="{{ $sale->discount ?? 0 }}">
                        </div>

                        <!-- Tombol Simpan -->
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
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
    // Update input kategori otomatis ketika produk diganti
    $(document).on('change', 'select[name^="products"]', function () {
        const category = $(this).find(':selected').data('category') || '-';
        $(this).closest('.product-item').find('.category').val(category);
    });
</script>
@endpush
