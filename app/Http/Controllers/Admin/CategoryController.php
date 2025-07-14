<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CategoryController extends Controller
{
     use ValidatesRequests;
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
   public function index(Request $request)
{
    $query = Category::query();

    if ($request->has('search') && $request->search != '') {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Mengganti get() dengan paginate() untuk mengaktifkan pagination
    $categories = $query->orderBy('created_at', 'desc')->paginate(15); 
    // Anda bisa menyesuaikan jumlah item per halaman (contoh: 15)

    // Cek jika request AJAX, kembalikan partial view
    if ($request->ajax()) {
        return view('admin.products.partials.categories-table', compact('categories'))->render();
    }

    return view('admin.products.categories', compact('categories'));
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
        'name' => 'required|max:100',
    ]);

    Category::create([
        'name' => $request->name,
    ]);

    return back()->with('success', 'Category has been added');
}





    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
{
    $request->validate([
        'name' => 'required|max:100',
    ]);

    $category = Category::findOrFail($request->id);
    $category->update([
        'name' => $request->name,
    ]);

    return back()->with('success', 'Category has been updated');
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        return Category::findOrFail($request->id)->delete();
    }
}
