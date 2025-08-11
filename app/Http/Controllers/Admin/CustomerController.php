<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    // Tampilkan semua customer
    public function index()
    {
        $customers = Customer::all();
        return view('admin.customers.index', compact('customers'));
    }

    // Tampilkan form tambah customer
    public function create()
    {
        return view('admin.customers.create');
    }

    // Simpan data customer baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        Customer::create($request->all());

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    // Tampilkan detail customer
    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.show', compact('customer'));
    }

    // Tampilkan form edit customer
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    // Update data customer
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'telepon' => 'required|string|max:20',
            'email' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',

        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    // Hapus customer
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }

    public function getCustomerData()
    {
        $customers = Customer::select('nama', 'telepon')->get();
        return response()->json($customers);
    }

    public function datatable(Request $request)
    {
        $query = Customer::query();
        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $editUrl = route('customers.edit', $row->id);
                $deleteUrl = route('customers.destroy', $row->id);
                $csrf = csrf_field();
                $method = method_field('DELETE');
                return "
                    <a href='{$editUrl}' class='btn btn-sm btn-warning'>Edit</a>
                    <form action='{$deleteUrl}' method='POST' style='display:inline;' onsubmit='return confirm(\"Yakin hapus data?\")'>
                        {$csrf}{$method}
                        <button type='submit' class='btn btn-sm btn-danger'>Hapus</button>
                    </form>
                ";
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
