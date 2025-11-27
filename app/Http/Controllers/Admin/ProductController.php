<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\SaleItem;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\StockReportExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use Yajra\DataTables\Facades\DataTables;
use QCod\AppSettings\Setting\AppSettings;
use Illuminate\Pagination\LengthAwarePaginator;


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
            'price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'product_code' => 'required|string|unique:products,product_code',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        // 2. LOGIKA UPLOAD GAMBAR (BARU)
        $imageName = null;
        if ($request->hasFile('image')) {
            // Buat nama file unik: time() + ekstensi asli
            $imageName = time() . '.' . $request->image->extension();

            // Simpan ke folder public/uploads/products
            // Pastikan folder ini ada, atau dia akan otomatis dibuat
            $request->image->move(public_path('uploads/products'), $imageName);
        }

        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'price' => $price,
            'discount' => $request->discount,
            'description' => $request->description,
            'product_code' => $request->product_code,
            'image' => $imageName, // Simpan nama filenya saja (atau null)
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
            'product_code' => 'required|string|unique:products,product_code,' . $product->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        // 1. LOGIKA GANTI GAMBAR (BARU)
        $imageName = $product->image; // Pakai gambar lama dulu sebagai default
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada (agar server tidak penuh)
            if ($product->image && file_exists(public_path('uploads/products/' . $product->image))) {
                unlink(public_path('uploads/products/' . $product->image));
            }

            // Upload gambar baru
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/products'), $imageName);
        }

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'unit' => $request->unit,
            'price' => $price,
            'discount' => $request->discount,
            'description' => $request->description,
            'product_code' => $request->product_code,
            'image' => $imageName,
        ]);

        return redirect()->route('products.index')->with(notify("Produk berhasil diperbarui"));
    }

    public function expired()
    {
        $title = 'Produk Kedaluwarsa';
        $soonExpiryDays = 30;

        return view('admin.products.expired', compact('title', 'soonExpiryDays'));
    }

    public function expiredDatatable(Request $request)
    {
        $soonExpiryDays = 30;
        $soonExpiryDate = now()->addDays($soonExpiryDays);
        App::setLocale('id');

        $allProducts = Product::with(['category', 'purchaseItems'])->get();

        // Lakukan filter awal pada koleksi produk
        $filteredProducts = $allProducts->filter(function ($product) use ($soonExpiryDate, $soonExpiryDays) {
            $batches = $product->purchaseItems->filter(function ($item) use ($soonExpiryDate) {
                if (is_null($item->expiry_date)) {
                    return false;
                }
                $expiry = \Carbon\Carbon::parse($item->expiry_date);
                $available = ($item->quantity - $item->sold_quantity) > 0;
                return (
                    ($expiry->lte(now()) && $available) ||
                    ($expiry->gt(now()) && $expiry->lte($soonExpiryDate) && $available)
                );
            });
            return $batches->count() > 0;
        });

        // Tangani filter dari permintaan DataTables
        if ($request->has('columns.6.search.value') && $request->input('columns.6.search.value') != '') {
            $filterValue = $request->input('columns.6.search.value');

            // Memfilter koleksi produk berdasarkan nilai status
            $filteredProducts = $filteredProducts->filter(function ($row) use ($filterValue, $soonExpiryDays) {
                // Logika untuk menentukan status setiap produk
                $relevant = $row->purchaseItems->filter(function ($item) use ($soonExpiryDays) {
                    $expiry = \Carbon\Carbon::parse($item->expiry_date);
                    $available = ($item->quantity - $item->sold_quantity) > 0;
                    return (
                        ($expiry->lte(now()) && $available) ||
                        ($expiry->gt(now()) && $expiry->lte(now()->copy()->addDays($soonExpiryDays)) && $available)
                    );
                })->sortBy('expiry_date');

                $closest = $relevant->first();
                $actualStatus = '';

                if (!$closest) {
                    $actualStatus = 'Aktif';
                } else {
                    $expiry = \Carbon\Carbon::parse($closest->expiry_date);
                    if ($expiry->lt(now())) {
                        $actualStatus = 'Sudah Kadaluarsa';
                    } elseif ($expiry->lte(now()->copy()->addDays($soonExpiryDays))) {
                        $actualStatus = 'Akan Kadaluarsa';
                    }
                }
                return $actualStatus === $filterValue;
            });
        }

        // Buat objek Datatables dari koleksi yang sudah difilter
        return DataTables::of($filteredProducts)
            ->addIndexColumn()
            ->addColumn('category', fn($row) => $row->category->name ?? '-')
            ->addColumn('price', fn($row) => (settings('app_currency') ?? 'Rp') . ' ' . number_format($row->price, 0, ',', '.'))
            ->addColumn('quantity', function ($row) use ($soonExpiryDays) {
                $relevant = $row->purchaseItems->filter(function ($item) use ($soonExpiryDays) {
                    $expiry = \Carbon\Carbon::parse($item->expiry_date);
                    $available = ($item->quantity - $item->sold_quantity) > 0;
                    return (
                        ($expiry->lte(now()) && $available) ||
                        ($expiry->gt(now()) && $expiry->lte(now()->copy()->addDays($soonExpiryDays)) && $available)
                    );
                })->sortBy('expiry_date');

                $closest = $relevant->first();
                return $closest ? $closest->quantity - $closest->sold_quantity : 0;
            })
            ->addColumn('expiry_date', function ($row) use ($soonExpiryDays) {
                $relevant = $row->purchaseItems->filter(function ($item) use ($soonExpiryDays) {
                    $expiry = \Carbon\Carbon::parse($item->expiry_date);
                    $available = ($item->quantity - $item->sold_quantity) > 0;
                    return (
                        ($expiry->lte(now()) && $available) ||
                        ($expiry->gt(now()) && $expiry->lte(now()->copy()->addDays($soonExpiryDays)) && $available)
                    );
                })->sortBy('expiry_date');

                $closest = $relevant->first();
                return $closest ? \Carbon\Carbon::parse($closest->expiry_date)->translatedFormat('d F Y') : '-';
            })
            ->addColumn('status', function ($row) use ($soonExpiryDays) {
                $relevant = $row->purchaseItems->filter(function ($item) use ($soonExpiryDays) {
                    $expiry = \Carbon\Carbon::parse($item->expiry_date);
                    $available = ($item->quantity - $item->sold_quantity) > 0;
                    return (
                        ($expiry->lte(now()) && $available) ||
                        ($expiry->gt(now()) && $expiry->lte(now()->copy()->addDays($soonExpiryDays)) && $available)
                    );
                })->sortBy('expiry_date');

                $closest = $relevant->first();
                if (!$closest)
                    return '<span class="badge bg-secondary">Aktif</span>';

                $expiry = \Carbon\Carbon::parse($closest->expiry_date);

                if ($expiry->lt(now())) {
                    return '<span class="badge bg-danger text-white">Sudah Kadaluarsa</span>';
                } elseif ($expiry->lte(now()->copy()->addDays($soonExpiryDays))) {
                    return '<span class="badge bg-warning text-dark">Akan Kadaluarsa</span>';
                }
                return '<span class="badge bg-secondary">Aktif</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    // Fungsi pembantu untuk mendapatkan status HTML
    private function getStatusHtml($row, $soonExpiryDays)
    {
        $relevant = $row->purchaseItems->filter(function ($item) use ($soonExpiryDays) {
            $expiry = \Carbon\Carbon::parse($item->expiry_date);
            $available = ($item->quantity - $item->sold_quantity) > 0;
            return (
                ($expiry->lte(now()) && $available) ||
                ($expiry->gt(now()) && $expiry->lte(now()->copy()->addDays($soonExpiryDays)) && $available)
            );
        })->sortBy('expiry_date');

        $closest = $relevant->first();
        if (!$closest) {
            return '<span class="badge bg-secondary">Aktif</span>';
        }

        $expiry = \Carbon\Carbon::parse($closest->expiry_date);

        if ($expiry->lt(now())) {
            return '<span class="badge bg-danger text-white">Sudah Kadaluarsa</span>';
        } elseif ($expiry->lte(now()->copy()->addDays($soonExpiryDays))) {
            return '<span class="badge bg-warning text-dark">Akan Kadaluarsa</span>';
        }
        return '<span class="badge bg-secondary">Aktif</span>';
    }




    public function available(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with([
                'category',
                'purchaseItems' => function ($query) {
                    $query->whereColumn('quantity', '>', 'sold_quantity')
                        ->where(function ($q) {
                            $q->whereDate('expiry_date', '>', now())
                                ->orWhereNull('expiry_date');
                        });
                }
            ])
                ->whereHas('purchaseItems', function ($query) {
                    $query->whereColumn('quantity', '>', 'sold_quantity')
                        ->where(function ($q) {
                            $q->whereDate('expiry_date', '>', now())
                                ->orWhereNull('expiry_date');
                        });
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
        $title = "Produk Stok Habis";
        return view('admin.products.outstock', compact('title'));
    }

    public function outstockDatatable(Request $request)
    {
        $products = Product::outOfStock()->with('category')->get();

        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('category', fn($row) => $row->category->name ?? '-')
            ->addColumn('stok', function ($row) {
                // total stok semua batch valid
                return $row->purchaseItems
                    ->where('expiry_date', '>', now())
                    ->sum(fn($item) => $item->quantity - $item->sold_quantity);
            })
            ->make(true);
    }






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
                $q->latest()->limit(1); // Ambil purchase item terbaru
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
                        ->orWhere('product_code', 'like', "%{$word}%") // <-- TAMBAH PENCARIAN KODE
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
            // 1. KOLOM GAMBAR (BARU)
        ->addColumn('image', function ($row) {
            $url = $row->image ? asset('uploads/products/' . $row->image) : asset('assets/img/medicine_no_picture.jpg');
            return '<img src="' . $url . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; border:1px solid #eee;">';
        })

        // 2. KOLOM KODE PRODUK (BARU)
        ->addColumn('product_code', function ($row) {
            return $row->product_code ?? '-';
        })
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
            ->addColumn('action', function ($row) {
                $edit = '<a href="' . route('products.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>';
                $delete = '<form action="' . route('products.destroy', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin ingin menghapus produk ini?\')">Hapus</button>
                    </form>';
                return $edit . ' ' . $delete;
            })
            ->rawColumns(['image', 'unit_price', 'price', 'action'])
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


    public function stockReportPdf(Request $request)
    {
        $currentStock = collect();
        $stockIn = collect();
        $stockInSummary = collect();
        $stockOut = collect();
        $stockOutSummary = collect();

        $tanggalMulai = $request->from_date ? Carbon::parse($request->from_date)->format('d-m-Y') : '-';
        $tanggalSelesai = $request->to_date ? Carbon::parse($request->to_date)->format('d-m-Y') : '-';

        $type = $request->get('type'); // null kalau export keseluruhan

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $startDate = Carbon::parse($request->from_date)->startOfDay();
            $endDate = Carbon::parse($request->to_date)->endOfDay();

            // --- ambil data sama kayak stockReport() ---
            $currentStock = Product::with('category')
                ->get()
                ->map(function ($product) use ($startDate, $endDate) {
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

            $stockIn = PurchaseItem::with(['product.category', 'purchase'])
                ->whereHas('purchase', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            $stockOut = SaleItem::with(['product.category', 'sale'])
                ->whereHas('sale', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            // summary stok masuk
            $stockInSummary = $stockIn->groupBy('product_id')->map(function ($items) {
                return [
                    'product_name' => $items->first()->product->name ?? '-',
                    'category_name' => $items->first()->product->category->name ?? '-',
                    'unit' => $items->first()->product->unit ?? '-',
                    'total_quantity' => $items->sum('quantity')
                ];
            })->values();

            // summary stok keluar
            $stockOutSummary = $stockOut->groupBy('product_id')->map(function ($items) {
                return [
                    'product_name' => $items->first()->product->name ?? '-',
                    'category_name' => $items->first()->product->category->name ?? '-',
                    'unit' => $items->first()->product->unit ?? '-',
                    'total_quantity' => $items->sum('quantity')
                ];
            })->values();
        }

        // --- bedakan template ---
        if ($type) {
            // export salah satu (current, in, out)
            $pdf = Pdf::loadView('admin.products.reports_pdf_single', compact(
                'tanggalMulai',
                'tanggalSelesai',
                'type',
                'currentStock',
                'stockIn',
                'stockOut'
            ))->setPaper('A4', 'portrait');
        } else {
            // export full (gabungan)
            $pdf = Pdf::loadView('admin.products.reports_pdf', compact(
                'tanggalMulai',
                'tanggalSelesai',
                'currentStock',
                'stockIn',
                'stockOut',
                'stockInSummary',
                'stockOutSummary'
            ))->setPaper('A4', 'portrait');
        }

        return $pdf->stream("laporan-stok-" . ($type ?? 'all') . ".pdf");
    }
    public function import(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            // Jalankan proses import
            Excel::import(new ProductImport, $request->file('file'));

            // Notifikasi sukses
            return back()->with('success', 'Import Berhasil! Data ganda dilewati, data baru masuk ke "Tanpa Kategori".');
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal import: ' . $e->getMessage()]);
        }
    }

    public function destroyAll()
    {
        try {
            // 1. Matikan pengecekan kunci asing (Foreign Key) biar bisa hapus paksa
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // 2. HAPUS SEMUA DATA SAMPAI AKAR-AKARNYA
            // Urutan menghapus biar aman dan bersih

            // A. Hapus Data Transaksi (Stok Masuk & Keluar)
            PurchaseItem::truncate();  // Detail stok masuk
            Purchase::truncate();      // Nota pembelian
            SaleItem::truncate();      // Detail stok keluar
            // Sale::truncate();       // Nota penjualan (Aktifkan jika model Sale ada)

            // B. Hapus Data Master (Produk & Kategori)
            Product::truncate();       // Data Produk
            Category::truncate();      // Data Kategori

            // 3. Buat ulang kategori default "Tanpa Kategori" (Penting buat import)
            Category::create(['name' => 'Tanpa Kategori']);

            // 4. Nyalakan lagi pengecekan kunci asing
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return back()->with('success', 'RESET TOTAL BERHASIL! Semua Produk, Stok, dan Riwayat Transaksi sudah 0 bersih.');
        } catch (\Exception $e) {
            // Jaga-jaga kalau error, tetap nyalakan foreign key check
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }
}
