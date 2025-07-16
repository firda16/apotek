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
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Mulai query untuk model Purchase dan eager load relasi 'category' dan 'supplier'
        // Ini memastikan data relasi tersedia saat pencarian atau tampilan
        $query = Purchase::query()->with(['category', 'supplier']);

        // Cek apakah ada parameter 'search' dalam request
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');

            // Terapkan kondisi pencarian. 
            // Kita menggunakan closure 'where()' untuk mengelompokkan kondisi OR
            // sehingga pencarian berjalan di antara nama produk, kategori, dan supplier.
            $query->where(function ($q) use ($searchTerm) {
                // 1. Pencarian berdasarkan nama produk ('product')
                $q->where('product', 'like', '%' . $searchTerm . '%');

                // 2. Pencarian berdasarkan nama kategori (menggunakan whereHas untuk relasi)
                $q->orWhereHas('category', function ($q_cat) use ($searchTerm) {
                    $q_cat->where('name', 'like', '%' . $searchTerm . '%');
                });

                // 3. Pencarian berdasarkan nama supplier (menggunakan whereHas untuk relasi)
                $q->orWhereHas('supplier', function ($q_sup) use ($searchTerm) {
                    $q_sup->where('name', 'like', '%' . $searchTerm . '%');
                });
                
                // Opsional: Jika Anda ingin mencari berdasarkan cost_price atau quantity
                // $q->orWhere('cost_price', 'like', '%' . $searchTerm . '%');
                // $q->orWhere('quantity', 'like', '%' . $searchTerm . '%');
            });
        }

        // Ambil data yang sudah difilter atau semua data jika tidak ada pencarian
        $pembelians = $query->get();

        return view('admin.purchases.index', compact(
            'pembelians'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'create purchase';
        $categories = Category::get();
        $suppliers = Supplier::get();
        return view('admin.purchases.create',compact(
            'title','categories','suppliers'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'product'=>'required|max:200',
            'category'=>'required',
            'cost_price'=>'required|min:1',
            'quantity'=>'required|min:1',
            'expiry_date'=>'required',
            'supplier'=>'required',
            'image'=>'file|image|mimes:jpg,jpeg,png,gif',
        ]);
        $imageName = null;
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('storage/purchases'), $imageName);
        }
        Purchase::create([
            'product'=>$request->product,
            'category_id'=>$request->category,
            'supplier_id'=>$request->supplier,
            'cost_price'=>$request->cost_price,
            'quantity'=>$request->quantity,
            'expiry_date'=>$request->expiry_date,
            'image'=>$imageName,
        ]);
        $notifications = notify("Purchase has been added");
        return redirect()->route('purchases.index')->with($notifications);
    }

    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \app\Models\Purchase $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit(Purchase $purchase)
    {
        $title = 'edit purchase';
        $categories = Category::get();
        $suppliers = Supplier::get();
        return view('admin.purchases.edit',compact(
            'title','purchase','categories','suppliers'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \app\Models\Purchase $purchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Purchase $purchase)
    {
        $request->validate([
            'product'=>'required|max:200',
            'category'=>'required',
            'cost_price'=>'required|numeric|min:1',
            'quantity'=>'required|min:1',
            'expiry_date'=>'required',
            'supplier'=>'required',
            'image'=>'file|image|mimes:jpg,jpeg,png,gif',
        ]);        
        $imageName = $purchase->image;
        if($request->hasFile('image')){
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('storage/purchases'), $imageName);
        }
        $purchase->update([
            'product'=>$request->product,
            'category_id'=>$request->category,
            'supplier_id'=>$request->supplier,
            'cost_price'=>$request->cost_price,
            'quantity'=>$request->quantity,
            'expiry_date'=>$request->expiry_date,
            'image'=>$imageName,
        ]);
        $notifications = notify("Purchase has been updated");
        return redirect()->route('purchases.index')->with($notifications);
    }

    public function reports(){
        $title ='purchase reports';
        return view('admin.purchases.reports',compact('title'));
    }

    public function generateReport(Request $request){
        $this->validate($request,[
            'from_date' => 'required',
            'to_date' => 'required'
        ]);
        $title = 'purchases reports';
        $purchases = Purchase::whereBetween(DB::raw('DATE(created_at)'), array($request->from_date, $request->to_date))->get();
        return view('admin.purchases.reports',compact(
            'purchases','title'
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Request $request)
    // {
    //     return Purchase::findOrFail($request->id)->delete();
    // }

    // app/Http/Controllers/PurchaseController.php

public function destroy(Request $request, Purchase $purchase)
{
    // Jika model ditemukan, Laravel akan meneruskannya ke $purchase
    $purchase->delete();

    // Anda bisa mengembalikan respons sesuai kebutuhan, misalnya redirect atau JSON
    return redirect()->route('purchases.index')->with('success', 'Data pembelian berhasil dihapus.');
}
}
