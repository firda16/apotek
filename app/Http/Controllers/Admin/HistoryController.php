<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Sale;

class HistoryController extends Controller
{
    public function index()
    {
        $title = 'Riwayat';

        $purchases = Purchase::orderBy('created_at', 'desc')->get();
        $sales = Sale::orderBy('created_at', 'desc')->get();

        return view('admin.history.index', compact('title', 'purchases', 'sales'));
    }
}
