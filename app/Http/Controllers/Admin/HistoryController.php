<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryController extends Controller
{
    // ===================================================================================
    // RIWAYAT PENJUALAN (BAGIAN YANG DIPERBARUI)
    // ===================================================================================
    public function penjualan(Request $request)
    {
        $title = 'Riwayat Penjualan';

        // Cek apakah ada filter yang diterapkan
        $filterApplied = !empty($request->query());

        // Siapkan variabel default
        $sales = new LengthAwarePaginator([], 0, 10); // Paginator kosong
        $total_pendapatan = 0;

        // Hanya jalankan query jika ada filter yang diterapkan dan diisi
        if ($filterApplied && ($request->filled('start_date') || $request->filled('end_date') || $request->filled('payment_method'))) {
            $query = Sale::with(['customer']); // Eager load relasi customer

            // Terapkan filter tanggal
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            // Terapkan filter metode pembayaran
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            // Hitung total pendapatan dari hasil query yang sudah difilter
            // Menggunakan sum() dari query builder jauh lebih efisien
            $total_pendapatan = (clone $query)->sum('total_price');

            // Lakukan pagination pada hasil akhir
            $sales = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        }

        // Kirim data ke view
        return view('admin.history.penjualan', compact('title', 'sales', 'total_pendapatan', 'filterApplied'));
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
    // RIWAYAT PEMBELIAN (BAGIAN YANG DIPERBARUI TOTAL)
    // ===================================================================================
    public function pembelian(Request $request)
    {
        $title = 'Riwayat Pembelian';

        // Cek apakah ada filter yang diterapkan
        $filterApplied = $request->filled('start_date') || $request->filled('end_date') || $request->filled('payment_method');

        $purchases = new LengthAwarePaginator([], 0, 10);
        $totalPembelian = 0;

        if ($filterApplied) {
            $query = Purchase::with(['items.product.category', 'supplier']);

            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            // Clone query untuk total
            $totalQuery = clone $query;
            $filteredPurchases = $totalQuery->get();

            $totalPembelian = $filteredPurchases->flatMap->items->sum(function ($item) {
                return $item->subtotal ?? ($item->quantity * $item->unit_price);
            });

            // Pagination
           $purchases = $query->orderBy('created_at', 'desc')
                   ->paginate(10)
                   ->withQueryString();

        }

        return view('admin.history.pembelian', compact('title', 'purchases', 'totalPembelian', 'filterApplied'));
    }


    // ===================================================================================
    // PENAMBAHAN: Fungsi baru untuk generate PDF Riwayat Pembelian
    // ===================================================================================
    public function cetakPembelianPDF(Request $request)
    {
        // 1. Logika filter disalin sama persis dari fungsi pembelian()
        $query = Purchase::with(['items.product.category', 'supplier']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // 2. Ambil SEMUA data yang terfilter (tanpa pagination)
        $purchases = $query->orderBy('created_at', 'desc')->get();

        // 3. Hitung totalnya agar konsisten
        $totalPembelian = $purchases->flatMap->items->sum(function ($item) {
            return $item->subtotal ?? ($item->quantity * $item->unit_price);
        });

        // Simpan tanggal filter untuk ditampilkan di judul PDF
        $tanggalMulai = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d M Y') : 'Awal';
        $tanggalSelesai = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d M Y') : 'Akhir';

        // 4. Load view PDF dengan data yang sudah disiapkan
        $pdf = PDF::loadView('admin.history.pembelian_pdf', compact('purchases', 'totalPembelian', 'tanggalMulai', 'tanggalSelesai'));

        // 5. Tampilkan PDF di browser
        return $pdf->stream('laporan-pembelian-' . now()->format('d-m-Y') . '.pdf');
    }
}


