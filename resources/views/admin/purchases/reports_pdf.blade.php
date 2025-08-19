<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembelian</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-width: 90px; margin-bottom: 5px; }
        .header h2 { margin: 0; font-size: 18px; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 10px; }
        .title { margin-top: 10px; font-size: 14px; font-weight: bold; text-decoration: underline; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #444; padding: 6px; font-size: 10px; }
        th { background: #f2f2f2; text-align: center; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .footer { margin-top: 30px; text-align: right; font-size: 11px; }
        .total-row td { background: #fafafa; font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('assets/img/logo.png') }}" alt="Logo Apotek">
    <h2>Apotek Sehat Sentosa</h2>
    <p>Jl. Jaya Alamat No. 123 | Telp: 0812-3456-7890 | Email: apotek@gmail.com</p>
    <div class="title">Laporan Pembelian</div>
    <p>Periode: {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Pemasok</th>
            <th>Metode</th>
            <th>Produk</th>
            <th>Kategori</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Harga</th>
            <th>Subtotal</th>
            <th>Expired</th>
        </tr>
    </thead>
    <tbody>
        @php $no=1; @endphp
        @foreach($pembelians as $purchase)
            @foreach($purchase->purchaseItems as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($purchase->created_at)->format('d-m-Y') }}</td>
                <td>{{ $purchase->supplier->name ?? '-' }}</td>
                <td class="text-center">{{ $purchase->payment_method ?? '-' }}</td>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td>{{ $item->product->category->name ?? '-' }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">{{ $item->product->unit ?? '-' }}</td>
                <td class="text-right">Rp{{ number_format($item->unit_price,0,',','.') }}</td>
                <td class="text-right">Rp{{ number_format($item->total_price,0,',','.') }}</td>
                <td class="text-center">{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('d-m-Y') : '-' }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="9" class="text-right">Total Pembelian</td>
            <td colspan="2" class="text-right">Rp{{ number_format($totalPembelian,0,',','.') }}</td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
</div>

</body>
</html>
