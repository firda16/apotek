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

        return view('admin.sales.index', compact(
            'sales',
            'items',
            'products',
            'category'
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
        return view('admin.sales.create', compact(
            'title',
            'categories',
            'products'
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
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'queue_number' => $request->queue_number,
                'payment_method' => $request->payment_method,
                'discount' => $request->discount ?? 0,
                'total_price' => 0,
            ]);

            $total = 0;

            foreach ($request->products as $product) {
                $subtotal = ($product['unit_price'] * $product['quantity']) - ($product['discount'] ?? 0);
                $total += $subtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['unit_price'],
                    'unit' => $product['unit'] ?? null,
                    'discount' => $product['discount'] ?? 0,
                    'total_price' => $subtotal,
                ]);

                // Kurangi stok
                Product::find($product['product_id'])->decrement('stock', $product['quantity']);
            }

            $sale->update(['total_price' => $total]);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Penjualan berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan penjualan: ' . $e->getMessage());
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
        $products = Product::get();
        $categories = Category::get();
        $sale->load('saleItems.product');

        return view('admin.sales.edit', compact(
            'title',
            'sale',
            'products',
            'categories'
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
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Kembalikan stok lama
            foreach ($sale->saleItems as $item) {
                Product::find($item->product_id)->increment('stock', $item->quantity);
            }

            // Hapus item lama
            $sale->saleItems()->delete();

            // Update sale
            $sale->update([
                'payment_method' => $request->payment_method,
                'discount' => $request->discount ?? 0,
            ]);

            $total = 0;

            foreach ($request->products as $product) {
                $subtotal = ($product['unit_price'] * $product['quantity']) - ($product['discount'] ?? 0);
                $total += $subtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'unit_price' => $product['unit_price'],
                    'discount' => $product['discount'] ?? 0,
                    'total_price' => $subtotal,
                ]);

                // Kurangi stok baru
                Product::find($product['product_id'])->decrement('stock', $product['quantity']);
            }

            $sale->update(['total_price' => $total]);

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Penjualan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui penjualan: ' . $e->getMessage());
        }
    }


    /**
     * Generate sales reports index
     *
     * @return \Illuminate\Http\Response
     */
    public function reports(Request $request)
    {
        $title = 'sales reports';
        return view('admin.sales.reports', compact(
            'title'
        ));
    }

    /**
     * Generate sales report form post
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'from_date' => 'required',
            'to_date' => 'required',
        ]);
        $title = 'sales reports';
        $sales = Sale::whereBetween(DB::raw('DATE(created_at)'), array($request->from_date, $request->to_date))->get();
        return view('admin.sales.reports', compact(
            'sales',
            'title'
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
