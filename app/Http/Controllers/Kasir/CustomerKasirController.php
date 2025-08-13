<?php

namespace App\Http\Controllers\Kasir;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerKasirController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('kasir.customers.index', compact('customers'));
    }

    public function datatable(Request $request)
    {
        $query = Customer::query();
        return datatables()->of($query)
            ->addIndexColumn()
            // ->addColumn('action', function($row){
            //     $editUrl = route('kasir.customers.edit', $row->id);
            //     $deleteUrl = route('kasir.customers.destroy', $row->id);
            //     $csrf = csrf_field();
            //     $method = method_field('DELETE');
            //     return "
            //         <a href='{$editUrl}' class='btn btn-sm btn-warning'>Edit</a>
            //         <form action='{$deleteUrl}' method='POST' style='display:inline;' onsubmit='return confirm(\"Yakin hapus data?\")'>
            //             {$csrf}{$method}
            //             <button type='submit' class='btn btn-sm btn-danger'>Hapus</button>
            //         </form>
            //     ";
            // })
            // ->rawColumns(['action'])
            ->make(true);
    }
}
