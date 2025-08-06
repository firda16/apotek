<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\Auth\RegisterController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\Admin\CustomerController;


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
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // Khusus untuk kasir
// Dashboard kasir
// Route::get('dashboard-kasir', [DashboardController::class, 'kasirDashboard'])->name('kasir.dashboard');
// // Transaksi penjualan (form dan simpan)
// Route::get('/transaksi', [KasirController::class, 'transaksi'])->name('kasir.transaksi');
// Route::post('/transaksi', [KasirController::class, 'storeTransaksi'])->name('kasir.transaksi.store');
// // Laporan penjualan kasir
// Route::get('/laporan', [KasirController::class, 'laporan'])->name('kasir.laporan');
// Route::get('/kasir/laporan', [KasirController::class, 'laporan'])->name('kasir.laporan');

    Route::get('',[DashboardController::class,'Index']);
    Route::get('notification',[NotificationController::class,'markAsRead'])->name('mark-as-read');
    Route::get('notification-read',[NotificationController::class,'read'])->name('read');
    Route::get('profile',[UserController::class,'profile'])->name('profile');
    Route::post('profile/{user}',[UserController::class,'updateProfile'])->name('profile.update');
    Route::put('profile/update-password/{user}',[UserController::class,'updatePassword'])->name('update-password');

    Route::resource('users',UserController::class);
    Route::resource('suppliers',SupplierController::class);
    Route::resource('categories',CategoryController::class)->only(['index','edit','store','destroy']);
    // Route::put('categories',[CategoryController::class,'update'])->name('categories.update');
    Route::post('categories/update', [CategoryController::class, 'update'])->name('categories.update');



    Route::get('purchases/reports',[PurchaseController::class,'reports'])->name('purchases.report');
    
    Route::post('purchases/reports',[PurchaseController::class,'generateReport']);
    Route::get('purchases/datatable', [PurchaseController::class, 'datatable'])->name('purchases.datatable');
    Route::resource('purchases', PurchaseController::class);

    Route::resource('products',ProductController::class)->except('show');
    Route::get('/products/{product}/stock-log', [ProductController::class, 'stockLog'])->name('products.stock-log');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    // Route::get('products/available', [ProductController::class, 'available'])->name('available');
    Route::get('products/available', [ProductController::class, 'available'])->name('products.available');
    Route::get('products/outstock',[ProductController::class,'outstock'])->name('outstock');
    Route::get('products/expired',[ProductController::class,'expired'])->name('expired');
    Route::resource('sales',SaleController::class)->except('show');
    Route::get('sales/{sale}/invoice', [SaleController::class, 'printInvoice'])->name('sales.invoice');
    Route::get('sales/data', [SaleController::class, 'getData'])->name('sales.data');
    Route::get('sales/reports',[SaleController::class,'reports'])->name('sales.report');
    Route::post('sales/reports',[SaleController::class,'generateReport']);
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

      // Route::get('history', [HistoryController::class,'index'])->name('history.index');
    Route::get('/admin/riwayat/penjualan', [HistoryController::class, 'penjualan'])->name('riwayat.penjualan');
    Route::get('/riwayat-penjualan', [HistoryController::class, 'penjualan'])->name('riwayat.penjualan');
    Route::get('/riwayat-penjualan/{invoice_number}', [HistoryController::class, 'show'])
    ->name('riwayat.penjualan.show');
    Route::get('/admin/riwayat/pembelian', [HistoryController::class, 'pembelian'])->name('riwayat.pembelian');
    Route::get('/riwayat-pembelian', [HistoryController::class, 'pembelian'])->name('riwayat.pembelian');
    Route::put('backup/create', [HistoryController::class,'create'])->name('backup.store');
    Route::get('backup/download/{file_name?}', [HistoryController::class,'download'])->name('backup.download');
    Route::delete('backup/delete/{file_name?}', [HistoryController::class,'destroy'])->where('file_name', '(.*)')->name('backup.destroy');

    Route::get('settings',[SettingController::class,'index'])->name('settings');
});

Route::middleware(['guest'])->group(function () {
    Route::get('',function(){
        return redirect()->route('dashboard');
    });

    Route::get('login',[LoginController::class,'index'])->name('login');
    Route::post('login',[LoginController::class,'login']);

    Route::get('register',[RegisterController::class,'index'])->name('register');
    Route::post('register',[RegisterController::class,'store']);

    Route::get('forgot-password',[ForgotPasswordController::class,'index'])->name('password.request');
    Route::post('forgot-password',[ForgotPasswordController::class,'requestEmail']);
    Route::get('reset-password/{token}',[ResetPasswordController::class,'index'])->name('password.reset');
    Route::post('reset-password',[ResetPasswordController::class,'resetPassword'])->name('password.update');
});


 Route::middleware(['auth'])->group(function () {
    Route::post('logout',[LogoutController::class,'index'])->name('logout');
});


Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('dashboard-kasir', [DashboardController::class, 'kasirDashboard'])->name('kasir.dashboard');
    Route::get('/transaksi', [KasirController::class, 'transaksi'])->name('kasir.transaksi');
    Route::post('/transaksi', [KasirController::class, 'storeTransaksi'])->name('kasir.transaksi.store');
    Route::get('/laporan', [KasirController::class, 'laporan'])->name('kasir.laporan');
    Route::get('/laporan/{id}', [KasirController::class, 'show'])->name('kasir.laporan.show');
});


