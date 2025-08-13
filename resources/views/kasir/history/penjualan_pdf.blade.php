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
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0; font-size: 12px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        table thead th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        /* Style untuk baris transaksi utama */
        .transaction-row td {
            background-color: #e9ecef;
            font-weight: bold;
        }
        /* Style untuk tabel detail produk */
        .product-details-table {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
        }
        .product-details-table th, .product-details-table td {
            border: 1px solid #eee;
            padding: 5px;
            font-size: 9px;
        }
        .product-details-table thead th {
            background-color: #fafafa;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        tfoot strong { font-size: 12px; }
        hr { border: 0; border-top: 1px solid #eee; margin: 20px 0; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Riwayat Penjualan</h1>
        <p>Periode: {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}</p>
    </div>

    <hr>

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
                {{-- Baris utama untuk setiap transaksi --}}
                <tr class="transaction-row">
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $sale->invoice_number }}</strong> <br>
                        Tanggal: {{ \Carbon\Carbon::parse($sale->created_at)->format('d-m-Y') }} <br>
                        Pelanggan: {{ $sale->customer->nama ?? 'Umum' }}
                    </td>
                    <td class="text-right">
                        Rp{{ number_format($sale->total_price, 0, ',', '.') }}
                    </td>
                </tr>
                {{-- Baris untuk detail produk --}}
                <tr>
                    <td colspan="3" style="padding: 5px 10px;">
                        <table class="product-details-table">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-right">Harga Satuan</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->saleItems as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp{{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
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
