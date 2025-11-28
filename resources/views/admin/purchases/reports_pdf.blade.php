<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pembelian</title>
    <style>
        /* --- Basic Setup --- */
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        /* --- Header --- */
        .header {
            text-align: center;
            margin-bottom: 25px;
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

        .header .title {
            margin-top: 15px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* --- Table Styling --- */
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
            vertical-align: middle;
            /* Ensures rowspan content is centered vertically */
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
            font-size: 11px;
        }

        .total-row td {
            background-color: #fafafa;
            font-weight: bold;
        }

        /* --- Text Alignment --- */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* --- Footer --- */
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>

<body>

    {{-- SECTION: Report Header --}}
    <div class="header">
        @if (!empty($setting?->image))
            <img src="{{ public_path($setting->image) }}" alt="Logo" style="max-width: 150px; height: auto;">
        @else
            <span>{{ $setting?->nama ?? 'Website' }}</span>
        @endif
        <h2>{{ $setting?->nama ?? 'website' }}</h2>
        <p>{{ $setting?->alamat ?? 'jl.contoh' }} | Telp: {{ $setting?->telepon ?? '0812-3456-7890' }} | Email:
            {{ $setting?->email ?? 'apotek@gmail.com' }}</p>
        <div class="title">LAPORAN PEMBELIAN</div>
        <p>Periode:
            {{ \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('l, d F Y') ?? '-' }}
            s/d
            {{ \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('l, d F Y') ?? '-' }}
        </p>
        <p>Metode Pembayaran:
            {{ request('payment_method') ?: 'Semua Metode' }}
        </p>
    </div>

    {{-- SECTION: Report Table --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No Invoice</th>
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
            @php $no = 1; @endphp
            @forelse($pembelians as $purchase)
                @foreach ($purchase->purchaseItems as $item)
                    <tr>
                        {{-- This cell is for every item row to keep numbering consistent --}}
                        <td class="text-center">{{ $no++ }}</td>

                        {{-- Using @if ($loop->first) with rowspan to group common purchase data --}}
                        @if ($loop->first)
                            <td class="text-center" rowspan="{{ $purchase->purchaseItems->count() }}">
                                {{ \Carbon\Carbon::parse($purchase->created_at)->format('d-m-Y') }}</td>
                            <td rowspan="{{ $purchase->purchaseItems->count() }}">
                                {{ $purchase->invoice_number ?? '-' }}
                            </td>
                            <td rowspan="{{ $purchase->purchaseItems->count() }}">
                                {{ $purchase->supplier->name ?? '-' }}</td>
                            <td class="text-center" rowspan="{{ $purchase->purchaseItems->count() }}">
                                {{ $purchase->payment_method ?? '-' }}</td>
                        @endif

                        {{-- These cells are for specific item details --}}
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->product->category->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ $item->product->unit ?? '-' }}</td>
                        <td class="text-right">Rp{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp{{ number_format($item->total_price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            {{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('d-m-Y') : '-' }}
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="12" class="text-center">Tidak ada data untuk periode yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                {{-- Colspan is adjusted to align the total under the Subtotal column --}}
                <td colspan="10" class="text-right"><strong>TOTAL PEMBELIAN</strong></td>
                <td class="text-right"><strong>Rp{{ number_format($totalPembelian, 0, ',', '.') }}</strong></td>
                <td></td> {{-- Empty cell for the 'Expired' column --}}
            </tr>
        </tfoot>
    </table>

    {{-- SECTION: Report Footer --}}
    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
    </div>

</body>

</html>
