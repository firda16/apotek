<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use Illuminate\Http\Request;
use App\Models\Customer;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// routes/api.php

// routes/web.php atau api.php
Route::get('/get-customer-phone', function (Request $request) {
    $nama = $request->nama;

    $customer = Customer::where('nama', $nama)
        ->select('telepon')
        ->first();

    if ($customer) {
        return response()->json([
            'telepon' => $customer->nomor_telepon
        ]);
    }

    return response()->json(['telepon' => null]);
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
