<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $title = 'Tambah Produk';
        $categories = Category::all();
        $products = Product::with('category')->get();

        return view('admin.products.create', compact('title', 'categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit'        => 'required|string|max:50',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $price = $request->price;
        if ($request->discount && $request->discount > 0) {
            $price = $price - ($request->discount * $price);
        }

        Product::create([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'unit'        => $request->unit,
            'stock'       => $request->stock,
            'price'       => $price,
            'discount'    => $request->discount,
            'description' => $request->description,
        ]);

        return redirect()->route('products.index')->with(notify("Produk berhasil ditambahkan"));
    }

    public function edit(Product $product)
    {
        $title = 'Edit Produk';
        $categories = Category::all();
        return view('admin.products.edit', compact('title', 'product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit'        => 'required|string|max:50',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $price = $request->price;
        if ($request->discount && $request->discount > 0) {
            $price = $price - ($request->discount * $price);
        }

        $product->update([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'unit'        => $request->unit,
            'stock'       => $request->stock,
            'price'       => $price,
            'discount'    => $request->discount,
            'description' => $request->description,
        ]);

        return redirect()->route('products.index')->with(notify("Produk berhasil diperbarui"));
    }

    public function expired()
    {
        $title = 'Produk Kedaluwarsa';
        $products = Product::whereHas('purchaseItems', function ($query) {
            $query->whereDate('expiry_date', '<=', now());
        })->with(['purchaseItems' => function ($query) {
            $query->whereDate('expiry_date', '<=', now());
        }])->paginate(10);

        return view('admin.products.expired', compact('title', 'products'));
    }

    public function available(Request $request)
    {
        $title = "Produk Tersedia";
        $products = Product::where('stock', '>', 0)->with('category')->paginate(10);

        return view('admin.products.available', compact('title', 'products'));
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
