@extends('kasir.layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Transaksi Penjualan</h4>

    <form action="{{ route('kasir.transaksi.store') }}" method="POST" id="form-transaksi">
        @csrf

        <div class="row">
            {{-- Informasi Transaksi --}}
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Informasi</h5>
                        <div class="mb-3">
                            <label>Invoice</label>
                            <input type="text" name="invoice" class="form-control-plaintext" value="INV{{ now()->format('YmdHis') }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Tambah Produk --}}
            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Pilih Obat</h5>
                        <div class="mb-3">
                            <label>Nama Obat</label>
                            <select id="product-select" class="form-control">
                                <option value="">-- Pilih Obat --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        data-name="{{ $product->nama }}"
                                        data-price="{{ $product->harga_jual }}"
                                        data-stock="{{ $product->stok }}">
                                        {{ $product->nama }} (Stok: {{ $product->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Jumlah</label>
                            <input type="number" id="qty" class="form-control" min="1" value="1">
                        </div>
                        <button type="button" class="btn btn-info" id="btn-add-cart">
                            <i class="fe fe-plus-circle"></i> Tambah ke Keranjang
                        </button>
                    </div>
                </div>
            </div>

            {{-- Total --}}
            <div class="col-md-3">
                <div class="card bg-white text-dark text-center mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Total</h5>
                        <h2 id="grand-total" class="fw-bold">Rp. 0</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Keranjang --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Keranjang Belanja</h5>
                <div class="table-responsive">
                    <table class="table table-bordered" id="cart-table">
                        <thead class="thead-light">
                            <tr>
                                <th>Obat</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cart-body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pembayaran --}}
        <div class="card mb-4">
            <div class="card-body row">
                <div class="col-md-4">
                    <label>Metode Pembayaran</label>
                    <select name="metode_pembayaran" class="form-control">
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Bayar</label>
                    <input type="number" name="bayar" id="bayar" class="form-control" placeholder="0">
                </div>
                <div class="col-md-4">
                    <label>Kembalian</label>
                    <input type="text" id="kembalian" class="form-control-plaintext text-success fw-bold" value="Rp. 0" readonly>
                </div>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="text-end">
            <button type="reset" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn btn-success">
                <i class="fe fe-save"></i> Simpan & Cetak Struk
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    const cart = [];

    function formatRupiah(number) {
        return 'Rp. ' + number.toLocaleString('id-ID');
    }

    function renderCart() {
        const tbody = document.getElementById('cart-body');
        tbody.innerHTML = '';
        let grandTotal = 0;

        cart.forEach((item, index) => {
            const total = item.harga * item.qty;
            grandTotal += total;

            tbody.innerHTML += `
                <tr>
                    <td>
                        <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                        ${item.nama}
                    </td>
                    <td>${formatRupiah(item.harga)}</td>
                    <td>
                        <input type="number" name="items[${index}][qty]" class="form-control qty-input" value="${item.qty}" min="1" data-index="${index}">
                    </td>
                    <td>${formatRupiah(total)}</td>
                    <td><button type="button" class="btn btn-danger btn-sm btn-delete" data-index="${index}"><i class="fe fe-trash"></i></button></td>
                </tr>
            `;
        });

        document.getElementById('grand-total').innerText = formatRupiah(grandTotal);
        updateKembalian();
    }

    document.getElementById('btn-add-cart').addEventListener('click', () => {
        const select = document.getElementById('product-select');
        const qty = parseInt(document.getElementById('qty').value);
        const selected = select.options[select.selectedIndex];

        if (!selected.value || qty <= 0) return alert('Pilih obat dan qty valid');

        const id = selected.value;
        const nama = selected.dataset.name;
        const harga = parseInt(selected.dataset.price);
        const stok = parseInt(selected.dataset.stock);

        if (qty > stok) return alert(`Stok hanya tersedia ${stok}`);

        const existing = cart.find(item => item.id == id);
        if (existing) {
            existing.qty += qty;
            if (existing.qty > stok) existing.qty = stok;
        } else {
            cart.push({ id, nama, harga, qty });
        }

        renderCart();
    });

    document.getElementById('cart-body').addEventListener('input', (e) => {
        if (e.target.classList.contains('qty-input')) {
            const index = e.target.dataset.index;
            let val = parseInt(e.target.value);
            if (val < 1) val = 1;
            cart[index].qty = val;
            renderCart();
        }
    });

    document.getElementById('cart-body').addEventListener('click', (e) => {
        if (e.target.closest('.btn-delete')) {
            const index = e.target.closest('.btn-delete').dataset.index;
            cart.splice(index, 1);
            renderCart();
        }
    });

    document.getElementById('bayar').addEventListener('input', updateKembalian);

    function updateKembalian() {
        const bayar = parseInt(document.getElementById('bayar').value) || 0;
        const totalText = document.getElementById('grand-total').innerText.replace(/[^\d]/g, '');
        const total = parseInt(totalText);
        const kembali = bayar - total;
        const kembaliText = kembali < 0 ? `Kurang Rp. ${Math.abs(kembali).toLocaleString('id-ID')}` : formatRupiah(kembali);
        document.getElementById('kembalian').value = kembaliText;
    }
</script>
@endsection
