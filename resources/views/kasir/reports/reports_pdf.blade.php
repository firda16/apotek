<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 90px;
            margin-bottom: 5px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .title {
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px;
            font-size: 10px;
        }

        th {
            background: #f2f2f2;
            text-align: center;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }

        .total-row td {
            background: #fafafa;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header">
        @if (!empty($setting?->image))
            <img src="{{ public_path($setting->image) }}" alt="Logo" style="height: auto; width: auto;">
        @else
            <span>{{ $setting?->nama ?? 'Website' }}</span>
        @endif
        <h2>{{ $setting?->nama ?? 'Website' }}</h2>
        <p>{{ $setting?->alamat ?? 'Jl. Jaya Alamat No. 123' }} | Telp: {{ $setting?->telepon ?? '0812-3456-7890' }} |
            Email: {{ $setting?->email ?? 'apotek@gmail.com' }}</p>
        <div class="title">Laporan Penjualan</div>
        <p>Periode:
            {{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('l, d F Y') ?? '-' }}
            s/d
            {{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('l, d F Y') ?? '-' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Telepon</th>
                <th>Metode</th>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
                <th>Diskon</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1; @endphp
            @foreach ($sales as $sale)
                @foreach ($sale->saleItems as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($sale->created_at)->format('d-m-Y') }}</td>
                        <td>{{ $sale->customer->nama ?? '-' }}</td>
                        <td class="text-center">{{ $sale->customer->telepon ?? '-' }}</td>
                        <td class="text-center">{{ $sale->payment_method ?? '-' }}</td>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $sale->discount ?? 0 }}%</td>
                        <td class="text-right">Rp{{ number_format($sale->total_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="10" class="text-right">Total Pendapatan</td>
                <td class="text-right">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
    </div>

</body>

</html>
