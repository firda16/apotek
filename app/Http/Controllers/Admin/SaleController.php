<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Events\PurchaseOutStock;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::with(['saleItems.product.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $items = SaleItem::all();
        $products = Product::all();
        $category = Category::all();

        return view('admin.sales.index', compact('sales', 'items', 'products', 'category'));
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
        $request->validate([
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0',
            'queue_number' => 'required|string',
            'payment_method' => 'required|string',
            'customer_id' => 'nullable|name,phone,id',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'queue_number' => $request->queue_number,
                'payment_method' => $request->payment_method,
                'discount' => $request->discount ?? 0,
                'total_price' => 0, // Diupdate setelah hitung
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
                    'customer_id' => $request->customer_id,
                ]);

                $productModel = Product::find($product['product_id']);
                $productModel->decrement('stock', $product['quantity']);

                if ($productModel->stock <= 1) {
                    event(new PurchaseOutStock($productModel));
                }
            }

            $sale->update(['total_price' => $total]);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Penjualan berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan penjualan: ' . $e->getMessage());
        }
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
