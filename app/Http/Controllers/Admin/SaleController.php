<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\SaleItem;
use Illuminate\Support\Str;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Events\PurchaseOutStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Notifications\SaleCompleted;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{

    public function printInvoice(Sale $sale)
    {
        $pdf = Pdf::loadView('admin.sales.invoice', compact('sale'));
        return $pdf->stream('invoice-' . $sale->invoice_number . '.pdf');
    }




    public function index(Request $request)
    {
        // Jika request AJAX (dari DataTables)
        if ($request->ajax()) {
            $sales = Sale::with(['customer', 'saleItems.product.category'])
                ->orderBy('created_at', 'desc');

            return DataTables::of($sales)
                ->addIndexColumn()
                // ->addColumn('tanggal_penjualan', function ($row) {
                //     return date('d M, Y', strtotime($row->created_at));
                // })
                ->addColumn('tanggal_penjualan', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('l, d F Y') ?? '-';
                })
                ->addColumn('nama_pelanggan', function ($row) {
                    return $row->customer->nama ?? '-';
                })
                ->addColumn('nomor_hp', function ($row) {
                    return $row->customer->telepon ?? '-';
                })
                ->addColumn('discount', function ($row) {
                    // Nilai 50 dari database langsung diformat.
                    return number_format($row->discount, 0, ',', '.') . '%';
                })
                ->addColumn('item', function ($row) {
                    $html = '';
                    foreach ($row->saleItems as $i => $item) {
                        $html .= "<div style='border-bottom:1px solid #ccc; padding-bottom:4px; margin-bottom:4px;'>
                        <strong>Item " . ($i + 1) . "</strong><br>
                        Nama produk: " . ($item->product->name ?? '-') . "<br>
                        Jumlah: {$item->quantity}<br>
                        Kategori: " . ($item->product->category->name ?? '-') . "<br>
                        Harga per Produk: Rp " . number_format($item->unit_price, 0, ',', '.') . "<br>
                        Total: Rp " . number_format($item->total_price, 0, ',', '.') . "
                    </div>";
                    }
                    return $html;
                })
                ->editColumn('total_price', function ($row) {
                    // Format di server agar tetap bisa search angka aslinya
                    return 'Rp ' . number_format($row->total_price, 0, ',', '.');
                })
                ->addColumn('aksi', function ($row) {
                    $edit = route('sales.edit', $row->id);
                    $invoice = route('sales.invoice', $row->id);
                    $delete = route('sales.destroy', $row->id);
                    return '
                    <a href="' . $edit . '" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                    <a href="' . $invoice . '" target="_blank" class="btn btn-info"><i class="fas fa-print"></i></a>
                    <form action="' . $delete . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button class="btn btn-danger" onclick="return confirm(\'Hapus data?\')"><i class="fas fa-trash"></i></button>
                    </form>
                ';
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

                ->rawColumns(['item', 'aksi', 'status']) // biar HTML di-render

                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value'] ?? null) {
                        $query->whereHas('customer', function ($q) use ($search) {
                            $q->where('nama', 'like', "%{$search}%")
                                ->orWhere('telepon', 'like', "%{$search}%");
                        })
                            ->orWhere('invoice_number', 'like', "%{$search}%")
                            ->orWhere('payment_method', 'like', "%{$search}%")
                            ->orWhere('total_price', 'like', "%{$search}%")
                            ->orWhere('discount', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                            ->orWhereHas('saleItems.product', function ($q) use ($search) { // Tambahan: Pencarian nama produk
                                $q->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('saleItems.product.category', function ($q) use ($search) { // Tambahan: Pencarian nama kategori
                                $q->where('name', 'like', "%{$search}%");
                            });
                    }
                })

                // ... kode setelahnya ...
                ->make(true);
        }

        // Jika bukan AJAX, return view biasa
        return view('admin.sales.index');
    }


    public function create()
    {
        $title = 'create sales';
        // $products = Product::all();
        // produk yang kadaluarsa tidak muncul di form penjualan.
        $products = Product::with(['purchaseItems'])
            ->whereHas('purchaseItems', function ($query) {
                $query->whereDate('expiry_date', '>', Carbon::today())
                    ->orWhereNull('expiry_date');
            })->get();
        $categories = Category::all();

        // Generate invoice number secara acak, contoh: INV-20250730-XXXX
        $invoice_number = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        $saleItems = collect();

        return view('admin.sales.create', compact('title', 'products', 'categories', 'invoice_number', 'saleItems'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'invoice_number' => 'required|string|max:255',
            'nama_customer' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'sale_items' => 'required|array|min:1',
            'sale_items.*.nama_produk' => 'required|exists:products,id',
            'sale_items.*.quantity' => 'required|numeric|min:1',
            'sale_items.*.unit_price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'required|string|max:50',
            'status' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // 🔁 Cek stok & masa kedaluwarsa tiap produk
            foreach ($request->sale_items as $item) {
                $product = Product::find($item['nama_produk']);

                if (!$product) {
                    return back()->withErrors(['stok' => 'Produk tidak ditemukan.']);
                }

                // ❗ Cek apakah produk punya stok kadaluarsa
                // $expired = $product->purchaseItems()
                //     ->whereDate('expiry_date', '<=', now()) // expired atau hari ini
                //     ->where('quantity', '>', 0)
                //     ->exists();

                // if ($expired) {
                //     return back()->withErrors([
                //         'expired' => "Produk {$product->name} sudah kadaluarsa dan tidak bisa dijual."
                //     ]);
                // }
                // $availableStock = $product->purchaseItems()
                //     ->whereDate('expiry_date', '>', now())
                //     ->get()
                //     ->sum(function ($item) {
                //         return $item->quantity - $item->sold_quantity;
                //     });
                $availableStock = $product->purchaseItems()
                    ->where(function ($query) {
                        $query->whereDate('expiry_date', '>', now())
                            ->orWhereNull('expiry_date'); // biar null dianggap valid
                    })
                    ->sum(DB::raw('quantity - sold_quantity'));

                if ($availableStock < $item['quantity']) {
                    return back()->withErrors([
                        'stok' => "Stok untuk produk {$product->name} tidak mencukupi."
                    ])->withInput();
                }


                // // ❗ Cek ketersediaan stok
                // if ($product->stock < $item['quantity']) {
                //     return back()->withErrors([
                //         'stok' => "Stok untuk produk {$product->name} tidak mencukupi."
                //     ]);
                // }
            }

            // ✅ Hitung total harga
            $overall_total_price = 0;
            foreach ($request->sale_items as $item) {
                $quantity = (float) $item['quantity'];
                $unit_price = (float) $item['unit_price'];
                $overall_total_price += $quantity * $unit_price;
            }

            // ✅ Terapkan diskon jika ada
            $discount_percentage = (float) ($request->discount ?? 0);
            if ($discount_percentage > 0) {
                $overall_total_price *= (1 - ($discount_percentage / 100));
            }

            // ✅ Simpan data customer
            // $customer = Customer::create([
            //     'nama' => $request->nama_customer,
            //     'telepon' => $request->nomor_telepon,
            // ]);

            $customer = Customer::firstOrCreate(
                ['telepon' => $request->nomor_telepon], // kolom unik
                ['nama' => $request->nama_customer]     // kolom tambahan kalau belum ada
            );


            // ✅ Simpan data sale utama
            $sale = Sale::create([
                'customer_id' => $customer->id,
                'invoice_number' => $request->invoice_number,
                'discount' => $discount_percentage,
                'total_price' => $overall_total_price,
                'payment_method' => $request->payment_method,
                'status' => $request->status,
            ]);

            // 🔁 Simpan item penjualan & kurangi stok
            foreach ($request->sale_items as $item) {
                $item_quantity = (float) $item['quantity'];
                $item_unit_price = (float) $item['unit_price'];

                // ✅ Simpan item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['nama_produk'],
                    'quantity' => $item_quantity,
                    'unit_price' => $item_unit_price,
                    'discount' => 0,
                ]);

                // // ✅ Kurangi stok produk
                // $product = Product::find($item['nama_produk']);
                // $product->stock -= $item_quantity;
                // $product->save();

                $product = Product::find($item['nama_produk']);
                $remainingQty = $item_quantity;

                // Ambil batch purchase_items yang belum expired dan masih punya stok, diurutkan dari yang paling awal (FIFO)
                $purchases = $product->purchaseItems()
                    ->where(function ($q) {
                        $q->whereDate('expiry_date', '>', now())
                            ->orWhereNull('expiry_date'); // biar batch NULL ikut
                    })
                    ->whereColumn('sold_quantity', '<', 'quantity')
                    ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END') // expired date duluan, null di belakang
                    ->orderBy('expiry_date') // FIFO
                    ->get();

                foreach ($purchases as $purchaseItem) {
                    if ($product->available_stock < $item['quantity']) {
                        return back()->withErrors([
                            'stok' => "Stok untuk produk {$product->name} tidak mencukupi."
                        ])->withInput();
                    }

                    $available = $purchaseItem->quantity - $purchaseItem->sold_quantity;
                    if ($available <= 0) {
                        event(new \App\Events\PurchaseOutStock($item));
                    }

                    if ($available >= $remainingQty) {
                        // Cukup dari 1 batch
                        $purchaseItem->sold_quantity += $remainingQty;
                        $purchaseItem->save();
                        $remainingQty = 0;
                        break;
                    } else {
                        // Ambil semua yang tersedia dari batch ini, lanjut ke berikutnya
                        $purchaseItem->sold_quantity = $purchaseItem->quantity;
                        $purchaseItem->save();
                        $remainingQty -= $available;
                    }
                }

                if ($remainingQty > 0) {
                    // Gagal: stok tidak cukup
                    DB::rollBack();
                    return back()->withErrors([
                        'stok' => "Stok tidak mencukupi untuk produk {$product->name}."
                    ])->withInput();
                }
            }

            DB::commit();

            // Ambil data customer dari penjualan
            $customer = $sale->customer;
            // Kirim notifikasi, misalnya via email ke customer
            \Illuminate\Support\Facades\Notification::send(Auth::user(), new SaleCompleted($sale->id, $sale->invoice_number));

            return redirect()
                ->route('sales.index')
                ->with([
                    'success' => 'Penjualan berhasil ditambahkan!',
                    'invoice_url' => route('sales.invoice', $sale->id)
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan penjualan: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data.'])->withInput();
        }
    }

    public function edit(Sale $sale)
    {
        $title = 'edit sale';
        // $products = Product::all();
        $products = Product::with(['purchaseItems'])
            ->whereHas('purchaseItems', function ($query) {
                $query->whereDate('expiry_date', '>', Carbon::today());
            })->get();
        $customer = Customer::find($sale->customer_id);
        $sale->load('saleItems.product');

        return view('admin.sales.edit', compact('title', 'sale', 'products', 'customer'));
    }


    public function update(Request $request, Sale $sale)
    {
        Log::info('--- Update Penjualan Dijalankan ---');
        Log::info('Request data:', $request->all());
        Log::info('Sale sebelum update:', $sale->toArray());

        $request->validate([
            'nama_customer' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:20',
            'sale_items' => 'required|array|min:1',
            'sale_items.*.nama_produk' => 'required|exists:products,id',
            'sale_items.*.quantity' => 'required|numeric|min:1',
            'sale_items.*.unit_price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'payment_method' => 'required|string|max:50',
            'status' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Ambil item penjualan lama untuk perbandingan
            $oldSaleItems = $sale->saleItems->keyBy('product_id');

            // Loop untuk memproses setiap item baru dari request
            foreach ($request->sale_items as $item) {
                $productId = $item['nama_produk'];
                $newQuantity = (int) $item['quantity'];

                // Cek apakah item ini sudah ada di penjualan lama
                if (isset($oldSaleItems[$productId])) {
                    $oldQuantity = (int) $oldSaleItems[$productId]->quantity;
                    $quantityChange = $newQuantity - $oldQuantity;

                    // Periksa jika ada perubahan kuantitas
                    if ($quantityChange !== 0) {
                        // Temukan purchase item yang sesuai untuk produk ini
                        $purchaseItem = PurchaseItem::where('product_id', $productId)
                            ->first();

                        if (!$purchaseItem) {
                            DB::rollBack();
                            return back()->withErrors(['stok' => 'Purchase item tidak ditemukan untuk produk ini.']);
                        }

                        // Tambahkan atau kurangi sold_quantity di purchase item
                        // Perlu validasi stok jika kuantitas baru lebih besar
                        if ($quantityChange > 0) {
                            // Jika kuantitas baru lebih besar, pastikan stok mencukupi
                            // Logika ini mungkin perlu disesuaikan dengan stok produk
                            // atau cara Anda mengelola stok. Contoh:
                            // $availableStock = $purchaseItem->quantity - $purchaseItem->sold_quantity;
                            // if ($quantityChange > $availableStock) { ... }
                        }

                        // Update sold_quantity di purchase item
                        $purchaseItem->increment('sold_quantity', $quantityChange);
                    }
                } else {
                    // Ini adalah item baru, tambahkan ke sold_quantity
                    $purchaseItem = PurchaseItem::where('product_id', $productId)->first();
                    if (!$purchaseItem) {
                        DB::rollBack();
                        return back()->withErrors(['stok' => 'Purchase item tidak ditemukan untuk produk ini.']);
                    }
                    $purchaseItem->increment('sold_quantity', $newQuantity);
                }
            }

            // Hitung total harga dan diskon
            $overall_total_price = 0;
            foreach ($request->sale_items as $item) {
                $overall_total_price += $item['unit_price'] * $item['quantity'];
            }

            $discount_percentage = (float) ($request->discount ?? 0);
            $discounted_total = $overall_total_price * (1 - $discount_percentage / 100);

            // Hapus item lama yang tidak ada di request baru (jika ada)
            $newProductIds = collect($request->sale_items)->pluck('nama_produk');
            foreach ($oldSaleItems as $oldItem) {
                if (!$newProductIds->contains($oldItem->product_id)) {
                    $purchaseItem = PurchaseItem::where('product_id', $oldItem->product_id)->first();
                    if ($purchaseItem) {
                        // Kembalikan stok ke purchase item
                        $purchaseItem->decrement('sold_quantity', $oldItem->quantity);
                    }
                    $oldItem->delete();
                }
            }

            // Update atau buat item penjualan
            $sale->saleItems()->delete(); // Hapus item lama
            foreach ($request->sale_items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['nama_produk'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                ]);
            }

            // Update data penjualan utama dan customer
            $customer = $sale->customer;
            $customer->update([
                'nama' => $request->nama_customer,
                'telepon' => $request->nomor_telepon,
            ]);
            Log::info("Customer diperbarui: ", $customer->toArray());

            $sale->update([
                'customer_id' => $customer->id,
                'payment_method' => $request->payment_method,
                'discount' => $discount_percentage,
                'total_price' => $discounted_total,
                'status' => $request->status,
            ]);
            Log::info("Sale diperbarui: ", $sale->fresh()->toArray());

            DB::commit();
            Log::info('--- Update penjualan berhasil ---');
            return redirect()->route('sales.index')->with('success', 'Penjualan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal update penjualan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui penjualan: ' . $e->getMessage());
        }
    }

    public function destroy(Sale $sale)
    {
        DB::beginTransaction();

        try {
            foreach ($sale->saleItems as $saleItem) {
                $qtyToReturn = $saleItem->quantity;

                // Ambil semua batch purchase_items untuk produk ini yang pernah terjual
                $batches = PurchaseItem::where('product_id', $saleItem->product_id)
                    ->where('sold_quantity', '>', 0)
                    ->orderBy('expiry_date', 'asc') // FIFO
                    ->get();

                foreach ($batches as $batch) {
                    if ($qtyToReturn <= 0)
                        break;

                    $deduct = min($batch->sold_quantity, $qtyToReturn);

                    $batch->decrement('sold_quantity', $deduct);

                    $qtyToReturn -= $deduct;
                }
            }

            // Hapus saleItems dan sale
            $sale->saleItems()->delete();
            $sale->delete();

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus penjualan: ' . $e->getMessage());
        }
    }


    public function generateReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        $sales = Sale::with(['saleItems.product', 'customer'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$request->from_date, $request->to_date])
            ->when($request->payment_method, function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method);
            })
            ->get();

        $title = 'sales reports';

        // Tambahkan pengecekan role untuk mengembalikan view yang benar
        if (Auth::user()->role == 'admin') {
            return view('admin.sales.reports', compact('sales', 'title'));
        } elseif (Auth::user()->role == 'kasir') {
            return view('kasir.reports.reports', compact('sales', 'title')); // Sesuai path file Anda
        }
    }

    public function reports()
    {
        $title = 'Sales Reports';

        $salesReport = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total_price) as total_price'),
            DB::raw('DATE(created_at) as date')
        )
            ->with('product')
            ->groupBy('product_id', 'date')
            ->orderBy('date', 'desc')
            ->get();

        // Tambahkan pengecekan role untuk mengembalikan view yang benar
        if (Auth::user()->role == 'admin') {
            return view('admin.sales.reports', compact('title'));
        } elseif (Auth::user()->role == 'kasir') {
            // Menggunakan path file yang Anda berikan: 'kasir.reports.reports'
            return view('kasir.reports.reports', compact('title'));
        }
    }

    public function exportPdf(Request $request)
    {
        $from = $request->from_date ?? now()->startOfMonth()->toDateString();
        $to = $request->to_date ?? now()->endOfMonth()->toDateString();

        $query = Sale::with(['customer', 'saleItems.product']);

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->whereBetween('created_at', [$from, $to])->get();

        $totalPendapatan = $sales->sum('total_price');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.sales.reports_pdf', [
            'sales' => $sales,
            'tanggalMulai' => \Carbon\Carbon::parse($from)->format('d-m-Y'),
            'tanggalSelesai' => \Carbon\Carbon::parse($to)->format('d-m-Y'),
            'totalPendapatan' => $totalPendapatan,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-penjualan.pdf');
    }
}
