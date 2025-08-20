<?php

namespace App\Http\Controllers\Kasir;


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


class ProductKasirController extends Controller
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

        return view('kasir.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        // Cek apakah nama dan satuan sudah ada

        $nameExists = Product::where('name', $request->name)->exists();

        $exists = Product::where('name', $request->name)
            ->where('unit', $request->unit)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'name' => 'Nama produk dan satuan sudah terdaftar.',
                'unit' => 'Nama produk dan satuan sudah terdaftar.'
            ]);
        } elseif ($nameExists) {
            return back()->withInput()->withErrors([
                'name' => 'Nama produk sudah terdaftar.'
            ]);
        }

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
            'price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        // Cek apakah nama dan satuan sudah ada (kecuali produk yang sedang diupdate)
        $comboExists = Product::where('name', $request->name)
            ->where('unit', $request->unit)
            ->where('id', '!=', $product->id)
            ->exists();

        // Cek nama produk saja, kecuali produk yang sedang diupdate
        $nameExists = Product::where('name', $request->name)
            ->where('id', '!=', $product->id)
            ->exists();

        if ($comboExists) {
            return back()->withInput()->withErrors([
                'name' => 'Nama produk dan satuan sudah terdaftar.',
                'unit' => 'Nama produk dan satuan sudah terdaftar.'
            ]);
        } elseif ($nameExists) {
            return back()->withInput()->withErrors([
                'name' => 'Nama produk sudah terdaftar.'
            ]);
        }

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

        $soonExpiryDays = 30;
        $soonExpiryDate = now()->addDays($soonExpiryDays);

        $allProducts = Product::with(['category', 'purchaseItems'])->get();

        $filtered = $allProducts->filter(function ($product) use ($soonExpiryDate) {
            // Ambil batch yang expired/akan expired dan stoknya masih ada
            $batches = $product->purchaseItems->filter(function ($item) use ($soonExpiryDate) {
                $expiry = \Carbon\Carbon::parse($item->expiry_date);
                $available = ($item->quantity - $item->sold_quantity) > 0;

                // Sudah expired dan stok masih ada
                if ($expiry->lte(now()) && $available) {
                    return true;
                }
                // Akan expired (< 30 hari) dan stok masih ada
                if ($expiry->gt(now()) && $expiry->lte($soonExpiryDate) && $available) {
                    return true;
                }
                return false;
            });

            // Tampilkan produk hanya jika ada batch yang memenuhi kondisi di atas
            return $batches->count() > 0;
        })->values();

        // Manual paginate Collection
        $page = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $results = $filtered->slice(($page - 1) * $perPage, $perPage)->values();

        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $results,
            $filtered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('kasir.products.expired', compact('title', 'products', 'soonExpiryDays'));
    }



    public function available(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with([
                'category',
                'purchaseItems' => function ($query) {
                    $query->whereDate('expiry_date', '>', now())
                        ->whereColumn('quantity', '>', 'sold_quantity')
                        ->orWhereNull('expiry_date');
                }
            ])
                ->whereHas('purchaseItems', function ($query) {
                    $query->whereDate('expiry_date', '>', now())
                        ->whereColumn('quantity', '>', 'sold_quantity')
                        ->orWhereNull('expiry_date');
                });

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('category', fn($row) => $row->category->name ?? '-')
                ->addColumn('available_stock', function ($row) {
                    return $row->purchaseItems->sum(function ($item) {
                        return $item->quantity - $item->sold_quantity;
                    });
                })
                ->make(true);
        }

        $title = "Produk Tersedia";
        return view('kasir.products.available', compact('title'));
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

        return view('kasir.products.outstock', compact('title', 'products'));
    }

    // public function outstock(Request $request)
    // {
    //     $title = "Produk Habis";
    //     $products = Product::where('stock', '<=', 0)->with('category')->paginate(10);

    //     return view('admin.products.outstock', compact('title', 'products'));
    // }

    public function destroy(Product $product)
    {
        // Cek apakah semua batch produk sudah expired (expiry_date <= hari ini)
        $allExpired = $product->purchaseItems->count() > 0 &&
            $product->purchaseItems->every(function ($item) {
                return \Carbon\Carbon::parse($item->expiry_date)->lte(now());
            });

        if (!$allExpired) {
            return back()->with('error', 'Produk hanya bisa dihapus jika semua batch sudah kadaluarsa.');
        }

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
                ->map(function ($product) use ($startDate, $endDate) {
                    // Ambil batch yang belum expired DAN masuk di periode
                    $validItems = $product->purchaseItems()
                        ->whereDate('expiry_date', '>', now())
                        ->whereHas('purchase', function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    })
                        ->get();

                    $totalPurchased = $validItems->sum('quantity');
                    $totalSold = $validItems->sum('sold_quantity');

                    return [
                        'product_name' => $product->name,
                        'category_name' => $product->category->name ?? '-',
                        'unit' => $product->unit ?? '-',
                        'available_stock' => $totalPurchased - $totalSold
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
                        // Ganti 'quantity' dengan 'sold_quantity' jika memang field-nya itu
                        'total_quantity' => $items->sum('quantity') // atau $items->sum('quantity')
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

    public function datatable(Request $request)
    {
        $query = Product::with([
            'category',
            'purchaseItems' => function ($q) {
                $q->latest()->limit(1);
            }
        ]);

        // Filter kategori
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        // Filter unit
        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        // Pencarian
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $keywords = preg_split('/\s+/', trim($search));

            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where('name', 'like', "%{$word}%")
                        ->orWhere('unit', 'like', "%{$word}%")
                        ->orWhereRaw("CAST(price AS CHAR) LIKE ?", ["%{$word}%"])
                        ->orWhere('description', 'like', "%{$word}%")
                        ->orWhereHas('category', function ($qc) use ($word) {
                            $qc->whereRaw("TRIM(name) LIKE ?", ["%{$word}%"]);
                        })
                        ->orWhereHas('purchaseItems', function ($qp) use ($word) {
                            $qp->whereRaw("TRIM(unit_price) LIKE ?", ["%{$word}%"]);
                        });
                }
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('category', fn($row) => $row->category->name ?? '-')
            ->addColumn('unit_price', function ($row) {
                $latestPurchaseItem = $row->purchaseItems->first();
                return $latestPurchaseItem
                    ? 'Rp ' . number_format($latestPurchaseItem->unit_price, 0, ',', '.')
                    : '<span class="text-danger">produk belum dibeli</span>';
            })
            ->addColumn('price', function ($row) {
                return $row->price
                    ? 'Rp ' . number_format($row->price, 0, ',', '.')
                    : '<span class="text-danger">belum ada harga jual</span>';
            })
            ->addColumn('description', fn($row) => $row->description)
            ->rawColumns(['unit_price', 'price'])
            ->make(true);
    }



    public function deleteExpired()
    {
        // Ambil semua batch yang sudah expired dan stoknya masih ada
        $expiredItems = PurchaseItem::where('expiry_date', '<=', now())
            ->whereRaw('quantity - sold_quantity > 0')
            ->get();

        foreach ($expiredItems as $item) {
            // Jika ingin menghapus batch: hapus PurchaseItem
            $item->delete();
            // Jika ingin menghapus produk jika semua batch-nya sudah dihapus, tambahkan logika di sini
        }

        return back()->with('success', 'Semua produk kadaluarsa berhasil dihapus.');
    }

}
