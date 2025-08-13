<?php

namespace App\Http\Controllers\Admin;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;

        if ($role === 'admin') {
            $title = 'dashboard';

            $total_pengeluaran_hari_ini = PurchaseItem::whereDate('created_at', Carbon::today())->sum('total_price');
            $total_pendapatan_hari_ini  = SaleItem::whereDate('created_at', Carbon::today())->sum('total_price');

            $total_pengeluaran_bulan_ini = PurchaseItem::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_price');

            $total_pendapatan_bulan_ini = SaleItem::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_price');

            $total_categories = Category::count();
            $total_suppliers  = Supplier::count();
            $total_pembelian_produk = PurchaseItem::count();
            $total_sales      = SaleItem::count();
            $total_products   = Product::count();
            $out_of_stock_products = Product::where('stock', '<=', 0)->count();
            $total_expired_products = PurchaseItem::whereDate('expiry_date', '<=', now())->count();
            $today_sales      = SaleItem::whereDate('created_at', Carbon::today())->sum('total_price');

            $latest_sales = SaleItem::with('product')
                ->whereDate('created_at', Carbon::today())
                ->latest()
                ->take(10)
                ->get();

            $latest_purchases = PurchaseItem::with('product')
                ->whereDate('created_at', Carbon::today())
                ->latest()
                ->take(10)
                ->get();

            $stok_produk = Product::where('stock', '>', 0)->count();

            $pieChart = new Chart;
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
                'stok_produk',
                'total_pendapatan_hari_ini',
                'total_pengeluaran_hari_ini',
                'total_pendapatan_bulan_ini',
                'total_pengeluaran_bulan_ini'
            ));
        }

        if ($role === 'kasir') {
            $title = 'kasir-dashboard';

            $total_purchases = PurchaseItem::where('expiry_date', '!=', Carbon::now())->count();
            $total_categories = Category::count();
            $total_suppliers  = Supplier::count();
            $total_sales      = Sale::count();

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

        abort(403, 'Role tidak dikenali');
    }
}
