<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use QCod\AppSettings\Setting\AppSettings;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    //    $products = Product::get();
        $query = Product::query()->with(['purchase.category', 'purchaseItems']);
        $products = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.products.index',compact(
            'products'
        ));
    }

    
    public function available(Request $request)
    {
       $allProducts = Product::with(['purchaseItems', 'category'])->get();

        $filtered = $allProducts->filter(function ($product) {
            return $product->purchaseItems->sum('qty') > 0;
        });

// manual paginate
$page = request()->get('page', 1);
$perPage = 10;
$offset = ($page - 1) * $perPage;
$paginated = new LengthAwarePaginator(
    $filtered->slice($offset, $perPage)->values(),
    $filtered->count(),
    $perPage,
    $page,
    ['path' => request()->url(), 'query' => request()->query()]
);

return view('admin.products.available', [
    'title' => 'available products',
    'products' => $paginated
]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'add product';
        $purchases = Purchase::get();
        $products = Product::get();
        return view('admin.products.create',compact(
            'title','purchases', 'products' // Pass the fetched products to the view
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
            'price'=>'required|min:1',
            'discount'=>'nullable',
            'description'=>'nullable|max:255',
        ]);
        $price = $request->price;
        if($request->discount >0){
           $price = $request->discount * $request->price;
        }
        Product::create([
            'purchase_id'=>$request->product,
            'price'=>$price,
            'discount'=>$request->discount,
            'description'=>$request->description,
        ]);
        $notification = notify("Product has been added");
        return redirect()->route('products.index')->with($notification);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \app\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $title = 'edit product';
        $purchases = Purchase::get();
        return view('admin.products.edit',compact(
            'title','product','purchases'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \app\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product'=>'required|max:200',
            'price'=>'required',
            'discount'=>'nullable',
            'description'=>'nullable|max:255',
        ]);

        $price = $request->price;
        if($request->discount >0){
           $price = $request->discount * $request->price;
        }
       $product->update([
            'purchase_id'=>$request->product,
            'price'=>$price,
            'discount'=>$request->discount,
            'description'=>$request->description,
        ]);
        $notification = notify('product has been updated');
        return redirect()->route('products.index')->with($notification);
    }

     /**
     * Display a listing of expired resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function expired(Request $request){
        $products = Product::whereHas('purchase', function ($q) {
            $q->whereDate('expiry_date', '<=', now());
        })->with('purchase.category')->get();

        return view('admin.products.expired', compact('products'));
    }

    /**
     * Display a listing of out of stock resources.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
     public function outstock(Request $request)
    {
        $title = "Outstocked Products";

        // Fetch products with quantity <= 0 directly
       $products = Product::with(['category', 'purchaseItems' => function ($q) {
            $q->where('quantity', '<=', 0);
        }])->paginate(10);


        return view('admin.products.outstock', compact(
            'title',
            'products' // Pass the fetched products to the view
        ));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        $notification = notify('Product has been deleted');
        return redirect()->route('products.index')->with($notification);
    }
}
