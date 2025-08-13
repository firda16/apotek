<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 700px;
            margin: auto;
            padding: 30px;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .company-info {
            text-align: right;
        }

        .company-info p {
            margin: 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 4px 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.items th {
            background: #f0f0f0;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ccc;
        }

        table.items td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .total-section {
            margin-top: 20px;
            width: 100%;
        }

        .total-section td {
            padding: 6px;
        }

        .total-section .label {
            text-align: right;
            width: 80%;
        }

        .total-section .amount {
            text-align: right;
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table width="100%">
                <tr>
                    <td>
                        <h2>Invoice Penjualan</h2>
                        <p><strong>No. Invoice:</strong> {{ $sale->invoice_number }}</p>
                        <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('d M Y') }}</p>
                        <p><strong>Status:</strong> {{ $sale->status }}</p>
                    </td>
                    <td class="company-info">
                        {{-- <h3>Nama Toko</h3> --}}
                        <img src="{{ public_path('assets/img/logo.png') }}" class="logo img-fluid"
                            style="max-width: 150px;">

                        <p>Jl. Jaya Alamat No. 123</p>
                        <p>Telp: 0812-3456-7890</p>
                        <p>Email: Apotek@gmail.com</p>
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-table">
            <tr>
                <td><strong>Pelanggan:</strong> {{ $sale->customer->nama }}</td>
                <td><strong>No. Telepon:</strong> {{ $sale->customer->telepon }}</td>
            </tr>
        </table>



        <table class="items">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    {{-- <th>Kategori</th> --}}
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->saleItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product->name }}</td>
                        {{-- <td>{{ $item->product->category->name ?? '-' }}</td> --}}
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="total-section">
            <tr>
                <td class="label">Metode Pembayaran:</td>
                <td class="amount">{{ $sale->payment_method }}</td>
            </tr>
            <tr>
                <td class="label">Diskon:</td>
                <td class="amount">{{ $sale->discount }}%</td>
            </tr>
            <tr>
                <td class="label">Total Bayar:</td>
                <td class="amount">Rp {{ number_format($sale->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Terima kasih atas pembelian Anda!</p>
        </div>
    </div>
</body>

</html>
