<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Validation\ValidatesRequests;

class LoginController extends Controller
{
    use ValidatesRequests;

    public function index()
    {
        $title = 'login';
        return view('admin.auth.login', compact('title'));
    }

    public function login(Request $request)
{
    $this->validate($request, [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $authenticated = Auth::attempt($request->only('email', 'password'));

    if (!$authenticated) {
        return back()->with('login_error', "Invalid user credentials");
    }

    // Dapatkan instansi pengguna yang berhasil login
    $user = Auth::user();

    // Cek peran (role) pengguna
    if ($user->role === 'admin') {
        // Jika peran adalah 'admin', arahkan ke dashboard admin
        return redirect()->route('dashboard');
    } elseif ($user->role === 'kasir') {
        // Jika peran adalah 'kasir', arahkan ke halaman kasir/dashboard
        // Asumsikan Anda memiliki route bernama 'kasir.dashboard' atau path '/kasir/dashboard'
        // return redirect()->route('kasir.dashboard');
        return redirect()->route('kasir.dashboard');

        // Atau jika Anda menggunakan URL langsung:
        // return redirect('/kasir/dashboard');
    }

    // Jika peran tidak dikenali (opsional), arahkan ke dashboard default
    return redirect()->route('kasir.dashboard');
}

}
