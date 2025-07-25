<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Container\Attributes\Log;
use QCod\AppSettings\Setting\AppSettings;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::query()->with([
            'supplier',
            'purchaseItems.product.category'
        ]);

        $items = PurchaseItem::query();
        $products = Product::get();
        $category = Category::get();
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->orWhereHas('purchaseItems', function ($q_item) use ($searchTerm) {
                    $q_item->whereHas('product', function ($q_prod) use ($searchTerm) {
                        $q_prod->where('name', 'like', '%' . $searchTerm . '%');
                    });
                })
                    ->orWhereHas('purchaseItems.product.category', function ($q_cat) use ($searchTerm) {
                        $q_cat->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('supplier', function ($q_sup) use ($searchTerm) {
                        $q_sup->where('name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }


        // $pembelians = $query->get();
        $pembelians = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.purchases.index', compact('pembelians', 'items', 'products', 'category'));
    }

    public function create()
    {
        $title = 'create purchase';
        $categories = Category::get();
        $suppliers = Supplier::get();
        $products = Product::all();
        return view('admin.purchases.create', compact('title', 'categories', 'suppliers', 'products'));
    }

    public function store(Request $request)
    {
        // Debugging: Lihat semua input yang diterima
        // dd($request->all());

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            // Validasi untuk setiap item produk dalam array 'products'
            'products.*.product_id' => 'nullable|exists:products,id', // product_id bisa null jika ingin menambah produk baru
            // 'products.*.product_name' => 'required_without:products.*.product_id|string|max:255', // Nama produk wajib jika product_id tidak ada
            'products.*.category_id' => 'required|exists:categories,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.expiry_date' => 'nullable|date',
            'products.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Untuk validasi gambar
            'payment_method' => 'required|string|in:Tunai,Transfer,QRIS,Ewallet', // Validasi payment method
        ]);

        DB::beginTransaction();

        try {
            // Buat entri pembelian utama
            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'payment_method' => $request->payment_method,
                'total_price' => 0, // karena field-nya di tabel purchases = total_price

            ]);

            $total = 0;

            // Loop produk yang dibeli
            foreach ($request->products as $index => $item) {
                $product = null;

                // Cek apakah product_id disediakan (produk yang sudah ada)
                if (isset($item['product_id']) && !empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                } else {
                    // Jika product_id tidak disediakan, buat produk baru
                    $product = new Product();
                    // $product->nama_produk = $item['product_name'];
                    $product->category_id = $item['category_id'];
                    $product->stock = 0; // Stok awal akan ditambahkan nanti
                    $product->unit_price = $item['unit_price']; // Harga jual produk
                    $product->save(); // Simpan produk baru untuk mendapatkan ID-nya
                }

                // Handle image upload jika ada
                $imagePath = null;
                if ($request->hasFile("products.{$index}.image")) {
                    $image = $request->file("products.{$index}.image");
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = $image->storeAs('public/products', $imageName); // Simpan gambar di storage/app/public/products
                    $imagePath = Storage::url($imagePath); // Dapatkan URL yang dapat diakses publik
                }

                $subtotal = $item['quantity'] * $item['unit_price'];
                $total += $subtotal;

                // Simpan item pembelian ke tabel purchase_items
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id, // Gunakan ID produk yang sudah ada atau yang baru dibuat
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'], // Ini adalah harga beli per unit
                    'total_price' => $subtotal,
                    'expiry_date' => $item['expiry_date'] ?? null, // Tambahkan expiry_date
                    // 'image' => $imagePath, // Kolom gambar biasanya di tabel produk, bukan purchase_items.
                    // Jika Anda ingin menyimpan gambar per item pembelian, pastikan tabel purchase_items memiliki kolom 'image'.
                    // Untuk saat ini, asumsikan gambar terkait dengan produk itu sendiri.
                ]);

                // Tambahkan stok produk yang sudah ada atau yang baru dibuat
                $product->increment('stock', $item['quantity']);
                // Jika Anda ingin memperbarui harga jual produk berdasarkan harga beli terbaru:
                // $product->update(['unit_price' => $item['unit_price']]);
            }

            // Update total pembelian di tabel purchases
            $purchase->update(['total_price' => $total]);

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging purposes
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pembelian: ' . $e->getMessage());
        }
    }


    public function edit(Purchase $purchase)
    {
        $title = 'edit purchase';
        $categories = Category::get();
        $suppliers = Supplier::get();
        $products = Product::get(); // ✅ Tambahkan ini
        $purchase->load('purchaseItems.product');

        return view('admin.purchases.edit', compact('title', 'purchase', 'categories', 'suppliers',  'products'));
    }


    public function update(Request $request, Purchase $purchase)
    {

        //dd($request->all());

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_method' => 'required|string',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.expiry_date' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'payment_method' => $request->payment_method,
            ]);

            $total = 0;

            // Kembalikan stok lama sebelum update
            foreach ($purchase->purchaseItems as $item) {
                Product::find($item->product_id)->decrement('stock', $item->quantity);
            }

            // Hapus item lama
            $purchase->purchaseItems()->delete();

            foreach ($request->products as $productData) {
                $subtotal = $productData['unit_price'] * $productData['quantity'];
                $total += $subtotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'total_price' => $subtotal,
                    'expiry_date' => $productData['expiry_date'] ?? null,
                ]);

                // Tambahkan stok baru
                Product::find($productData['product_id'])->increment('stock', $productData['quantity']);
            }

            $purchase->update(['total_price' => $total]);

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui pembelian: ' . $e->getMessage());
        }
    }



    public function reports()
    {
        $title = 'purchase reports';
        return view('admin.purchases.reports', compact('title'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date'
        ]);

        $title = 'purchases reports';
        $purchases = Purchase::whereBetween(DB::raw('DATE(created_at)'), [$request->from_date, $request->to_date])->get();
        return view('admin.purchases.reports', compact('purchases', 'title'));
    }

    public function destroy(Request $request, Purchase $purchase)
    {
        foreach ($purchase->purchaseItems as $item) {
            Product::find($item->product_id)->decrement('stock', $item->quantity);
        }
        return redirect()->route('purchases.index')->with('success', 'Data pembelian berhasil dihapus.');
    }
}
