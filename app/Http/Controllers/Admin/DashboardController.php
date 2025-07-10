<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'dashboard';

        $total_purchases = Purchase::where('expiry_date', '!=', Carbon::now())->count();
        $total_categories = Category::count();
        $total_suppliers = Supplier::count();
        $total_sales = Sale::count();

        // ✅ Buat chart dengan benar
        $pieChart = new Chart;
        $pieChart->labels(['Total Purchases', 'Total Suppliers', 'Total Sales']);
        $pieChart->dataset('Data Summary', 'pie', [
            $total_purchases,
            $total_suppliers,
            $total_sales
        ])->backgroundColor(['#FF6384', '#36A2EB', '#7bb13c']);

        $total_expired_products = Purchase::whereDate('expiry_date', '=', Carbon::now())->count();
        $latest_sales = Sale::whereDate('created_at', '=', Carbon::now())->get();
        $today_sales = Sale::whereDate('created_at', '=', Carbon::now())->sum('total_price');

        return view('admin.dashboard', compact(
            'title',
            'pieChart',
            'total_expired_products',
            'latest_sales',
            'today_sales',
            'total_categories'
        ));
    }
}
