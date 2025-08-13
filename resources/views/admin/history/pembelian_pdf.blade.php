<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Riwayat Pembelian</title>
    <style>
        /* Gaya dasar untuk dokumen PDF */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 10px; /* Ukuran font dikecilkan agar muat banyak kolom */
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 6px; /* Padding dikecilkan */
            text-align: left;
            vertical-align: top;
        }
        table thead th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        table tfoot td {
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        hr {
            border: 0;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Riwayat Pembelian</h1>
        <p>Periode: {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}</p>
    </div>

    <hr>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Pemasok</th>
                <th>Nama Obat</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th class="text-center">Jml</th>
                <th class="text-right">Harga Beli</th>
                <th>Metode Bayar</th>
                <th class="text-right">Total Harga</th>
                <th>Tgl. Kedaluwarsa</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            @forelse ($purchases as $purchase)
                @foreach ($purchase->items as $item)
                    <tr>
                        <td class="text-center">{{ $counter++ }}</td>
                        <td>{{ \Carbon\Carbon::parse($purchase->created_at)->format('d-m-Y') }}</td>
                        <td>{{ $purchase->supplier->name ?? '-' }}</td>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->product->category->name ?? '-' }}</td>
                        <td>{{ $item->product->unit ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($purchase->payment_method ?? '-') }}</td>
                        <td class="text-right">Rp{{ number_format($item->subtotal ?? ($item->quantity * $item->unit_price), 0, ',', '.') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->expiry_date)->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" class="text-right"><strong>Total Keseluruhan Pembelian</strong></td>
                <td colspan="2" class="text-right"><strong>Rp{{ number_format($totalPembelian, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
