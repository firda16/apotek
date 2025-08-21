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
            font-size: 10px;
        }

        /* Gaya untuk Header/Kop Surat */
        .header-center {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .header-center img {
            max-width: 100px;
        }

        .header-center h2 {
            margin: 5px 0 0 0;
            font-size: 18px;
        }

        .header-center p {
            margin: 2px 0;
            font-size: 11px;
        }

        /* Gaya untuk Judul Laporan */
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

        /* Gaya untuk Tabel Utama */
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
            vertical-align: top;
        }

        table thead th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        table tfoot td {
            font-weight: bold;
        }

        /* Perataan Teks */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header-center">
        @if (!empty($setting?->image))
            <img src="{{ public_path($setting->image) }}" alt="Logo" style="height: auto; width: auto;">
        @else
            <span>{{ $setting?->nama ?? 'Website' }}</span>
        @endif
        <h2>{{ $setting?->nama ?? 'Website' }}</h2>
        <p>{{ $setting?->alamat ?? 'Jl. Jaya Alamat No. 123' }}</p>
        <p>Telp: {{ $setting?->telepon ?? '0812-3456-7890' }} | Email: {{ $setting?->email ?? 'apotek@gmail.com' }}</p>
    </div>

    <div class="report-title">
        <h1>Laporan Riwayat Pembelian</h1>
        <p>Periode:
            @if ($tanggalMulai)
                {{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('l, d F Y') }}
            @else
                Awal
            @endif
            s/d
            @if ($tanggalSelesai)
                {{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('l, d F Y') }}
            @else
                Akhir
            @endif
        </p>
        <p>Metode Pembayaran:
            {{ request('payment_method') ?: 'Semua Metode' }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>No Invoice</th>
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
                        <td>{{ $purchase->invoice_number ?? '-' }}</td>
                        <td>{{ $purchase->supplier->name ?? '-' }}</td>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->product->category->name ?? '-' }}</td>
                        <td>{{ $item->product->unit ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($purchase->payment_method ?? '-') }}</td>
                        <td class="text-right">
                            Rp{{ number_format($item->subtotal ?? $item->quantity * $item->unit_price, 0, ',', '.') }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->expiry_date)->format('d-m-Y') }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="12" class="text-center">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10" class="text-right"><strong>Total Keseluruhan Pembelian</strong></td>
                <td colspan="2" class="text-right">
                    <strong>Rp{{ number_format($totalPembelian, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
