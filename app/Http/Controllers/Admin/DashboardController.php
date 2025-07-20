<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use App\Models\Product;
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

        $total_purchases = Purchase::whereDate('created_at', Carbon::today())->sum('cost_price');

        $total_categories = Category::count();

        $total_pembelian_produk = Purchase::count();
        $total_suppliers = Supplier::count();
        $total_sales = Sale::count();
        $total_products = Product::count(); //total produk

        $pieChart = new Chart;
        $pieChart->labels(['Total Pembelian', 'Total Pemasok', 'Total Penjualan', 'Total Produk']);
        $pieChart->dataset('Data Summary', 'pie', [
            $total_pembelian_produk,
            $total_suppliers,
            $total_sales,
            $total_products
        ])->backgroundColor(['#FF6384', '#36A2EB', '#7bb13c', '#FFCE56']);


        //produk habis stok
        $out_of_stock_products = Product::whereHas('purchase', function ($q) {
            return $q->where('quantity', '<=', 0);
        })->count();
        // $total_expired_products = Purchase::whereDate('expiry_date', '=', Carbon::now())->count();
        $total_expired_products = Product::whereHas('purchase', function ($q) {
    $q->whereDate('expiry_date', '<=', now());
})->count();

        //jumlah uang hari ini
        $today_sales = Sale::whereDate('created_at', '=', Carbon::now())->sum('total_price');
        //tabel penjualan hari ini
        $latest_sales = Sale::whereDate('created_at', '=', Carbon::now())->get();
        //tabel pembelian hari ini
        $latest_purchases = Purchase::whereDate('created_at', '=', Carbon::now())->get();

        return view('admin.dashboard', compact(
            'title',
            'pieChart',
            'total_purchases',
            'total_expired_products',
            'latest_sales',
            'today_sales',
            'total_categories',
            'total_products',           // ← kirim ke view
            'out_of_stock_products',
            'latest_purchases',
            'total_pembelian_produk',
            'total_sales',
            'total_suppliers'
        ));
    }

    public function kasirDashboard()
    {
        $title = 'kasir-dashboard';

        $total_purchases = Purchase::where('expiry_date', '!=', Carbon::now())->count();
        $total_categories = Category::count();
        $total_suppliers = Supplier::count();
        $total_sales = Sale::count();

        $pieChart = new Chart;
        $pieChart->labels(['Total Purchases', 'Total Suppliers', 'Total Sales']);
        $pieChart->dataset('Data Summary', 'pie', [
            $total_purchases,
            $total_suppliers,
            $total_sales
        ])->backgroundColor(['#FF6384', '#36A2EB', '#7bb13c']);

        return view('kasir.dashboard', compact(
            'title',
            'pieChart',
            'total_categories'
        ));
    }

}
