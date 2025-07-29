<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use App\Events\PurchaseOutStock;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::with(['customer', 'saleItems'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);


        $items = SaleItem::all();
        // $products = Product::all();
        // $category = Category::all();

        return view('admin.sales.index', compact('sales', 'items'));
    }

    public function create()
    {
        $title = 'create sales';
        $products = Product::all();
        $categories = Category::all();

        return view('admin.sales.create', compact('title', 'products', 'categories'));
    }


    public function store(Request $request)
    {
    // 1. Validasi Input
    // Perhatikan perubahan:
    // - 'nama_customer' dan 'nomor_telepon' dibuat 'required' karena ini adalah informasi pelanggan utama.
    // - 'sale_items' adalah array, jadi kita validasi isinya.
    // - 'sale_items.*.nama_produk' diubah menjadi 'product_id' agar sesuai dengan database (asumsi Anda menyimpan ID produk).
    // - 'sale_items.*.quantity' dan 'sale_items.*.unit_price' dibuat 'required' jika produk dipilih.
    // - 'sale_items.*.total_price' sekarang 'nullable' jika memang dihitung di backend atau 'required' jika Anda ingin mempercayai frontend.
    // - 'total_price' (untuk keseluruhan penjualan) dihapus dari validasi request karena seharusnya dihitung di backend.
    // - 'discount' dan 'payment_method' tetap 'nullable' atau bisa juga 'required' tergantung kebutuhan bisnis.

    $request->validate([
        'nama_customer' => 'required|string|max:255',
        'nomor_telepon' => 'required|string|max:20',
        'sale_items' => 'required|array|min:1', // Pastikan ada setidaknya satu item penjualan
        'sale_items.*.nama_produk' => 'required|exists:products,id', // Harus ada dan produknya ada di DB
        'sale_items.*.quantity' => 'required|numeric|min:1',
        'sale_items.*.unit_price' => 'nullable|numeric|min:0',
        // 'sale_items.*.total_price' => 'nullable|numeric|min:0', // Ini dihapus karena kita akan menghitung ulang di backend
        'discount' => 'nullable|numeric|min:0|max:100', // Diskon dalam persen
        'payment_method' => 'required|string|max:50', // Metode pembayaran harus diisi
    ]);

    
    // 2. Hitung Total Harga Keseluruhan (DI BACKEND)
    $overall_total_price = 0;
    foreach ($request->sale_items as $item) {
        $quantity = (float) $item['quantity'];
        $unit_price = (float) $item['unit_price'];
        $overall_total_price += ($quantity * $unit_price);
    }
    
    // Terapkan diskon jika ada
    $discount_percentage = (float) $request->discount ?? 0;
    if ($discount_percentage > 0) {
        $overall_total_price = $overall_total_price * (1 - ($discount_percentage / 100));
    }
    
    $customer = Customer::create([
       'nama' => $request->nama_customer,
       'telepon' => $request->nomor_telepon,
   ]);
    // 3. Buat Entri Sale (Penjualan Utama)
    $sale = Sale::create([
        'customer_id' => $customer->id,
        'payment_method' => $request->payment_method,
        'discount' => $discount_percentage, // Simpan diskon dalam persen
        'total_price' => $overall_total_price, // Total harga setelah diskon
    ]);

    // 4. Buat Entri SaleItem (Detail Produk)
    // Sekarang kita mengiterasi array 'sale_items' yang benar
    foreach ($request->sale_items as $item) {
        $item_quantity = (float) $item['quantity'];
        $item_unit_price = (float) $item['unit_price'];
        $item_subtotal = $item_quantity * $item_unit_price; // Subtotal per item

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $item['nama_produk'], // Sesuaikan dengan 'nama_produk' dari request
            'quantity' => $item_quantity,
            'unit_price' => $item_unit_price,
            'discount' => 0, // Jika diskon hanya global, set 0 di sini.
                            // Jika ada diskon per item, Anda perlu tambahkan input di HTML dan validasi di sini.
            // 'total_price' => $item_subtotal, // Subtotal per item
        ]);
    }

    // 5. Redirect atau kembalikan response sukses
    return redirect()->route('sales.index')->with('success', 'Penjualan berhasil ditambahkan!');
    }

    public function edit(Sale $sale)
    {
        $title = 'edit sale';
        $products = Product::all();
        $categories = Category::all();
        $sale->load('saleItems.product.category');

        return view('admin.sales.edit', compact('title', 'sale', 'products', 'categories'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Kembalikan stok lama
            foreach ($sale->saleItems as $item) {
                Product::find($item->product_id)->increment('stock', $item->quantity);
            }

            // Hapus item lama
            $sale->saleItems()->delete();

            // Update data sale
            $sale->update([
                'payment_method' => $request->payment_method,
                'discount' => $request->discount ?? 0,
            ]);

            $total = 0;

            foreach ($request->products as $product) {
                $subtotal = ($product['unit_price'] * $product['quantity']) - ($product['discount'] ?? 0);
                $total += $subtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'unit' => $product['unit'] ?? null,
                    'unit_price' => $product['unit_price'],
                    'discount' => $product['discount'] ?? 0,
                    'total_price' => $subtotal,
                ]);

                $productModel = Product::find($product['product_id']);
                $productModel->decrement('stock', $product['quantity']);

                if ($productModel->stock <= 1) {
                    event(new PurchaseOutStock($productModel));
                }
            }

            $sale->update(['total_price' => $total]);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Penjualan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui penjualan: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $sale = Sale::findOrFail($request->id);
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus.');
    }

    public function reports(Request $request)
    {
        $title = 'sales reports';
        return view('admin.sales.reports', compact('title'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        $sales = Sale::whereBetween(DB::raw('DATE(created_at)'), [$request->from_date, $request->to_date])->get();
        $title = 'sales reports';

        return view('admin.sales.reports', compact('sales', 'title'));
    }
}
