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
use App\Events\ProductExpired;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login dulu.');
        }

        $role = $user->role;


        if ($role === 'admin') {
            $title = 'dashboard';
            // $expiredProducts = PurchaseItem::whereDate('expiry_date', '<=', now())->get();

            // foreach ($expiredProducts as $expired) {
            //     event(new ProductExpired($expired));
            // }

            $total_pengeluaran_hari_ini = PurchaseItem::whereDate('created_at', Carbon::today())->sum('total_price');
            $total_pendapatan_hari_ini = Sale::whereDate('created_at', Carbon::today())->sum('total_price');

            $total_pengeluaran_bulan_ini = Purchase::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_price');

            $total_pendapatan_bulan_ini = Sale::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_price');

            $total_categories = Category::count();
            $total_suppliers = Supplier::count();
            $total_pembelian_produk = PurchaseItem::count();
            // $total_sales = SaleItem::whereHas('sale', function ($query) {
            //     $query->whereNull('deleted_at');
            // })->sum('quantity');
            $total_products = Product::count();
            $out_of_stock_products = Product::outOfStock()->count();

            $total_expired_products = PurchaseItem::whereDate('expiry_date', '<=', now())->count();
            // $total_expired_products = PurchaseItem::whereDate('expiry_date', '<=', now())
            //     ->whereRaw('(quantity - sold_quantity) > 0')
            //     ->count();

            // $total_expired_products = Product::whereHas('purchaseItems', function ($q) {
            //     $q->whereDate('expiry_date', '<=', now())
            //         ->whereColumn('quantity', '>', 'sold_quantity');
            // })->count();

            // Total produk terjual (default semua waktu)
            $filter_sales = request()->input('filter_sales', 'all'); // default 'all'

            if ($filter_sales === 'today') {
                $total_sales = SaleItem::whereHas('sale', function ($query) {
                    $query->whereDate('created_at', Carbon::today())
                        ->whereNull('deleted_at');
                })->sum('quantity');
            } elseif ($filter_sales === 'month') {
                $total_sales = SaleItem::whereHas('sale', function ($query) {
                    $query->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereNull('deleted_at');
                })->sum('quantity');
            } else {
                $total_sales = SaleItem::whereHas('sale', function ($query) {
                    $query->whereNull('deleted_at');
                })->sum('quantity');
            }




            $today_sales = SaleItem::whereDate('created_at', Carbon::today())->sum('total_price');

            $latest_sales = SaleItem::with('product')
                ->whereHas('sale', function ($query) {
                    $query->whereDate('created_at', Carbon::today());   // filter transaksi dari tabel sales
                })
                ->latest()
                ->take(10)
                ->get();


            $latest_purchases = PurchaseItem::with('product')
                ->whereDate('created_at', Carbon::today())
                ->latest()
                ->take(10)
                ->get();



            // $stok_produk = Product::where('stock', '>', 0)->count();
            $stok_produk = Product::with([
                'category',
                'purchaseItems' => function ($query) {
                    $query->whereColumn('quantity', '>', 'sold_quantity')
                        ->where(function ($q) {
                            $q->whereDate('expiry_date', '>', now())
                                ->orWhereNull('expiry_date');
                        });
                }
            ])
                ->whereHas('purchaseItems', function ($query) {
                    $query->whereColumn('quantity', '>', 'sold_quantity')
                        ->where(function ($q) {
                            $q->whereDate('expiry_date', '>', now())
                                ->orWhereNull('expiry_date');
                        });
                })
                ->count();

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
                'total_pengeluaran_bulan_ini',
                'filter_sales' // <-- tambahan
            ));
        }

        if ($role === 'kasir') {
            $title = 'kasir-dashboard';

            $total_pendapatan_hari_ini = Sale::whereDate('created_at', Carbon::today())->sum('total_price');

            $total_pendapatan_bulan_ini = Sale::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_price');

            // $total_sales = SaleItem::whereHas('sale', function ($query) {
            //     $query->whereNull('deleted_at');
            // })->sum('quantity');

            // Total produk terjual (default semua waktu)
            $filter_sales = request()->input('filter_sales', 'all'); // default 'all'

            if ($filter_sales === 'today') {
                $total_sales = SaleItem::whereHas('sale', function ($query) {
                    $query->whereDate('created_at', Carbon::today())
                        ->whereNull('deleted_at');
                })->sum('quantity');
            } elseif ($filter_sales === 'month') {
                $total_sales = SaleItem::whereHas('sale', function ($query) {
                    $query->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereNull('deleted_at');
                })->sum('quantity');
            } else {
                $total_sales = SaleItem::whereHas('sale', function ($query) {
                    $query->whereNull('deleted_at');
                })->sum('quantity');
            }

            $stok_produk = Product::whereHas('purchaseItems', function ($query) {
                $query->whereColumn('quantity', '>', 'sold_quantity')
                    ->where(function ($q) {
                        $q->whereDate('expiry_date', '>', now())
                            ->orWhereNull('expiry_date');
                    });
            })->count();
            $out_of_stock_products = Product::outOfStock()->count();

            $total_expired_products = PurchaseItem::whereDate('expiry_date', '<=', now())->count();

            $latest_sales = SaleItem::with('product')
                ->whereHas('sale', function ($query) {
                    $query->whereDate('created_at', Carbon::today());   // filter transaksi dari tabel sales
                })
                ->latest()
                ->take(10)
                ->get();

            $pieChart = new Chart;
            $pieChart->labels(['Total Produk', 'Stok Habis', 'Produk Kedaluwarsa']);
            $pieChart->dataset('Data Produk', 'pie', [
                $stok_produk,
                $out_of_stock_products,
                $total_expired_products
            ])->backgroundColor(['#36A2EB', '#FF6384', '#FFCE56']);

            return view('kasir.dashboard', compact(
                'title',
                'pieChart',
                'total_pendapatan_hari_ini',
                'total_pendapatan_bulan_ini',
                'total_sales',
                'stok_produk',
                'out_of_stock_products',
                'total_expired_products',
                'latest_sales',
                'filter_sales'
            ));
        }
    }
}
