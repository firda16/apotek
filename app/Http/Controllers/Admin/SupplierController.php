<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Supplier::query();
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', '%' . $searchTerm . '%')
                ->orWhere('company', 'like', '%' . $searchTerm . '%')
                ->orWhere('email', 'like', '%' . $searchTerm . '%')
                ->orWhere('phone', 'like', '%' . $searchTerm . '%');
        }

        $suppliers = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.suppliers.index',compact(
            'suppliers'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'create supplier';
        return view('admin.suppliers.create',compact(
            'title'
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
            'name'=>'required|min:3|max:255',
            'email'=>'nullable|email|string',
            'phone'=>'nullable|min:10|max:20',
            'company'=>'nullable|max:200|required',
            'address'=>'nullable|required|max:200',
          
        ]);
        Supplier::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'company'=>$request->company,
            'address'=>$request->address,

        ]);
        $notification = notify("Pemasok berhasil di tambah");
        return redirect()->route('suppliers.index')->with($notification);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \app\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        $title = 'edit supplier';
        return view('admin.suppliers.edit',compact(
            'title','supplier'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \app\Models\Supplier $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'=>'required|min:3|max:255',
            'email'=>'nullable|email|string',
            'phone'=>'nullable|min:10|max:20',
            'company'=>'nullable|max:200|required',
            'address'=>'nullable|required|max:200',

        ]);
        $supplier->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'company'=>$request->company,
            'address'=>$request->address,

        ]);
        $notification = notify("Supplier berhasil diubah");
        return redirect()->route('suppliers.index')->with($notification);
    }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  \Illuminate\Http\Request $request
    //  * @return \Illuminate\Http\Response
    //  */
    public function destroy(Supplier $supplier)
{
    $supplier->delete();

    return redirect()->route('suppliers.index')->with("Supplier berhasil dihapus");
}

}
