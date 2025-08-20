<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { max-width: 90px; margin-bottom: 5px; }
        .header h2 { margin: 0; font-size: 18px; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 10px; }
        .title { margin-top: 10px; font-size: 14px; font-weight: bold; text-decoration: underline; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #444; padding: 6px; font-size: 10px; vertical-align: top; }
        th { background: #f2f2f2; text-align: center; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .footer { margin-top: 30px; text-align: right; font-size: 11px; }
        .total-row td { background: #fafafa; font-weight: bold; }

        /* Style untuk daftar item agar tidak ada bullet/nomor */
        .item-list {
            padding-left: 0;
            margin: 0;
            list-style-type: none;
        }
        .item-list li {
            margin-bottom: 3px; /* Memberi sedikit jarak antar item */
        }
    </style>
</head>
<body>

{{-- BAGIAN HEADER INI SAMA SEPERTI KODE ASLI ANDA --}}
<div class="header">
    <img src="{{ public_path('assets/img/logo.png') }}" alt="Logo Apotek">
    <h2>Apotek Sehat Sentosa</h2>
    <p>Jl. Jaya Alamat No. 123 | Telp: 0812-3456-7890 | Email: apotek@gmail.com</p>
    <div class="title">Laporan Penjualan</div>
    <p>Periode: {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>No. Invoice</th>
            <th>Pelanggan</th>
            <th>No Telepon</th>
            <th>Metode</th>
            <th>Detail Produk</th>
            <th>Diskon</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @php $no=1; @endphp
        @forelse($sales as $sale)
        <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td class="text-center">{{ \Carbon\Carbon::parse($sale->created_at)->format('d-m-Y') }}</td>
            {{-- KOLOM NOMOR INVOICE DITAMBAHKAN --}}
            <td>{{ $sale->invoice_number ?? '-' }}</td>
            <td>{{ $sale->customer->nama ?? '-' }}</td>
            <td>{{ $sale->customer->telepon ?? '-' }}</td>
            <td class="text-center">{{ $sale->payment_method ?? '-' }}</td>
            <td>
                <ul class="item-list">
                    @foreach($sale->saleItems as $item)
                        {{-- HARGA SATUAN & SUBTOTAL DITAMBAHKAN DI SINI --}}
                        <li>
                            {{ $item->product->name ?? 'N/A' }} <br>
                            <em>({{ $item->quantity }} x Rp{{ number_format($item->unit_price, 0, ',', '.') }}) = <strong>Rp{{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</strong></em>
                        </li>
                    @endforeach
                </ul>
            </td>
            <td class="text-center">{{ $sale->discount ?? 0 }}%</td>
            <td class="text-right">Rp{{ number_format($sale->total_price, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            {{-- Colspan disesuaikan menjadi 8 --}}
            <td colspan="9" class="text-center">Tidak ada data penjualan pada periode ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="total-row">
            {{-- Colspan disesuaikan menjadi 7 --}}
            <td colspan="8" class="text-right"><strong>Total Pendapatan</strong></td>
            <td class="text-right"><strong>Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</strong></td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
</div>

</body>
</html>
