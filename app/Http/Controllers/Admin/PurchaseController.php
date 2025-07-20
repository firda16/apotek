<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use QCod\AppSettings\Setting\AppSettings;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::query()->with(['category', 'supplier']);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->where('product', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function ($q_cat) use ($searchTerm) {
                      $q_cat->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('supplier', function ($q_sup) use ($searchTerm) {
                      $q_sup->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $pembelians = $query->get();

        return view('admin.purchases.index', compact('pembelians'));
    }

    public function create()
    {
        $title = 'create purchase';
        $categories = Category::get();
        $suppliers = Supplier::get();
        return view('admin.purchases.create', compact('title', 'categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product' => 'required|max:200',
            'category' => 'required',
            'cost_price' => 'required|min:1',
            'quantity' => 'required|min:1',
            'expiry_date' => 'required',
            'supplier' => 'required',
            'image' => 'file|image|mimes:jpg,jpeg,png,gif',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('storage/purchases'), $imageName);
        }

        // Bersihkan input cost_price
        $cleanCostPrice = str_replace(['Rp', '.', ',', ' '], '', $request->cost_price);

        Purchase::create([
            'product' => $request->product,
            'category_id' => $request->category,
            'supplier_id' => $request->supplier,
            'cost_price' => $cleanCostPrice,
            'quantity' => $request->quantity,
            'expiry_date' => $request->expiry_date,
            'image' => $imageName,
        ]);

        $notifications = notify("Purchase has been added");
        return redirect()->route('purchases.index')->with($notifications);
    }

    public function edit(Purchase $purchase)
    {
        $title = 'edit purchase';
        $categories = Category::get();
        $suppliers = Supplier::get();
        return view('admin.purchases.edit', compact('title', 'purchase', 'categories', 'suppliers'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'product' => 'required|max:200',
            'category' => 'required',
            'cost_price' => 'required|min:1',
            'quantity' => 'required|min:1',
            'expiry_date' => 'required',
            'supplier' => 'required',
            'image' => 'file|image|mimes:jpg,jpeg,png,gif',
        ]);

        $imageName = $purchase->image;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('storage/purchases'), $imageName);
        }

        // Bersihkan input cost_price
        $cleanCostPrice = str_replace(['Rp', '.', ',', ' '], '', $request->cost_price);

        $purchase->update([
            'product' => $request->product,
            'category_id' => $request->category,
            'supplier_id' => $request->supplier,
            'cost_price' => $cleanCostPrice,
            'quantity' => $request->quantity,
            'expiry_date' => $request->expiry_date,
            'image' => $imageName,
        ]);

        $notifications = notify("Purchase has been updated");
        return redirect()->route('purchases.index')->with($notifications);
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
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Data pembelian berhasil dihapus.');
    }
}
