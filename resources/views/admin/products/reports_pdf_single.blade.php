<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Stok{{ ucfirst($type) }}</title>
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
        <p> {{ $setting?->alamat ?? 'Jl. Jaya Alamat No. 123' }} | Telp: {{ $setting?->telepon ?? '0812-3456-7890' }} | Email: {{ $setting?->email ?? 'apotek@gmail.com' }}</p>
        <div class="title">Laporan Stok {{ ucfirst($type) }}</div>
        <p>Periode: {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}</p>
    </div>

    @if ($type === 'current')
        <h4>Stok Saat Ini</h4>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Stok Tersedia</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($currentStock as $i => $row)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $row['product_name'] }}</td>
                        <td>{{ $row['category_name'] }}</td>
                        <td class="text-right">{{ number_format($row['available_stock'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $row['unit'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($type === 'in')
        <h4>Stok Masuk</h4>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Jumlah Masuk</th>
                    <th>Satuan</th>
                    <th>Tgl Expired</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stockIn as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->purchase->created_at)->format('d-m-Y') }}</td>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->product->category->name ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->product->unit ?? '-' }}</td>
                        <td class="text-center">
                            {{ $item->expiry_date ? date('d-m-Y', strtotime($item->expiry_date)) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($type === 'out')
        <h4>Stok Keluar</h4>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Jumlah Keluar</th>
                    <th>Satuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stockOut as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="text-center">{{ $item->sale->created_at->format('d-m-Y') }}</td>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->product->category->name ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item->product->unit ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
    </div>

</body>

</html>
