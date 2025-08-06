<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    // Tampilkan form transaksi
    public function transaksi()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('kasir.transaksi.create', compact('products'));
    }

    // Simpan transaksi
    public function storeTransaksi(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'metode_pembayaran' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // Hitung total
            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['produk_id']);
                $subtotal = $product->price * $item['jumlah'];
                $total += $subtotal;
            }

            // Buat no invoice
            $no_invoice = 'INV-' . date('YmdHis') . '-' . rand(100, 999);

            // Simpan ke tabel transaksi
            $transaksi = Transaksi::create([
                'no_invoice' => $no_invoice,
                'nama_pelanggan' => $request->nama_pelanggan,
                'nomor_telepon' => $request->nomor_telepon,
                'metode_pembayaran' => $request->metode_pembayaran,
                'total_harga' => $total,
                'diskon' => 0, // default jika belum support diskon input
            ]);

            // Simpan detail per item
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['produk_id']);

                // Kurangi stok
                if ($product->stock < $item['jumlah']) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                $product->stock -= $item['jumlah'];
                $product->save();

                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $product->id,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $product->price,
                    'subtotal' => $product->price * $item['jumlah'],
                ]);
            }

            DB::commit();

            return redirect()->route('kasir.transaksi')->with('success', 'Transaksi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }

    // Tampilkan laporan
    public function laporan(Request $request)
    {
        $query = Transaksi::with(['details.produk']);

        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $query->whereBetween('created_at', [
                $request->tanggal_mulai . ' 00:00:00',
                $request->tanggal_selesai . ' 23:59:59'
            ]);
        }

        $transaksis = $query->orderBy('created_at', 'desc')->get();

        return view('kasir.laporan.index', compact('transaksis'));
    }

    // Detail transaksi
    public function show($id)
    {
        $transaksi = Transaksi::with(['details.produk'])->findOrFail($id);
        return view('kasir.laporan.show', compact('transaksi'));
    }
}
