<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CategoryController extends Controller
{
    use ValidatesRequests;



    public function datatable(Request $request)
    {
        $query = Category::query();
        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $editBtn = "<a href='javascript:void(0)' data-id='{$row->id}' data-name='{$row->name}' class='editbtn'><button class='btn btn-primary'><i class='fas fa-edit'></i></button></a>";
                $deleteBtn = "<a data-id='{$row->id}' data-route='" . route('categories.destroy', $row->id) . "' href='javascript:void(0)' id='deletebtn'><button class='btn btn-danger'><i class='fas fa-trash'></i></button></a>";
                return $editBtn . ' ' . $deleteBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('created_at', 'desc')->paginate(15);

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
            'name' => 'required|max:100|unique:categories,name',
        ], [
            'name.unique' => 'Kategori sudah ada.',
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Category has been added');
    }


    public function edit(Request $request)
    {
        $category = Category::findOrFail($request->id);
        return view('admin.products.partials.edit-category', compact('category'));
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
            'name' => 'required|max:100|unique:categories,name,' . $request->id,
        ], [
            'name.unique' => 'Kategori sudah ada.',
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        $category = Category::findOrFail($request->id);
        $category->update([
            'name' => $request->name,
        ]);

        return back()->with('edit_success', 'Kategori berhasil diubah');
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
