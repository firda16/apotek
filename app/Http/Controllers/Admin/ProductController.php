<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use QCod\AppSettings\Setting\AppSettings;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        App::setLocale('id');
        //    $products = Product::get();
        $query = Product::query()->with(['purchase.category', 'purchaseItems']);
        $products = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.products.index', compact(
            'products'
        ));
    }

        public function create()
    {
        $title = 'add product';
        $purchases = Purchase::get();
        $products = Product::get();
        return view('admin.products.create', compact(
            'title',
            'purchases',
            'products' // Pass the fetched products to the view
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit'        => 'required|string|max:50',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        Product::create([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'unit'        => $request->unit,
            'stock'       => $request->stock,
            'price'       => $request->price,
            'description' => $request->description,
        ]);

        return redirect()->route('products.index')->with(notify("Produk berhasil ditambahkan"));
    }

        public function edit(Product $product)
    {
        $title = 'edit product';
        $purchases = Purchase::get();
        return view('admin.products.edit', compact(
            'title',
            'product',
            'purchases'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit'        => 'required|string|max:50',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $product->update([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'unit'        => $request->unit,
            'stock'       => $request->stock,
            'price'       => $request->price,
            'description' => $request->description,
        ]);

        return redirect()->route('products.index')->with(notify("Produk berhasil diperbarui"));
    }

    /**
     * Display a listing of expired resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
  public function expired()
{
    $title = 'Produk Kedaluwarsa';
    App::setLocale('id');

    $products = Product::whereHas('purchaseItems', function ($query) {
            $query->whereDate('expiry_date', '<=', now());
        })
        ->with(['purchaseItems' => function ($query) {
            $query->whereDate('expiry_date', '<=', now());
        }])
        ->paginate(10);

    return view('admin.products.expired', compact('title', 'products'));
}


    /**
     * Display a listing of out of stock resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function available(Request $request)
    {
        $products = Product::where('stock', '>', 0)->with('category')->paginate(10);
        return view('admin.products.available', [
            'title'    => 'Produk Tersedia',
            'products' => $products,
        ]);
    }

    public function outstock(Request $request)
    {
        $title = "Produk Habis";
        $products = Product::where('stock', '<=', 0)->with('category')->paginate(10);

        return view('admin.products.outstock', compact('title', 'products'));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with(notify('Produk berhasil dihapus'));
    }
}
