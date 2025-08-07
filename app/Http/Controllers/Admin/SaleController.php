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

class SaleController extends Controller
{

    public function printInvoice(Sale $sale)
    {
        $pdf = Pdf::loadView('admin.sales.invoice', compact('sale'));
        return $pdf->stream('invoice-' . $sale->invoice_number . '.pdf');
    }


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
        // $products = Product::all();
        // produk yang kadaluarsa tidak muncul di form penjualan.
        $products = Product::with(['purchaseItems'])
            ->whereHas('purchaseItems', function ($query) {
                $query->whereDate('expiry_date', '>', Carbon::today());
            })->get();
        $categories = Category::all();

        // Generate invoice number secara acak, contoh: INV-20250730-XXXX
        $invoice_number = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        return view('admin.sales.create', compact('title', 'products', 'categories', 'invoice_number'));
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
                $availableStock = $product->purchaseItems()
                    ->whereDate('expiry_date', '>', now())
                    ->get()
                    ->sum(function ($item) {
                        return $item->quantity - $item->sold_quantity;
                    });

                if ($availableStock < $item['quantity']) {
                    return back()->withErrors([
                        'stok' => "Stok untuk produk {$product->name} tidak mencukupi."
                    ])->withInput();
                }


                // ❗ Cek ketersediaan stok
                if ($product->stock < $item['quantity']) {
                    return back()->withErrors([
                        'stok' => "Stok untuk produk {$product->name} tidak mencukupi."
                    ]);
                }
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
            $customer = Customer::create([
                'nama' => $request->nama_customer,
                'telepon' => $request->nomor_telepon,
            ]);

            // ✅ Simpan data sale utama
            $sale = Sale::create([
                'customer_id' => $customer->id,
                'payment_method' => $request->payment_method,
                'discount' => $discount_percentage,
                'total_price' => $overall_total_price,
                'invoice_number' => $request->invoice_number,
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
                    ->whereDate('expiry_date', '>', now())
                    ->whereColumn('sold_quantity', '<', 'quantity')
                    ->orderBy('expiry_date') // FIFO: barang lama dijual lebih dulu
                    ->get();

                foreach ($purchases as $purchaseItem) {
                    if ($product->available_stock < $item['quantity']) {
                        return back()->withErrors([
                            'stok' => "Stok untuk produk {$product->name} tidak mencukupi."
                        ])->withInput();
                    }

                    $available = $purchaseItem->quantity - $purchaseItem->sold_quantity;

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






    //     public function store(Request $request)
    //     {

    //     $request->validate([
    //         'nama_customer' => 'required|string|max:255',
    //         'nomor_telepon' => 'required|string|max:20',
    //         'sale_items' => 'required|array|min:1',
    //         'sale_items.*.nama_produk' => 'required|exists:products,id',
    //         'sale_items.*.quantity' => 'required|numeric|min:1',
    //         'sale_items.*.unit_price' => 'nullable|numeric|min:0',
    //         'discount' => 'nullable|numeric|min:0|max:100', // Diskon dalam persen
    //         'payment_method' => 'required|string|max:50',
    //     ]);


    //     // 2. Hitung Total Harga Keseluruhan (DI BACKEND)
    //     $overall_total_price = 0;
    //     foreach ($request->sale_items as $item) {
    //         $quantity = (float) $item['quantity'];
    //         $unit_price = (float) $item['unit_price'];
    //         $overall_total_price += ($quantity * $unit_price);
    //     }

    //     // Terapkan diskon jika ada
    //     $discount_percentage = (float) $request->discount ?? 0;
    //     if ($discount_percentage > 0) {
    //         $overall_total_price = $overall_total_price * (1 - ($discount_percentage / 100));
    //     }

    //     $customer = Customer::create([
    //        'nama' => $request->nama_customer,
    //        'telepon' => $request->nomor_telepon,
    //    ]);
    //     // 3. Buat Entri Sale (Penjualan Utama)
    //     $sale = Sale::create([
    //         'customer_id' => $customer->id,
    //         'payment_method' => $request->payment_method,
    //         'discount' => $discount_percentage, // Simpan diskon dalam persen
    //         'total_price' => $overall_total_price, // Total harga setelah diskon
    //     ]);

    //     // 4. Buat Entri SaleItem (Detail Produk)
    //     // Sekarang kita mengiterasi array 'sale_items' yang benar
    //     foreach ($request->sale_items as $item) {
    //         $item_quantity = (float) $item['quantity'];
    //         $item_unit_price = (float) $item['unit_price'];
    //         $item_subtotal = $item_quantity * $item_unit_price; // Subtotal per item

    //         // $product = Product::find($item['nama_produk']);
    //         // if (!$product || $product->stock < $item['quantity']) {
    //         //     return back()->withErrors(['stok' => "Stok untuk produk {$product->name} tidak mencukupi."]);
    //         // }

    //         $product = Product::find($item['nama_produk']);

    //         if (!$product) {
    //             return back()->withErrors(['stok' => 'Produk tidak ditemukan.']);
    //         }

    //         if ($product->stock < $item_quantity) {
    //             return back()->withErrors(['stok' => "Stok untuk produk {$product->name} tidak mencukupi."]);
    //         }


    //         SaleItem::create([
    //             'sale_id' => $sale->id,
    //             'product_id' => $item['nama_produk'], // Sesuaikan dengan 'nama_produk' dari request
    //             'quantity' => $item_quantity,
    //             'unit_price' => $item_unit_price,
    //             'discount' => 0, // Jika diskon hanya global, set 0 di sini.
    //                             // Jika ada diskon per item, Anda perlu tambahkan input di HTML dan validasi di sini.
    //             // 'total_price' => $item_subtotal, // Subtotal per item
    //         ]);
    //         // // Kurangi stok produk
    //         // $product = Product::find($item['nama_produk']);
    //         // if ($product) {
    //         //     $product->stock -= $item_quantity;
    //         //     $product->save();
    //         // }

    //         $product->stock -= $item_quantity;
    //         $product->save();
    //     }

    //     // 5. Redirect atau kembalikan response sukses
    //     return redirect()->route('sales.index')->with('success', 'Penjualan berhasil ditambahkan!');
    //     }

    public function edit(Sale $sale)
    {
        $title = 'edit sale';
        $products = Product::all();
        $categories = Category::all();
        $customer = Customer::find($sale->customer_id);
        $sale->load('saleItems.product.category');

        return view('admin.sales.edit', compact('title', 'sale', 'products', 'categories', 'customer'));
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
        ]);

        DB::beginTransaction();

        try {
            // Validasi stok
            foreach ($request->sale_items as $item) {
                $product = Product::find($item['nama_produk']);
                if (!$product) {
                    Log::error("Produk tidak ditemukan: ID {$item['nama_produk']}");
                    return back()->withErrors(['stok' => 'Produk tidak ditemukan.']);
                }
                if ($product->stock < $item['quantity']) {
                    Log::error("Stok tidak cukup untuk produk: {$product->name}");
                    return back()->withErrors(['stok' => "Stok untuk produk {$product->name} tidak mencukupi."]);
                }
            }

            // Kembalikan stok lama
            foreach ($sale->saleItems as $item) {
                Log::info("Kembalikan stok produk ID {$item->product_id} sebanyak {$item->quantity}");
                Product::find($item->product_id)->increment('stock', $item->quantity);
            }

            // Hitung total harga
            $overall_total_price = 0;
            foreach ($request->sale_items as $item) {
                $overall_total_price += $item['unit_price'] * $item['quantity'];
            }

            $discount_percentage = (float) ($request->discount ?? 0);
            $discounted_total = $overall_total_price * (1 - $discount_percentage / 100);

            Log::info("Total harga sebelum diskon: {$overall_total_price}");
            Log::info("Diskon: {$discount_percentage}%");
            Log::info("Total setelah diskon: {$discounted_total}");

            // Hapus item lama
            $sale->saleItems()->delete();
            Log::info("Item lama dihapus.");

            // Update customer
            $customer = $sale->customer;
            $customer->update([
                'nama' => $request->nama_customer,
                'telepon' => $request->nomor_telepon,
            ]);
            Log::info("Customer diperbarui: ", $customer->toArray());

            // Update penjualan
            $sale->update([
                'customer_id' => $customer->id,
                'payment_method' => $request->payment_method,
                'discount' => $discount_percentage,
                'total_price' => $discounted_total,
            ]);
            Log::info("Sale diperbarui: ", $sale->fresh()->toArray());

            // Tambahkan item baru
            foreach ($request->sale_items as $item) {
                $subtotal = ($item['unit_price'] * $item['quantity']) - ($item['discount'] ?? 0);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['nama_produk'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    // 'total_price' => $subtotal,
                ]);

                $product = Product::find($item['nama_produk']);
                $product->decrement('stock', $item['quantity']);
                Log::info("Stok produk ID {$item['nama_produk']} dikurangi sebanyak {$item['quantity']}");

                if ($product->stock <= 1) {
                    event(new PurchaseOutStock($product));
                    Log::warning("Produk {$product->name} hampir habis stok.");
                }
            }

            DB::commit();
            Log::info('--- Update penjualan berhasil ---');
            return redirect()->route('sales.index')->with('success', 'Penjualan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal update penjualan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui penjualan: ' . $e->getMessage());
        }
    }




    //  public function update(Request $request, Sale $sale)
    // {
    //     // 1. Log or dump the incoming request data
    //     // This confirms what your form is actually sending.
    //     Log::info('Incoming Update Sale Request:', $request->all());
    //     // Or for a browser dump: dd($request->all());

    //     $rules = [
    //         'nama_customer' => 'required|string|max:255',
    //         'nomor_telepon' => 'required|string|max:20',
    //         'discount' => 'nullable|numeric|min:0|max:100',
    //         'payment_method' => 'required|in:Cash,Transfer,QRIS',
    //         'sale_items' => 'required|array|min:1',
    //         'sale_items.*.nama_produk' => ['required', 'exists:products,id'],
    //         'sale_items.*.quantity' => 'required|numeric|min:1',
    //         'sale_items.*.unit_price' => 'required|numeric|min:0',
    //         'total_final_price' => 'required|numeric|min:0', // Ensure this hidden field is validated
    //     ];

    //     $messages = [
    //         // ... (your existing validation messages) ...
    //         'total_final_price.required' => 'Total harga akhir wajib dihitung.',
    //         'total_final_price.numeric' => 'Total harga akhir harus berupa angka.',
    //         'total_final_price.min' => 'Total harga akhir tidak boleh negatif.',
    //     ];

    //     try {
    //         $validatedData = $request->validate($rules, $messages);

    //         // 2. Log or dump after successful validation
    //         // If you don't see this, validation is failing.
    //         Log::info('Sale Validation Passed:', $validatedData);
    //         // Or for a browser dump: dd('Validation Passed!', $validatedData);

    //         DB::beginTransaction();

    //         // Check if customer exists before updating
    //         $customer = $sale->customer; // Assuming $sale has a customer relationship
    //         if (!$customer) {
    //             // This shouldn't happen if the sale is properly associated with a customer
    //             throw new \Exception('Customer not found for this sale.');
    //         }
    //         $customer->nama = $validatedData['nama_customer'];
    //         $customer->telepon = $validatedData['nomor_telepon'];
    //         $customer->save();
    //         Log::info('Customer updated:', ['id' => $customer->id, 'nama' => $customer->nama]);


    //         // Update Sale details
    //         $sale->discount = $validatedData['discount'] ?? 0;
    //         $sale->payment_method = $validatedData['payment_method'];
    //         // Use the hidden_final_total value for total_price
    //         $sale->total_price = $validatedData['total_final_price']; // Make sure to use the validated value
    //         $sale->save();
    //         Log::info('Sale updated:', ['id' => $sale->id, 'total_price' => $sale->total_price]);

    //         // Handle Sale Items
    //         // Get current product IDs and quantities before deleting
    //         $oldSaleItems = $sale->saleItems()->get();
    //         $oldProductQuantities = [];
    //         foreach ($oldSaleItems as $oldItem) {
    //             $oldProductQuantities[$oldItem->product_id] = $oldItem->quantity;
    //         }

    //         // Delete existing sale items first
    //         $sale->saleItems()->delete();
    //         Log::info('Existing sale items deleted for sale ID:', ['sale_id' => $sale->id]);

    //         // Recreate new sale items and update product stock
    //         foreach ($validatedData['sale_items'] as $itemData) {
    //             $product = Product::find($itemData['nama_produk']);
    //             if ($product) {
    //                 $saleItem = $sale->saleItems()->create([
    //                     'product_id' => $product->id,
    //                     'quantity' => $itemData['quantity'],
    //                     'unit_price' => $itemData['unit_price'],
    //                     'total_price' => $itemData['quantity'] * $itemData['unit_price'],
    //                 ]);
    //                 Log::info('New sale item created:', ['sale_item_id' => $saleItem->id, 'product_id' => $product->id]);

    //                 // Adjust product stock: Add back old quantity, then subtract new quantity
    //                 $oldQty = $oldProductQuantities[$product->id] ?? 0;
    //                 $newQty = $itemData['quantity'];

    //                 // This logic assumes you decrement stock on sale creation/update
    //                 // Make sure your stock logic is correct.
    //                 // If you *only* decrement on new sale, then this part needs careful thought
    //                 // to avoid over/under-stocking on updates.
    //                 // A simpler way: increment old stock back, then decrement new stock.
    //                 Product::where('id', $product->id)->increment('stock', $oldQty); // Return old stock
    //                 Product::where('id', $product->id)->decrement('stock', $newQty); // Subtract new stock
    //                 Log::info('Product stock adjusted for product ID:', ['product_id' => $product->id, 'old_qty' => $oldQty, 'new_qty' => $newQty]);

    //                 // Basic check for sufficient stock (can also be a validation rule)
    //                 if ($product->stock < 0) {
    //                      // This means the new quantity makes stock negative after adjustment
    //                      // You might want to throw an exception or handle this more gracefully
    //                      throw new \Exception("Insufficient stock for product: " . $product->name);
    //                 }
    //             } else {
    //                 Log::error('Product not found during sale item creation:', ['product_id' => $itemData['nama_produk']]);
    //                 throw new \Exception("Produk dengan ID " . $itemData['nama_produk'] . " tidak ditemukan.");
    //             }
    //         }

    //         DB::commit();
    //         Log::info('Sale update successful and committed for sale ID:', ['sale_id' => $sale->id]);
    //         return redirect()->route('sales.index')->with('success', 'Penjualan berhasil diperbarui!');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // Log the actual exception message for detailed debugging
    //         Log::error('Sale Update Failed:', [
    //             'error_message' => $e->getMessage(),
    //             'file' => $e->getFile(),
    //             'line' => $e->getLine(),
    //             'trace' => $e->getTraceAsString(), // Full stack trace
    //         ]);
    //         return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui penjualan: ' . $e->getMessage());
    //     }
    // }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus.');
    }


    // public function destroy(Request $request)
    // {
    //     $sale = Sale::findOrFail($request->id);
    //     $sale->delete();
    //     return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus.');
    // }

    public function generateReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        $sales = Sale::with(['saleItems.product', 'customer']) // ← ini wajib!
            ->whereBetween(DB::raw('DATE(created_at)'), [$request->from_date, $request->to_date])
            ->get();

        $title = 'sales reports';

        return view('admin.sales.reports', compact('sales', 'title'));
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

        return view('admin.sales.reports', compact('title', 'salesReport'));
    }
}
