<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Collection;

class HistoryController extends Controller
{
    // FUNGSI TIDAK DIPAKAI LAGI
    // public function index()
    // {
    //     ...
    // }

    // RIWAYAT PENJUALAN
    public function penjualan()
    {
        $title = 'Riwayat Penjualan';

        $sales = Sale::get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->created_at ?? '-',
                    'jenis' => 'Penjualan',
                    'nama' => '-', // Tidak ada nama supplier di penjualan
                    'total' => $item->total_price ?? '-',
                    'produk' => $item->product->purchase->product ?? '-',
                    'kategori' => $item->purchase->category->name ?? '-',
                    'jumlah' => $item->quantity ?? '-',
                    'harga' => $item->price ?? 0,
                    'metode_pembayaran' => $item->payment_method ?? '-',
                    'no_antrian' => $item->queue_number ?? '-',
                    'satuan' => $item->unit ?? '-',
                    'diskon' => $item->discount ?? 0,
                ];
            })
            ->sortByDesc('tanggal');

        return view('admin.history.penjualan', compact('title', 'sales'));
    }

    // RIWAYAT PEMBELIAN
    public function pembelian()
    {
        $title = 'Riwayat Pembelian';

        $purchases = Purchase::with(['product', 'supplier', 'category'])
            ->get()
            ->map(function ($item) {
                $jumlah = $item->quantity ?? 0;
                $harga = $item->price ?? 0;
                $diskon = $item->discount ?? 0;
                $total = ($jumlah * $harga) - ($jumlah * $harga * $diskon / 100);

                return [
                    'tanggal' => $item->created_at ?? '-',
                    'jenis' => 'Pembelian',
                    'nama' => $item->supplier->name ?? '-',
                    'kategori' => $item->category->name ?? '-',
                    'produk' => $item->product ?? '-',
                    'jumlah' => $jumlah,
                    'satuan' => $item->unit ?? '-',
                    'harga' => $harga,
                    'diskon' => $diskon,
                    'metode_pembayaran' => $item->payment_method ?? '-',
                    'total' => $total,
                    'expired_at' => $item->expiry_date
                        ? date('d-m-Y', strtotime($item->expiry_date))
                        : '-',
                ];
            })
            ->sortByDesc('tanggal');

        return view('admin.history.pembelian', compact('title', 'purchases'));
    }
}
