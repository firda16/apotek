<?php

namespace App\Http\Controllers\Kasir;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

class CategoryKasirController extends Controller
{
    use ValidatesRequests;
    public function datatable(Request $request)
    {
        $query = Category::query();
        return datatables()->of($query)
            ->addIndexColumn()           
            ->make(true);
    }

    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('kasir.products.categories', compact('categories'));
    }


}
