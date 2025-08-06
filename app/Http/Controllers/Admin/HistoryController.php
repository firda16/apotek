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

    public function penjualan(Request $request)
    {
        $title = 'Riwayat Penjualan';

        // Ambil data penjualan dengan relasi
        $query = Sale::with(['customer', 'saleItems.product.category']);
        $sales = $query->orderBy('created_at', 'desc')->paginate(10);

        // Hitung total pendapatan dari semua sale_items
        $total_pendapatan = 0;
        foreach (Sale::with('saleItems')->get() as $sale) {
            foreach ($sale->saleItems as $item) {
                $total_pendapatan += $item->total_price ?? ($item->quantity * $item->unit_price);
            }
        }

        return view('admin.history.penjualan', compact('title', 'sales', 'total_pendapatan'));
    }

    public function show($invoice_number)
    {
        $sale = Sale::with(['saleItems.product.category', 'customer'])
            ->where('invoice_number', $invoice_number)
            ->firstOrFail();

        return view('admin.history.show', compact('sale'));
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

        $query = Purchase::with(['items.product.category', 'supplier']);

        // Optional: handle search if needed
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->orWhereHas('supplier', function ($q_sup) use ($searchTerm) {
                    $q_sup->where('name', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        $purchases = $query->orderBy('created_at', 'desc')->paginate(10);

        // Ambil semua data untuk total keseluruhan
        $allPurchases = Purchase::with('items')->get();

        $totalPembelian = 0;
        foreach ($allPurchases as $purchase) {
            foreach ($purchase->items as $item) {
                $totalPembelian += $item->subtotal ?? ($item->quantity * $item->unit_price);
            }
        }

        return view('admin.history.pembelian', compact('title', 'purchases', 'totalPembelian'));
    }
}
