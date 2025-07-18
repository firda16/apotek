<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaksi;

class KasirController extends Controller
{
  public function transaksi()
{
    $products = Product::all();
    return view('kasir.transaksi.create', compact('products'));
}

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

}
