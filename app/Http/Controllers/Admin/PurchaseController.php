<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\SaleCompleted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use QCod\AppSettings\Setting\AppSettings;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PurchaseCompletedNotification;



class PurchaseController extends Controller
{

    public function datatable(Request $request)
    {
        try {
            $query = Purchase::with(['supplier', 'purchaseItems.product.category']);

            // Manual filter jika ada pencarian
            if ($search = $request->get('search')['value'] ?? null) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('supplier', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhere('invoice_number', 'like', "%{$search}%")
                        ->orWhere('total_price', 'like', "%{$search}%")
                        ->orWhereHas('purchaseItems.product', function ($q3) use ($search) {
                            $q3->where('name', 'like', "%{$search}%")
                                ->orWhereHas('category', function ($q4) use ($search) {
                                    $q4->where('name', 'like', "%{$search}%");
                                });
                        });
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tanggal', function ($purchase) {
                    return Carbon::parse($purchase->created_at)->translatedFormat('l, d F Y') ?? '-';
                })
                ->addColumn('invoice_number', function ($purchase) {
                    return $purchase->invoice_number ?: '-';
                })
                ->addColumn('supplier', function ($purchase) {
                    return $purchase->supplier->name ?? '-';
                })
                ->addColumn('payment_method', function ($purchase) {
                    return $purchase->payment_method ?? '-';
                })
                ->addColumn('items', function ($purchase) {
                    $html = '<ul>';
                    foreach ($purchase->purchaseItems as $item) {
                        $product = optional($item->product);
                        $category = optional($product->category);
                        $html .= '<li>';
                        $html .= '<strong>' . ($product->name ?? '-') . '</strong><br>';
                        $html .= 'Exp: ' . ($item->expiry_date
                            ? Carbon::parse($item->expiry_date)->translatedFormat('l, d F Y')
                            : 'tidak ada tanggal kadaluarsa') . '<br>';

                        // $html .= 'Kategori: ' . ($category->name ?? '-') . '<br>';
                        $html .= 'Jumlah: ' . $item->quantity . '<br>';
                        $html .= 'Harga: Rp ' . number_format($item->unit_price, 0, ',', '.') . '<br>';
                        $html .= 'Sub Total: Rp ' . number_format($item->total_price, 0, ',', '.') . '<br>';
                        $html .= '</li><hr>';
                    }
                    $html .= '</ul>';
                    return $html;
                })
                ->addColumn('total', function ($purchase) {
                    return 'Rp ' . number_format($purchase->total_price, 0, ',', '.');
                })
                ->addColumn('action', function ($purchase) {
                    $edit = route('purchases.edit', $purchase->id);
                    $delete = route('purchases.destroy', $purchase->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');

                    return <<<HTML
<a href="{$edit}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
<form action="{$delete}" method="POST" style="display:inline;">
    {$csrf}
    {$method}
    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus pembelian ini?');">
        <i class="fas fa-trash"></i>
    </button>
</form>
HTML;
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status; // Asumsikan kolom di tabel bernama 'status'
                    $class = '';

                    // Tentukan kelas CSS berdasarkan nilai status
                    if ($status === 'pending') {
                        $class = 'status-pending';
                    } elseif ($status === 'selesai') {
                        $class = 'status-selesai';
                    } elseif ($status === 'dibatalkan') {
                        $class = 'status-dibatalkan';
                    } else {
                        // Kelas default jika status tidak sesuai
                        $class = 'status-default';
                    }

                    // Kembalikan HTML dengan kelas CSS yang sudah ditentukan
                    return '<span class="status ' . $class . '">' . ucfirst($status) . '</span>';
                })
                ->rawColumns(['items', 'action', 'status'])
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? null) {
                        $query->whereHas('supplier', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhereHas('purchaseItems.product', function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        })->orWhereHas('purchaseItems', function ($q) use ($search) {
                            $q->where('quantity', 'like', "%{$search}%")
                                ->orWhere('unit_price', 'like', "%{$search}%")
                                ->orWhere('total_price', 'like', "%{$search}%")
                                ->orWhere('expiry_date', 'like', "%{$search}%");
                        })->orWhere('payment_method', 'like', "%{$search}%")
                            ->orWhere('total_price', 'like', "%{$search}%");
                    }
                })
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTable Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
            ], 500);
        }
    }




    public function show($id)
    {
        // Misalnya fetch data pembelian berdasarkan ID
        $purchase = Purchase::with('supplier', 'items')->findOrFail($id);

        // Return JSON untuk DataTables atau tampilan detail
        return response()->json($purchase);
    }


    public function index(Request $request)
    {
        App::setLocale('id');
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
        // $categories = Category::get();
        $suppliers = Supplier::get();
        $products = Product::with('category')->get();
        return view('admin.purchases.create', compact('title', 'suppliers', 'products'));
    }

    public function getLastPrice(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $lastPurchaseItem = PurchaseItem::whereHas('purchase', function ($q) use ($request) {
            $q->where('supplier_id', $request->supplier_id);
        })
            ->where('product_id', $request->product_id)
            ->orderByDesc('created_at')
            ->first();

        if ($lastPurchaseItem) {
            return response()->json([
                'unit_price' => $lastPurchaseItem->unit_price
            ]);
        }

        return response()->json(['unit_price' => null]);
    }



    public function store(Request $request)
    {
        Log::info('Memulai proses penyimpanan pembelian.', ['request_data' => $request->all()]);

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_items' => 'required|array|min:1',
            'purchase_items.*.product_id' => 'nullable|exists:products,id',
            'purchase_items.*.quantity' => 'required|numeric|min:1',
            'purchase_items.*.unit_price' => 'required|numeric|min:0',
            'purchase_items.*.expiry_date' => 'nullable|date',
            'invoice_number' => 'required|string|max:255',
            'payment_method' => 'required|string|in:Cash,Transfer,QRIS',
            'status' => 'required',
        ]);

        DB::beginTransaction();

        try {
            Log::info('Validasi sukses. Mencari supplier...');
            $supplier = Supplier::findOrFail($request->supplier_id);
            Log::info('Supplier ditemukan.', ['supplier_id' => $supplier->id]);

            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'payment_method' => $request->payment_method,
                'invoice_number' => $request->invoice_number,
                'status' => $request->status,
                // 'total_price' => 0,
            ]);

            Log::info('Entri pembelian utama berhasil dibuat.', ['purchase_id' => $purchase->id]);

            $total = 0;

            foreach ($request->purchase_items as $index => $item) {
                if (
                    !is_array($item) ||
                    empty($item['product_id']) ||
                    empty($item['quantity']) ||
                    empty($item['unit_price'])
                ) {
                    Log::warning("Baris pembelian ke-{$index} kosong/tidak valid, dilewati.");
                    continue;
                }
                Log::info("Memproses item ke-{$index}", ['item' => $item]);

                $subtotal = $item['quantity'] * $item['unit_price'];
                $total += $subtotal;

                if (empty($item['product_id'])) {
                    throw new \Exception("Product ID pada item ke-{$index} kosong.");
                }

                $product = Product::findOrFail($item['product_id']);
                Log::info("Produk ditemukan.", ['product_id' => $product->id]);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    // 'quantity' => $item['quantity'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    // 'total_price' => $subtotal,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'sold_quantity' => 0,
                ]);

                Log::info("Item pembelian berhasil disimpan.", ['product_id' => $product->id]);

                $product->increment('stock', $item['quantity']);
                Log::info("Stok produk berhasil ditambahkan.", [
                    'product_id' => $product->id,
                    'jumlah_ditambahkan' => $item['quantity']
                ]);
            }

            $purchase->update(['total_price' => $total]);
            Log::info('Total pembelian berhasil diupdate.', ['total_price' => $total]);

            DB::commit();
            Log::info('Transaksi berhasil disimpan.');

            $purchase->load('purchaseItems.product', 'supplier');
            Notification::send(
                Auth::user(),
                new PurchaseCompletedNotification($purchase, $total)
            );


            return redirect()
                ->route('purchases.index')
                ->with('success', 'Pembelian berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Terjadi error saat menyimpan pembelian.', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pembelian: ' . $e->getMessage());
        }
    }




    public function edit(Purchase $purchase)
    {
        $title = 'edit purchase';
        $categories = Category::get();
        $suppliers = Supplier::get();
        $products = Product::get();
        $purchase->load('purchaseItems.product');

        return view('admin.purchases.edit', compact('title', 'purchase', 'categories', 'suppliers', 'products'));
    }


    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'payment_method' => 'required|string',
            'purchase_items' => 'required|array|min:1',
            'purchase_items.*.product_id' => 'required|exists:products,id',
            'purchase_items.*.quantity' => 'required|numeric|min:1',
            'purchase_items.*.unit_price' => 'required|numeric|min:0',
            'purchase_items.*.expiry_date' => 'nullable|date',
            'invoice_number' => 'required|string|max:255',
            'status' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // Update header
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'payment_method' => $request->payment_method,
                'invoice_number' => $request->invoice_number,
                'status' => $request->status,
            ]);

            $total = 0;

            // Ambil purchaseItems lama keyed by product_id
            $existing = $purchase->purchaseItems()->get()->keyBy('product_id');

            foreach ($request->purchase_items as $productData) {
                $productId = $productData['product_id'];
                $qty = (int) $productData['quantity'];
                $unitPrice = $productData['unit_price'];
                $expiry = $productData['expiry_date'] ?? null;

                $subtotal = $qty * $unitPrice;
                $total += $subtotal;

                if ($existing->has($productId)) {
                    // Update batch yang ada — JANGAN reset sold_quantity
                    $item = $existing->get($productId);

                    // Validasi: quantity baru tidak boleh kurang dari sold_quantity
                    if ($qty < $item->sold_quantity) {
                        DB::rollBack();
                        return back()->withInput()->with(
                            'error',
                            "Jumlah pembelian untuk produk {$item->product->name} ({$qty}) tidak bisa kurang dari jumlah yang sudah terjual ({$item->sold_quantity})."
                        );
                    }

                    $item->update([
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'expiry_date' => $expiry,
                        // sold_quantity dibiarkan sama
                    ]);

                    // tandai item sudah diproses
                    $existing->forget($productId);
                } else {
                    // batch baru — sold_quantity default 0
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $productId,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'expiry_date' => $expiry,
                        'sold_quantity' => 0,
                    ]);
                }
            }

            // Sisanya di $existing adalah batch yang dihapus di form.
            foreach ($existing as $left) {
                if ($left->sold_quantity > 0) {
                    DB::rollBack();
                    return back()->withInput()->with(
                        'error',
                        "Tidak dapat menghapus batch pembelian '{$left->product->name}' karena sudah ada penjualan ({$left->sold_quantity})."
                    );
                }
                // aman dihapus karena belum ada penjualan dari batch ini
                $left->delete();
            }

            // Update total
            $purchase->update(['total_price' => $total]);

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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

        $pembelians = Purchase::with(['supplier', 'purchaseItems.product.category'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$request->from_date, $request->to_date])
            ->when($request->payment_method, function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.purchases.reports', compact('pembelians', 'title'));
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        $pembelians = Purchase::with(['supplier', 'purchaseItems.product.category'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$request->from_date, $request->to_date])
            ->when($request->payment_method, function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPembelian = $pembelians->flatMap->purchaseItems->sum(function ($item) {
            return $item->total_price ?? ($item->quantity * $item->unit_price);
        });

        $tanggalMulai = \Carbon\Carbon::parse($request->from_date)->format('d M Y');
        $tanggalSelesai = \Carbon\Carbon::parse($request->to_date)->format('d M Y');

        $pdf = Pdf::loadView('admin.purchases.reports_pdf', compact(
            'pembelians',
            'totalPembelian',
            'tanggalMulai',
            'tanggalSelesai'
        ));

        return $pdf->stream('laporan-pembelian-' . now()->format('d-m-Y') . '.pdf');
    }


    public function checkInvoice(Request $request)
    {
        $invoiceNumber = $request->invoice_number;

        $exists = Purchase::where('invoice_number', $invoiceNumber)->exists();

        return response()->json(['exists' => $exists]);
    }

    // public function destroy(Request $request, Purchase $purchase)
    // {
    //     foreach ($purchase->purchaseItems as $item) {
    //         Product::find($item->product_id)->decrement('stock', $item->quantity);
    //     }
    //     return redirect()->route('purchases.index')->with('success', 'Data pembelian berhasil dihapus.');
    // }
    public function destroy(Purchase $purchase)
    {
        // Kembalikan stok sebelum hapus
        foreach ($purchase->purchaseItems as $item) {
            $item->decrement('quantity', $item->quantity);
        }

        // Hapus semua item terkait
        $purchase->purchaseItems()->delete();

        // Hapus purchase utama
        $purchase->delete();

        return redirect()->route('purchases.index')->with('success', 'Data pembelian berhasil dihapus.');
    }

}
