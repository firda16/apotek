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

    // ✅ RIWAYAT PENJUALAN
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
                ];
            })
            ->sortByDesc('tanggal');

        return view('admin.history.penjualan', compact('title', 'sales'));
    }

    // ✅ RIWAYAT PEMBELIAN
    public function pembelian()
    {
        $title = 'Riwayat Pembelian';

        $purchases = Purchase::get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->created_at ?? '-',
                    'jenis' => 'Pembelian',
                    'nama' => $item->supplier->name ?? '-',
                    'kategori' => $item->category->name ?? '-',
                    'total' => $item->cost_price ?? '-',
                    'produk' => $item->product ?? '-',
                    'jumlah' => $item->quantity ?? '-',
                ];
            })
            ->sortByDesc('tanggal');

        return view('admin.history.pembelian', compact('title', 'purchases'));
    }
}
