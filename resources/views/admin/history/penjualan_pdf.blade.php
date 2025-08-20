<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Penjualan</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 10px;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .header .logo {
            flex: 0 0 120px;
        }

        .header .logo img {
            max-width: 100px;
        }

        .header .company-info {
            flex: 1;
            text-align: left;
            padding-left: 15px;
        }

        .header .company-info h2 {
            margin: 0;
            font-size: 18px;
        }

        .header .company-info p {
            margin: 2px 0;
            font-size: 11px;
        }

        .report-title {
            text-align: center;
            margin: 10px 0 20px;
        }

        .report-title h1 {
            margin: 0;
            font-size: 20px;
        }

        .report-title p {
            margin: 5px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        table thead th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Baris transaksi utama */
        .transaction-row td {
            background-color: #e9ecef;
            font-weight: bold;
        }

        /* Tabel detail produk */
        .product-details-table {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
        }

        .product-details-table th,
        .product-details-table td {
            border: 1px solid #eee;
            padding: 5px;
            font-size: 9px;
        }

        .product-details-table thead th {
            background-color: #fafafa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        tfoot strong {
            font-size: 12px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table style="width: 100%; border-bottom: 2px solid #444; margin-bottom: 20px; padding-bottom: 10px;">
        <tr>
            <td style="width: 120px; text-align: center; vertical-align: middle;">
                @if (!empty($setting?->image))
                    <img src="{{ public_path($setting->image) }}" alt="Logo" height="50">
                @else
                    <span>{{ $setting?->nama ?? 'Website' }}</span>
                @endif
            </td>
            <td style="text-align: left; vertical-align: middle; padding-left: 15px;">
                <h2 style="margin: 0; font-size: 18px;">{{ $setting?->nama ?? 'Website' }}</h2>
                <p style="margin: 2px 0; font-size: 11px;">{{ $setting?->alamat ?? 'Jl. Jaya Alamat No. 123' }}</p>
                <p style="margin: 2px 0; font-size: 11px;">Telp: {{ $setting?->telepon ?? '0812-3456-7890' }}</p>
                <p style="margin: 2px 0; font-size: 11px;">Email: {{ $setting?->email ?? 'apotek@gmail.com' }}</p>
            </td>
        </tr>
    </table>


    <!-- JUDUL LAPORAN -->
    <div class="report-title">
        <h1>Laporan Riwayat Penjualan</h1>
        <p>Periode:
            {{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('l, d F Y') ?? '-' }}
            s/d
            {{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('l, d F Y') ?? '-' }}
        </p>
        <p>Metode Pembayaran:
            {{ request('payment_method') ?: 'Semua Metode' }}
        </p>
    </div>

    <!-- TABEL TRANSAKSI -->
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Info Transaksi</th>
                <th class="text-right">Total Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <!-- Baris utama transaksi -->
                <tr class="transaction-row">
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $sale->invoice_number }}</strong><br>
                        Tanggal: {{ \Carbon\Carbon::parse($sale->created_at)->format('d-m-Y') }}<br>
                        Pelanggan: {{ $sale->customer->nama ?? 'Umum' }}
                    </td>
                    <td class="text-right">
                        Rp{{ number_format($sale->total_price, 0, ',', '.') }}
                    </td>
                </tr>
                <!-- Detail produk -->
                <tr>
                    <td colspan="3" style="padding: 5px 10px;">
                        <table class="product-details-table">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Metode Pembayaran</th>
                                    <th class="text-right">Harga Satuan</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->saleItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-center">{{ ucfirst($sale->payment_method ??'-')}}</td>
                                        <td class="text-right">Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="text-right">
                                            Rp{{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right"><strong>Total Keseluruhan Pendapatan</strong></td>
                <td class="text-right"><strong>Rp{{ number_format($total_pendapatan, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
