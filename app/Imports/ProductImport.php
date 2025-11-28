<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {

        // Pastikan nama barang ada, jika kosong skip
        if (empty($row['nama_barang'])) {
            return null;
        }

        $namaBarang = trim($row['nama_barang']);

        // Bersihkan angka (Hapus 'Rp', titik, koma, spasi, dll)
        // Jika kosong, set jadi 0
        $hargaBeli = isset($row['harga_beli']) ? (int) preg_replace('/\D/', '', $row['harga_beli']) : 0;
        $hargaJual = isset($row['harga_jual']) ? (int) preg_replace('/\D/', '', $row['harga_jual']) : 0;

        // Stok Awal
        $stokAwal = isset($row['stok_awal']) ? (int) preg_replace('/\D/', '', $row['stok_awal']) : 0;
        $product = Product::where('name', $namaBarang)->first();

        if (!$product) {
            // === BARANG BARU ===
            $kategoriDefault = Category::firstOrCreate(['name' => 'Tanpa Kategori']);

            $product = Product::create([
                'name'           => $namaBarang,
                'category_id'    => $kategoriDefault->id,
                'unit'           => 'Pcs',
                'purchase_price' => $hargaBeli, // Pastikan nama kolom di DB 'purchase_price'
                'price'          => $hargaJual,
                'stock'          => 0, // Inisialisasi 0 dulu, nanti ditambah di bawah
                'description'    => '-',
            ]);
        } else {
            // === UPDATE HARGA JIKA ADA ===
            $updateData = [];
            if ($hargaBeli > 0) $updateData['purchase_price'] = $hargaBeli;
            if ($hargaJual > 0) $updateData['price'] = $hargaJual;

            if (!empty($updateData)) {
                $product->update($updateData);
            }
        }

        if ($stokAwal > 0) {

            // A. Update Master Stok Produk (INI YANG SEBELUMNYA HILANG)
            $product->increment('stock', $stokAwal);

            // B. Buat Supplier Default (Cek dulu biar gak duplikat terus)
            $supplier = Supplier::firstOrCreate(
                ['name' => 'Stok Awal Import'],
                [
                    'email'   => 'import@system.com',
                    'phone'   => '-',
                    'address' => 'Data Import Excel'
                ]
            );

            // C. Buat Nota Pembelian (Purchase)
            $purchase = Purchase::create([
                'supplier_id'    => $supplier->id,
                'date'           => now(),
                'invoice_number' => 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(4)),
                'status'         => 1, // Lunas
                // 'total_price'    => $stokAwal * $hargaBeli, // Opsional jika kolom ini ada
            ]);

            // D. Masukkan Detail Item (PurchaseItem)
            PurchaseItem::create([
                'purchase_id'   => $purchase->id,
                'product_id'    => $product->id,
                'quantity'      => $stokAwal,
                'sold_quantity' => 0,
                'unit_price'    => $hargaBeli,
                'expiry_date'   => null,
            ]);
        }

        return $product;
    }
}
