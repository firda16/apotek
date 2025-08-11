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
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    // Hapus customer
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil dihapus.');
    }

    public function getCustomerData()
    {
        $customers = Customer::select('nama', 'telepon')->get();
        return response()->json($customers);
    }

}
