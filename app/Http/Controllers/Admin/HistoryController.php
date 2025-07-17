<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Support\Collection;

class HistoryController extends Controller
{
    // public function index()
    // {
    //     $title = 'Riwayat';

    //     $purchases = Purchase::orderBy('created_at', 'desc')->get();
    //     $sales = Sale::orderBy('created_at', 'desc')->get();

    //     return view('admin.history.index', compact('title', 'purchases', 'sales'));
    // }
    

public function index()
{
    $title = 'Riwayat';

    // Ambil dan format data pembelian
    $purchases = Purchase::get()
        ->map(function ($item) {
            return [
                'tanggal' => $item->created_at ?? '-',
                'jenis' => 'Pembelian' ?? '-',
                'nama' => $item->supplier->name ?? '-',
                'kategori' => $item->category->name ?? '-',
                'total' => $item->cost_price ?? '-',
                'produk' => $item->product ?? '-',
                'jumlah' => $item->quantity ?? '-',
            ];
        });

    // Ambil dan format data penjualan
    $sales = Sale::get()
        ->map(function ($item) {
            return [
                'tanggal' => $item->created_at ?? '-',
                'jenis' => 'Penjualan' ?? '-',
                'nama' => '-' ?? '-',
                'total' => $item->total_price ?? '-',
                'produk' => $item->product->purchase->product ?? '-',
                'kategori' => $item->purchase->category->name ?? '-',
                'jumlah' => $item->quantity ?? '-',
            ];
        });

    // Gabung dan urutkan berdasarkan tanggal
    $histories = $purchases->concat($sales)->sortByDesc('tanggal');

    return view('admin.history.index', compact('title', 'histories'));
}

}
