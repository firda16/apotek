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
use App\Models\PurchaseItem;
use App\Models\SaleItem;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'dashboard';

        // Total pengeluaran hari ini (dari tabel purchase_items)
        $total_pengeluaran = \App\Models\PurchaseItem::whereDate('created_at', Carbon::today())->sum('total_price');

        // Total pendapatan hari ini (dari tabel sale_items)
        $total_pendapatan = \App\Models\SaleItem::whereDate('created_at', Carbon::today())->sum('total_price');

        // Total kategori & supplier
        $total_categories = Category::count();
        $total_suppliers = Supplier::count();

        // Total produk yang pernah dibeli dan dijual
        $total_pembelian_produk = \App\Models\PurchaseItem::count();
        $total_sales = \App\Models\SaleItem::count();

        // Total produk
        $total_products = Product::count();

        // Produk stok habis
        $out_of_stock_products = Product::where('stock', '<=', 0)->count();

        // Produk expired (dari purchase_items)
        $total_expired_products = \App\Models\PurchaseItem::whereDate('expiry_date', '<=', now())->count();

        // Penjualan hari ini
        $today_sales = \App\Models\SaleItem::whereDate('created_at', Carbon::today())->sum('total_price');

        // Data terbaru pembelian dan penjualan (10 terakhir)
        $latest_sales = \App\Models\SaleItem::with('product')
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->take(10)
            ->get();
        $latest_purchases = \App\Models\PurchaseItem::with('product')->whereDate('created_at', Carbon::today())->latest()->take(10)->get();

        // Stok produk yang tersedia
        $stok_produk = Product::where('stock', '>', 0)->count();

        // Pie Chart
        $pieChart = new \ConsoleTVs\Charts\Classes\Chartjs\Chart;
        $pieChart->labels(['Total Pembelian', 'Total Pemasok', 'Total Penjualan', 'Total Produk']);
        $pieChart->dataset('Data Summary', 'pie', [
            $total_pembelian_produk,
            $total_suppliers,
            $total_sales,
            $total_products
        ])->backgroundColor(['#FF6384', '#36A2EB', '#7bb13c', '#FFCE56']);

        return view('admin.dashboard', compact(
            'title',
            'pieChart',
            'total_expired_products',
            'latest_sales',
            'today_sales',
            'total_categories',
            'total_products',
            'out_of_stock_products',
            'latest_purchases',
            'total_pembelian_produk',
            'total_sales',
            'total_suppliers',
            'total_pendapatan',
            'total_pengeluaran',
            'stok_produk'
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
