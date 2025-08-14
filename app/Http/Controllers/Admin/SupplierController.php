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
    // app/Http/Controllers/SupplierController.php

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Supplier::latest()->get(); // Mengambil semua data supplier
            return Datatables::of($data)
                ->addIndexColumn() // Menambahkan kolom nomor urut (DT_RowIndex)
                ->addColumn('action', function ($row) {
                    // Membuat HTML untuk tombol Edit dan Hapus
                    $btn = '<a href="' . route('suppliers.edit', $row->id) . '" class="btn btn-sm bg-success-light"><i class="fe fe-pencil"></i> Edit</a> ';
                    $btn .= '<form action="' . route('suppliers.destroy', $row->id) . '" method="POST" style="display:inline;">';
                    $btn .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
                    $btn .= '<input type="hidden" name="_method" value="DELETE">';
                    $btn .= '<button type="submit" class="btn btn-sm bg-danger-light deletebtn" onclick="return confirm(\'Yakin ingin menghapus pemasok ini?\')"><i class="fe fe-trash"></i> Hapus</button>';
                    $btn .= '</form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        // Jika bukan request AJAX, tampilkan view seperti biasa
        return view('admin.suppliers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'create supplier';
        return view('admin.suppliers.create', compact(
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
            'name' => 'required|min:3|max:255',
            'email' => 'nullable|email|string',
            'phone' => 'nullable|min:10|max:20',
            'company' => 'nullable|max:200',
            'address' => 'nullable|max:200',

        ]);
        Supplier::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'address' => $request->address,

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
        return view('admin.suppliers.edit', compact(
            'title',
            'supplier'
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
            'name' => 'required|min:3|max:255',
            'email' => 'nullable|email|string',
            'phone' => 'nullable|min:10|max:20',
            'company' => 'nullable|max:200',
            'address' => 'nullable|max:200',

        ]);
        $supplier->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'address' => $request->address,

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
//     public function destroy(Supplier $supplier)
// {
//     $supplier->delete();

    //     return redirect()->route('suppliers.index')->with("Supplier berhasil dihapus");
// }
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('suppliers.index')->with("Supplier berhasil dihapus");
    }

}
