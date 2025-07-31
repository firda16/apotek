<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $sale->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { padding: 20px; }
        .header { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Invoice Penjualan</h2>
        <div class="header">
            <p><strong>Invoice:</strong> {{ $sale->invoice_number }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y') }}</p>
            <p><strong>Pelanggan:</strong> {{ $sale->customer->nama }}</p>
            <p><strong>No. Telepon:</strong> {{ $sale->customer->telepon }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->saleItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->category->name ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p><strong>Metode Pembayaran:</strong> {{ $sale->payment_method }}</p>
        <p><strong>Diskon:</strong> {{ $sale->discount }}%</p>
        <p><strong>Total Bayar:</strong> Rp {{ number_format($sale->total_price, 0, ',', '.') }}</p>
    </div>
</body>
</html>
