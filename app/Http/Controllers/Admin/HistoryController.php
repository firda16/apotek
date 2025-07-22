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
//     public function penjualan(Request $request)
// {
//     $title = 'Riwayat Penjualan';

//     $sales = Sale::with(['product.purchase.category'])
//         ->latest()
//         ->paginate(10)
//         ->through(function ($item) {
//             return [
//                 'tanggal' => $item->created_at ?? '-',
//                 'jenis' => 'Penjualan',
//                 'nama' => '-', // Tidak ada nama supplier di penjualan
//                 'total' => $item->total_price ?? '-',
//                 'produk' => $item->product->purchase->product ?? '-',
//                 'kategori' => $item->product->category->name ?? '-',
//                 'jumlah' => $item->quantity ?? '-',
//                 'harga' => $item->price ?? 0,
//                 'metode_pembayaran' => $item->payment_method ?? '-',
//                 'no_antrian' => $item->queue_number ?? '-',
//                 'satuan' => $item->unit ?? '-',
//                 'diskon' => $item->discount ?? 0,
//             ];
//         })->withQueryString();

//     return view('admin.history.penjualan', compact('title', 'sales'));
// }

    public function penjualan(Request $request){
        $title = 'Riwayat Penjualan';
        $query = Sale::query()->with(['product', 'purchase']);
        $sales = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.history.penjualan', compact('title', 'sales'));
    }


    // RIWAYAT PEMBELIAN
//    public function pembelian(Request $request)
// {
//     $title = 'Riwayat Pembelian';

//     $purchases = Purchase::with(['product', 'supplier', 'category'])
//         ->latest()
//         ->paginate(10)
//         ->through(function ($item) {
//             $jumlah = $item->quantity ?? 0;
//             $harga = $item->price ?? 0;
//             $diskon = $item->discount ?? 0;
//             $total = ($jumlah * $harga) - ($jumlah * $harga * $diskon / 100);

//             return [
//                 'tanggal' => $item->created_at ?? '-',
//                 'jenis' => 'Pembelian',
//                 'nama' => $item->supplier->name ?? '-',
//                 'kategori' => $item->category->name ?? '-',
//                 'produk' => $item->product ?? '-',
//                 'jumlah' => $jumlah,
//                 'satuan' => $item->unit ?? '-',
//                 'harga' => $harga,
//                 'diskon' => $diskon,
//                 'metode_pembayaran' => $item->payment_method ?? '-',
//                 'total_price' => $total,
//                 'expired_at' => $item->expiry_date
//                     ? date('d-m-Y', strtotime($item->expiry_date))
//                     : '-',
//             ];
//         })->withQueryString();

//     return view('admin.history.pembelian', compact('title', 'purchases'));
// }
    public function pembelian(Request $request)
    {
        $title = 'Riwayat Pembelian';

        $query = Purchase::query()->with(['category', 'supplier']);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->where('product', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function ($q_cat) use ($searchTerm) {
                      $q_cat->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('supplier', function ($q_sup) use ($searchTerm) {
                      $q_sup->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // $pembelians = $query->get();
        $purchases = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.history.pembelian', compact('title', 'purchases'));
    }

}
