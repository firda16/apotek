<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use App\Events\PurchaseOutStock;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {

        // $products = Product::get();
        $query = Sale::query()->with(['saleItems.product.category', 'saleItems.product', 'saleItems']);
        $items = SaleItem::query();
        $products = Product::get();
        $category = Category::get();

        $sales = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.sales.index',compact(
        'sales', 'items', 'products', 'category' 
        ));
    }

    // public function data(Request $request)
    // {
    //     $title = 'sales';
    //     if($request->ajax()){
    //         $sales = Sale::latest()->with((['category']));
    //         return DataTables::of($sales)
    //                 ->addIndexColumn()
    //                 ->addColumn('product',function($sale){
    //                     $image = '';
    //                     if(!empty($sale->product)){
    //                         $image = null;
    //                         if(!empty($sale->product->purchase->image)){
    //                             $image = '<span class="avatar avatar-sm mr-2">
    //                             <img class="avatar-img" src="'.asset("storage/purchases/".$sale->product->purchase->image).'" alt="image">
    //                             </span>';
    //                         }
    //                         return $sale->product->purchase->product. ' ' . $image;
    //                     }
    //                 })
    //                 ->addColumn('total_price',function($sale){
    //                     return settings('app_currency','Rp').' '. $sale->total_price;
    //                 })
    //                 ->addColumn('date',function($row){
    //                     return date_format(date_create($row->created_at),'d M, Y');
    //                 })
    //                 ->addColumn('action', function ($row) {
    //                     $editbtn = '<a href="'.route("sales.edit", $row->id).'" class="editbtn"><button class="btn btn-primary"><i class="fas fa-edit"></i></button></a>';
    //                     $deletebtn = '<a data-id="'.$row->id.'" data-route="'.route('sales.destroy', $row->id).'" href="javascript:void(0)" id="deletebtn"><button class="btn btn-danger"><i class="fas fa-trash"></i></button></a>';
    //                     if (!auth()->user()->hasPermissionTo('edit-sale')) {
    //                         $editbtn = '';
    //                     }
    //                     if (!auth()->user()->hasPermissionTo('destroy-sale')) {
    //                         $deletebtn = '';
    //                     }
    //                     $btn = $editbtn.' '.$deletebtn;
    //                     return $btn;
    //                 })
    //                 ->rawColumns(['product','action'])
    //                 ->make(true);

    //     }
    //     // $products = Product::get();
    //     return view('admin.sales.index',compact(
    //         'title','products',
    //     ));
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'create sales';
        $products = Product::get();
        $categories = Category::get();
        return view('admin.sales.create',compact(
            'title', 'categories','products'
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
        'queue_number' => 'required|string',
        'product' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'unit' => 'nullable|string',
        'unit_price' => 'required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0|max:100',
        'payment_method' => 'required|string',
    ]);

    DB::beginTransaction();
    try {
        $product = Product::findOrFail($request->product);

        if ($product->quantity < $request->quantity) {
            return back()->withErrors(['Stok tidak mencukupi. Stok tersedia: ' . $product->quantity]);
        }

        // Hitung total harga
        $quantity = $request->quantity;
        $unit_price = $request->unit_price;
        $discount = $request->discount ?? 0;
        $total_price_before_discount = $unit_price * $quantity;
        $discount_amount = ($discount / 100) * $total_price_before_discount;
        $final_total = $total_price_before_discount - $discount_amount;

        // Buat sale
        $sale = Sale::create([
            'queue_number' => $request->queue_number,
            'discount' => $discount,
            'payment_method' => $request->payment_method,
            'total_price' => $final_total, // total semua produk, kalau 1 saja langsung isi
        ]);

        // Buat sale item
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit' => $request->unit,
            'unit_price' => $unit_price,
            'total_price' => $final_total,
        ]);

        // Kurangi stok produk
        $product->decrement('quantity', $quantity);

        // Kirim event kalau hampir habis
        if ($product->quantity <= 1) {
            event(new PurchaseOutStock($product));
        }

        DB::commit();
        return redirect()->route('sales.index')->with(notify('Penjualan berhasil ditambahkan.'));
    } catch (\Throwable $th) {
        DB::rollback();
        return back()->withErrors(['Terjadi kesalahan: ' . $th->getMessage()]);
    }
}





    /**
     * Show the form for editing the specified resource.
     *
     * @param  \app\Models\Sale $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        $title = 'edit sale';
        $categories = Category::get();
        $products = Product::get();
        $sale->load( 'saleItems');        
        return view('admin.sales.edit',compact(
            'title','sale','products'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \app\Models\Sale $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'product'=>'required',
            'quantity'=>'required|integer|min:1'
        ]);
        $sold_product = Product::find($request->product);
        /**
         * update quantity of sold item from purchases
        **/
        $purchased_item = Purchase::find($sold_product->purchase->id);
        if(!empty($request->quantity)){
            $new_quantity = ($purchased_item->quantity) - ($request->quantity);
        }
        $new_quantity = $sale->quantity;
        $notification = '';
        if (!($new_quantity < 0)){
            $purchased_item->update([
                'quantity'=>$new_quantity,
            ]);

            /**
             * calcualting item's total price
            **/
            if(!empty($request->quantity)){
                $total_price = ($request->quantity) * ($sold_product->price);
            }
            $total_price = $sale->total_price;
            $sale->update([
                'product_id'=>$request->product,
                'quantity'=>$request->quantity,
                'total_price'=>$total_price,
            ]);

            $notification = notify("Product has been updated");
        }
        if($new_quantity <=1 && $new_quantity !=0){
            // send notification
            $product = Purchase::where('quantity', '<=', 1)->first();
            event(new PurchaseOutStock($product));
            // end of notification
            $notification = notify("Product is running out of stock!!!");

        }
        return redirect()->route('sales.index')->with($notification);
    }

    /**
     * Generate sales reports index
     *
     * @return \Illuminate\Http\Response
     */
    public function reports(Request $request){
        $title = 'sales reports';
        return view('admin.sales.reports',compact(
            'title'
        ));
    }

    /**
     * Generate sales report form post
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request){
        $request->validate([
            'from_date' => 'required',
            'to_date' => 'required',
        ]);
        $title = 'sales reports';
        $sales = Sale::whereBetween(DB::raw('DATE(created_at)'), array($request->from_date, $request->to_date))->get();
        return view('admin.sales.reports',compact(
            'sales','title'
        ));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Sale::findOrFail($request->id)->delete();
        return redirect()->route('sales.index')->with(notify("Sale has been deleted"));
    }
}
