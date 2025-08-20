<?php

use App\Models\Customer;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Kasir\SaleKasirController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Kasir\ProductKasirController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Kasir\CategoryKasirController;
use App\Http\Controllers\Kasir\CustomerKasirController;
use App\Http\Controllers\Kasir\NotificationKasirController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('', [DashboardController::class, 'Index']);

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::delete('/notifications/delete-all', [NotificationController::class, 'destroyAll'])
        ->name('notifications.destroyAll');


    // Khusus untuk kasir
// Dashboard kasir
// Route::get('dashboard-kasir', [DashboardController::class, 'kasirDashboard'])->name('kasir.dashboard');
// // Transaksi penjualan (form dan simpan)
// Route::get('/transaksi', [KasirController::class, 'transaksi'])->name('kasir.transaksi');
// Route::post('/transaksi', [KasirController::class, 'storeTransaksi'])->name('kasir.transaksi.store');
// // Laporan penjualan kasir
// Route::get('/laporan', [KasirController::class, 'laporan'])->name('kasir.laporan');
// Route::get('/kasir/laporan', [KasirController::class, 'laporan'])->name('kasir.laporan');

    Route::get('notification', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
    Route::get('notification/semua', [NotificationController::class, 'semua'])->name('show-all');
    Route::get('notification-read', [NotificationController::class, 'read'])->name('read');
    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::post('profile/{user}', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/update-password/{user}', [UserController::class, 'updatePassword'])->name('update-password');

    Route::resource('users', UserController::class);
    Route::post('/suppliers/check-phone', [SupplierController::class, 'checkPhone'])
        ->name('suppliers.checkPhone');

    Route::resource('suppliers', SupplierController::class);

    Route::get('categories/datatable', [CategoryController::class, 'datatable'])->name('categories.datatable');
    Route::resource('categories', CategoryController::class)->only(['index', 'edit', 'store', 'destroy']);
    // Route::put('categories',[CategoryController::class,'update'])->name('categories.update');
    Route::post('categories/update', [CategoryController::class, 'update'])->name('categories.update');


    Route::get('/check-invoice', [App\Http\Controllers\Admin\PurchaseController::class, 'checkInvoice'])
        ->name('check.invoice');
    Route::get('purchases/reports', [PurchaseController::class, 'reports'])->name('purchases.report');
    Route::get('purchases/reports/pdf', [PurchaseController::class, 'exportPdf'])
        ->name('purchases.reports.pdf');
    Route::get('/get-last-price', [PurchaseController::class, 'getLastPrice']);

    Route::post('purchases/reports', [PurchaseController::class, 'generateReport']);
    Route::get('purchases/datatable', [PurchaseController::class, 'datatable'])->name('purchases.datatable');
    Route::resource('purchases', PurchaseController::class);


    Route::get('products/datatable', [ProductController::class, 'datatable'])->name('products.datatable');
    Route::resource('products', ProductController::class)->except('show');
    Route::get('/products/{product}/stock-log', [ProductController::class, 'stockLog'])->name('products.stock-log');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    // Route::get('products/available', [ProductController::class, 'available'])->name('available');
    Route::get('products/available', [ProductController::class, 'available'])->name('products.available');
    Route::get('products/outstock', [ProductController::class, 'outstock'])->name('outstock');
    Route::get('/products/outstock/datatable', [ProductController::class, 'outstockDatatable'])
        ->name('outstock.datatable');
    Route::get('products/expired', [ProductController::class, 'expired'])->name('expired');
    Route::get('/products/expired/datatable', [ProductController::class, 'expiredDatatable'])
        ->name('products.expired.datatable');

    Route::post('/products/delete-expired', [ProductController::class, 'deleteExpired'])
        ->name('products.deleteExpired');
    Route::get('/products/stock-report', [ProductController::class, 'stockReport'])->name('reports.stock');
    Route::get('/reports/stock/pdf', [ProductController::class, 'stockReportPdf'])->name('reports.stock.pdf');



    Route::get('customers/datatable', [CustomerController::class, 'datatable'])->name('customers.datatable');
    Route::resource('customers', CustomerController::class);

    Route::get('customer-by-name', function (Illuminate\Http\Request $request) {
        return Customer::select('id', 'nama', 'telepon')
            ->where('nama', $request->nama)
            ->where('telepon', $request->telepon)
            ->first();
    });

    Route::resource('sales', SaleController::class)->except('show');
    Route::get('sales/{sale}/invoice', [SaleController::class, 'printInvoice'])->name('sales.invoice');
    Route::get('sales/data', [SaleController::class, 'getData'])->name('sales.data');
    Route::get('sales/reports', [SaleController::class, 'reports'])->name('sales.report');
    Route::post('sales/reports', [SaleController::class, 'generateReport']);
    Route::get('sales/reports/pdf', [SaleController::class, 'exportPdf'])->name('sales.reports.pdf');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

    // Route::get('history', [HistoryController::class,'index'])->name('history.index');
    Route::get('/admin/riwayat/penjualan', [HistoryController::class, 'penjualan'])->name('riwayat.penjualan');
    Route::get('/riwayat-penjualan', [HistoryController::class, 'penjualan'])->name('riwayat.penjualan');
    Route::get('/riwayat-penjualan/{invoice_number}', [HistoryController::class, 'show'])
        ->name('riwayat.penjualan.show');
    Route::get('/admin/riwayat/pembelian', [HistoryController::class, 'pembelian'])->name('riwayat.pembelian');
    Route::get('/riwayat-pembelian', [HistoryController::class, 'pembelian'])->name('riwayat.pembelian');
    Route::get('riwayat/pembelian/pdf', [HistoryController::class, 'cetakPembelianPDF'])->name('riwayat.pembelian.pdf');
    Route::get('riwayat/penjualan/pdf', [HistoryController::class, 'cetakPenjualanPDF'])->name('riwayat.penjualan.pdf');

    Route::put('backup/create', [HistoryController::class, 'create'])->name('backup.store');
    Route::get('backup/download/{file_name?}', [HistoryController::class, 'download'])->name('backup.download');
    Route::delete('backup/delete/{file_name?}', [HistoryController::class, 'destroy'])->where('file_name', '(.*)')->name('backup.destroy');

    // Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');


    Route::get('customer-autocomplete', function (Illuminate\Http\Request $request) {
        $term = $request->term;
        $customers = Customer::where('nama', 'like', "%$term%")
            ->select('id', 'nama', 'telepon')
            ->get();
        return response()->json($customers);
    });
    Route::get('customer-check-phone', function (\Illuminate\Http\Request $request) {
        $phone = $request->phone;
        $name = $request->name;
        $customer = Customer::where('telepon', $phone)->first();
        if ($customer && strtolower(trim($customer->nama)) !== strtolower(trim($name))) {
            return response()->json([
                'exists' => true,
                'real_name' => $customer->nama
            ]);
        }
        return response()->json(['exists' => false]);
    });
    Route::post('products/delete-expired', [ProductController::class, 'deleteExpired'])->name('products.deleteExpired');

});

// --- GRUP ROUTE KASIR ---
// PERBAIKAN: Menambahkan prefix dan name pada grup
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'kasirDashboard'])->name('dashboard');

    // category
    Route::get('categories', [CategoryKasirController::class, 'index'])->name('categories.index');
    Route::get('categories/datatable', [CategoryKasirController::class, 'datatable'])->name('categories.datatable');
    // products
    
    Route::get('/produk/expired/datatable', [ProductKasirController::class, 'expiredDatatable'])
        ->name('products.kadaluarsa.datatable');
    Route::get('produk', [ProductKasirController::class, 'index'])->name('products.index');
    Route::get('produk/datatable', [ProductKasirController::class, 'datatable'])->name('products.datatable');
    Route::get('produk/tersedia', [ProductKasirController::class, 'available'])->name('products.available');
    Route::get('produk/kadaluarsa', [ProductKasirController::class, 'expired'])->name('products.expired');
    Route::get('produk/stok-habis', [ProductKasirController::class, 'outstock'])->name('products.outstock');
    Route::get('/products/outstock/datatable', [ProductKasirController::class, 'outstockDatatable'])->name('products.outstock.datatable');

    // transaksi
    Route::get('/transaksi', [SaleKasirController::class, 'index'])->name('transaksi');
    Route::post('/transaksi', [SaleKasirController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/create', [SaleKasirController::class, 'create'])->name('transaksi.create');
    Route::get('/transaksi/{sale}/edit', [SaleKasirController::class, 'edit'])->name('transaksi.edit');
    Route::put('/transaksi/{sale}', [SaleKasirController::class, 'update'])->name('transaksi.update');
    Route::get('transaksi/{sale}/invoice', [SaleKasirController::class, 'printInvoice'])->name('transaksi.invoice');
    Route::delete('transaksi/{sale}', [SaleKasirController::class, 'destroy'])->name('transaksi.destroy');


    // customers
    Route::get('customers', [CustomerKasirController::class, 'index'])->name('customers');
    Route::get('customers/datatable', [CustomerKasirController::class, 'datatable'])->name('customers.datatable');
    Route::get('customer-autocomplete-kasir', function (Illuminate\Http\Request $request) {
        $term = $request->term;
        $customers = Customer::where('nama', 'like', "%$term%")
            ->select('id', 'nama', 'telepon')
            ->get();
        return response()->json($customers);
    });
    Route::get('customer-check-phone-kasir', function (\Illuminate\Http\Request $request) {
        $phone = $request->phone;
        $name = $request->name;
        $customer = Customer::where('telepon', $phone)->first();
        if ($customer && strtolower(trim($customer->nama)) !== strtolower(trim($name))) {
            return response()->json([
                'exists' => true,
                'real_name' => $customer->nama
            ]);
        }
        return response()->json(['exists' => false]);
    });

    // Riwayat Penjualan Kasir
    Route::get('riwayat-penjualan', [HistoryController::class, 'penjualan'])->name('riwayat.penjualan');
    Route::get('riwayat-penjualan/pdf', [HistoryController::class, 'cetakPenjualanPDF'])->name('riwayat.penjualan.pdf');
    Route::get('riwayat-penjualan/{invoice_number}', [HistoryController::class, 'show'])->name('riwayat.penjualan.show');


    // Laporan Penjualan Kasir
    Route::get('sales/{sale}/invoice', [SaleController::class, 'printInvoice'])->name('sales.invoice');
    Route::get('sales/data', [SaleController::class, 'getData'])->name('sales.data');
    Route::get('sales/reports', [SaleController::class, 'reports'])->name('sales.report');
    Route::post('sales/reports', [SaleController::class, 'generateReport']);
    Route::get('/reports/pdf', [SaleKasirController::class, 'exportPdf'])->name('sales.reports.pdf');

    //notifikasi
    Route::get('notifikasi-baca', [NotificationKasirController::class, 'baca'])->name('baca');
    Route::get('notifikasi', [NotificationKasirController::class, 'tandai'])->name('tandai');
    Route::get('notifikasi/semua', [NotificationKasirController::class, 'semua'])->name('notifikasi-semua');
    Route::delete('/notifikasi/hapus-semua', [NotificationKasirController::class, 'destroyAll'])
        ->name('notifications.destroyAll');

    //pdf
    // routes/web.php
    Route::get('panduan', function () {
        $title = 'Panduan Buku';
        $pdfPath = asset('assets/PANDUAN KASIR_ APOTEK.pdf');
        return view('kasir.panduan', compact('title', 'pdfPath'));
    })->name('panduan');

    // Autocomplete pelanggan kasir
    Route::get('customer-autocomplete', function (Illuminate\Http\Request $request) {
        $term = $request->term;
        $customers = \App\Models\Customer::where('nama', 'like', "%$term%")
            ->orWhere('telepon', 'like', "%$term%")
            ->select('id', 'nama', 'telepon')
            ->get();
        return response()->json($customers);
    });

    // Cek nomor telepon pelanggan kasir
    Route::get('customer-check-phone', function (Illuminate\Http\Request $request) {
        $phone = $request->phone;
        $name = $request->name;
        $customer = \App\Models\Customer::where('telepon', $phone)->first();
        if ($customer && strtolower(trim($customer->nama)) !== strtolower(trim($name))) {
            return response()->json([
                'exists' => true,
                'real_name' => $customer->nama
            ]);
        }
        return response()->json(['exists' => false]);
    });



});


Route::middleware(['guest'])->group(function () {
    Route::get('', function () {
        return redirect()->route('dashboard');
    });

    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('register', [RegisterController::class, 'index'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'index'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'requestEmail']);
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'index'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');


});


Route::middleware(['auth'])->group(function () {
    Route::post('logout', [LogoutController::class, 'index'])->name('logout');
});






