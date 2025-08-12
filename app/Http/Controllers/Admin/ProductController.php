<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Yajra\DataTables\Facades\DataTables;
use QCod\AppSettings\Setting\AppSettings;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockReportExport;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        App::setLocale('id');

        $query = Product::with([
            'category',
            'purchaseItems' => function ($query) {
                $query->whereDate('expiry_date', '>', now());
            }
        ])->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(15);

        return view('admin.products.index', compact('products'));
    }


    public function create()
    {
        $title = 'Tambah Produk';
        $categories = Category::all();
        $purchases = Purchase::get();
        $products = Product::with('category')->get();

        return view('admin.products.create', compact('title', 'categories', 'purchases', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $price = $request->price;
        if ($request->discount && $request->discount > 0) {
            $price = $price - ($request->discount * $price);
        }

        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'price' => $price,
            'discount' => $request->discount,
            'description' => $request->description,
        ]);

        return redirect()->route('products.index')->with(notify("Produk berhasil ditambahkan"));
    }

    public function edit(Product $product)
    {
        $title = 'Edit Produk';
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $price = $request->price;
        if ($request->discount && $request->discount > 0) {
            $price = $price - ($request->discount * $price);
        }

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'price' => $price,
            'discount' => $request->discount,
            'description' => $request->description,
        ]);

        return redirect()->route('products.index')->with(notify("Produk berhasil diperbarui"));
    }

    public function expired()
    {
        $title = 'Produk Kedaluwarsa';
        App::setLocale('id');

        // Tentukan batas "akan kadaluarsa": misal 30 hari ke depan
        $soonExpiryDays = 30;
        $soonExpiryDate = now()->addDays($soonExpiryDays);

        $products = Product::whereHas('purchaseItems', function ($query) use ($soonExpiryDate) {
            $query->whereDate('expiry_date', '<=', $soonExpiryDate); // termasuk yang sudah expired dan akan expired
        })
            ->with([
                'purchaseItems' => function ($query) use ($soonExpiryDate) {
                    $query->whereDate('expiry_date', '<=', $soonExpiryDate);
                },
                'category'
            ])
            ->paginate(10);

        return view('admin.products.expired', compact('title', 'products', 'soonExpiryDays'));
    }



    public function available(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'purchaseItems' => function ($query) {
                $query->whereDate('expiry_date', '>', now())
                    ->whereColumn('quantity', '>', 'sold_quantity');
            }])
                ->whereHas('purchaseItems', function ($query) {
                    $query->whereDate('expiry_date', '>', now())
                        ->whereColumn('quantity', '>', 'sold_quantity');
                });

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('category', fn($row) => $row->category->name ?? '-')
                ->addColumn('available_stock', function ($row) {
                    return $row->purchaseItems->sum(function ($item) {
                        return $item->quantity - $item->sold_quantity;
                    });
                })
                ->addColumn('action', function ($row) {                   
                    $edit = '<a href="' . route('products.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>';
                    $delete = '<form action="' . route('products.destroy', $row->id) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin hapus?\')">Hapus</button>
                            </form>';
                    return $edit . ' ' . $delete;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $title = "Produk Tersedia";
        return view('admin.products.available', compact('title'));
    }




    public function outstock(Request $request)
    {
        $title = "Produk Habis";

        // Ambil semua produk dan relasinya
        $allProducts = Product::with(['category', 'purchaseItems'])->get();

        // Filter produk yang stoknya habis (FIFO aware)
        // $filtered = $allProducts->filter(function ($product) {
        //     return $product->available_stock <= 0;
        // });

        $filtered = $allProducts->filter(function ($product) {
            // Ambil hanya batch yang belum expired
            $unexpiredBatches = $product->purchaseItems->where('expiry_date', '>', now());

            // Jika tidak ada batch belum expired, jangan tampilkan
            if ($unexpiredBatches->isEmpty()) {
                return false;
            }

            // Hitung total stok dari batch belum expired
            $availableStock = $unexpiredBatches->sum(fn($item) => $item->quantity - $item->sold_quantity);

            // Tampilkan hanya jika stok habis
            return $availableStock <= 0;
        });


        // Manual paginate Collection
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $results = $filtered->slice(($page - 1) * $perPage, $perPage)->values();

        $products = new LengthAwarePaginator(
            $results,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.products.outstock', compact('title', 'products'));
    }

    // public function outstock(Request $request)
    // {
    //     $title = "Produk Habis";
    //     $products = Product::where('stock', '<=', 0)->with('category')->paginate(10);

    //     return view('admin.products.outstock', compact('title', 'products'));
    // }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with(notify('Produk berhasil dihapus'));
    }

    public function stockLog(Product $product)
    {
        $batches = $product->purchaseItems()
            ->orderBy('expiry_date')
            ->get();

        return view('admin.products.stock_log', compact('product', 'batches'));
    }


    public function stockReport(Request $request)
    {
        $currentStock = collect();
        $stockIn = collect();
        $stockInSummary = collect();
        $stockOut = collect();
        $stockOutSummary = collect();

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $startDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();

            /**
             * 1. Stok Saat Ini
             *    Hitung manual = stok masuk - stok keluar
             */
            $currentStock = Product::with('category')
                ->get()
                ->map(function ($product) {
                    $stockIn = PurchaseItem::where('product_id', $product->id)->sum('quantity');
                    $stockOut = SaleItem::where('product_id', $product->id)->sum('quantity');

                    return [
                        'product_name' => $product->name,
                        'category_name' => $product->category->name ?? '-',
                        'unit' => $product->unit ?? '-',
                        'available_stock' => $stockIn - $stockOut
                    ];
                });

            /**
             * 2. Stok Masuk
             */
            $stockIn = PurchaseItem::with(['product.category', 'purchase'])
                ->whereHas('purchase', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            $stockInSummary = $stockIn
                ->groupBy('product_id')
                ->map(function ($items) {
                    return [
                        'product_name' => $items->first()->product->name ?? '-',
                        'category_name' => $items->first()->product->category->name ?? '-',
                        'unit' => $items->first()->product->unit ?? '-',
                        'total_quantity' => $items->sum('quantity')
                    ];
                })
                ->values();

            /**
             * 3. Stok Keluar
             */
            $stockOut = SaleItem::with(['product.category', 'sale'])
                ->whereHas('sale', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            $stockOutSummary = $stockOut
                ->groupBy('product_id')
                ->map(function ($items) {
                    return [
                        'product_name' => $items->first()->product->name ?? '-',
                        'category_name' => $items->first()->product->category->name ?? '-',
                        'unit' => $items->first()->product->unit ?? '-',
                        'total_quantity' => $items->sum('quantity')
                    ];
                })
                ->values();
        }

        return view('admin.products.reports', compact(
            'currentStock',
            'stockIn',
            'stockInSummary',
            'stockOut',
            'stockOutSummary'
        ));
    }
}
